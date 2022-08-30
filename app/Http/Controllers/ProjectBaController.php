<?php

namespace App\Http\Controllers;

use App\Events\SendStatusUpdate;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;

class ProjectBaController extends Controller
{
    function getAllProjectsBaDataTable(Request $request)
    {
        $emp_id = JWTAuth::toUser()->getId();
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $allRecords = $this->projectBaRepo->countAllProjectsBa($emp_id);
        $filterRecords = $this->projectBaRepo->getFilterRecords($search,$emp_id);
        $data = $this->projectBaRepo->getAllProjectsBaDataTable($order, $column_name, $search, $start, $length, $emp_id);

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    function saveBaProject(Request $request)
    {
        $data = $request->all();
        $notify = [];
        $ba_project_id = $data['id'];
        $data['id'] = $this->projectBaRepo->ProjectOfId((int)$data['id']);
        $data['est_time'] = (int)$data['est_time'];
        $assigned_ba = $data['jr_ba'];
        if ($data['est_time'] > 0 && $data['est_time_changed']) {
            $data['flag'] = $this->optionRepo->OptionOfId(123);
        }

        $updateBaProject = $this->projectBaRepo->update($data['id'], $data);
        $from_emp = $updateBaProject->getEmpId()->getFirstname() . ' ' . $updateBaProject->getEmpId()->getLastname();

        if ($updateBaProject) {
            if ($assigned_ba) {
                $allJrBaOfProject = $this->projectJrBaRepo->getAllJrBaOfBaProject($ba_project_id);
                foreach ($allJrBaOfProject as $row) {
                    $isFound = false;
                    foreach ($assigned_ba as $ba) {
                        if ($ba == $row['emp_id']) {
                            $isFound = true;
                        }
                    }
                    if (!$isFound) {
                        // delete that ba project row
                        $baProjectOfId = $this->projectJrBaRepo->ProjectJrBaOfId($row['id']);
                        $this->projectJrBaRepo->delete($baProjectOfId);
                        // end

                        // delete conversation id
                        $conv_id = $this->projectConversationRepo->getJrConvId($data['proj_id'], $ba_project_id, $row['id']);
                        if (!empty($conv_id)) {
                            $c_id = $conv_id[0]['id'];
                            $c_id = $this->projectConversationRepo->ConvOfId($c_id);
                            $this->projectConversationRepo->delete($c_id);
                        }
                        // end
                    }
                }

                foreach ($assigned_ba as $ba) {
                    $exists = false;
                    foreach ($allJrBaOfProject as $row) {
                        if ($ba == $row['emp_id']) {
                            $exists = true;
                        }
                    }
                    if (!$exists) {
                        $jrData['project_id'] = $this->projectRepo->ProjectOfId($data['proj_id']);
                        $jrData['emp_id'] = $this->userRepo->UserOfId($ba);
                        $jrData['flag'] = $this->optionRepo->OptionOfId(122);
                        $jrData['ba_project_id'] = $data['id'];
                        $jrData['sr_to_jr_flag'] = $this->optionRepo->OptionOfId(126);
                        $prepareData = $this->projectJrBaRepo->prepareData($jrData);
                        $create = $this->projectJrBaRepo->create($prepareData);

                        $conv_data['project_id'] = $this->projectRepo->ProjectOfId($data['proj_id']);
                        $conv_data['project_ba_id'] = $this->projectBaRepo->ProjectOfId($data['id']);
                        $conv_data['project_jr_ba_id'] = $this->projectJrBaRepo->ProjectJrBaOfId($create->getId());
                        $prepareData = $this->projectConversationRepo->prepareData($conv_data);
                        $create = $this->projectConversationRepo->create($prepareData);
                        if ($create) {
                            $notify[] = [
                                'channel_name' => 'project-update-' . $ba,
                                'from_emp' => $from_emp,
                                'title' => 'Project Assigned',
                                'details' => $from_emp . ' assigned you project ' . $updateBaProject->getProjectId()->getProjectName()
                            ];
                        }
                    }
                }
            }
            if ($data['est_time_changed'] && $data['est_time'] > 0) {
                $project_id = $this->projectRepo->ProjectOfId($data['proj_id']);
                $project_data['status_flag'] = $this->optionRepo->OptionOfId(124);
                $update = $this->projectRepo->update($project_id, $project_data);
                if ($update) {
                    $notify[] = [
                        'channel_name' => 'project-update-' . $updateBaProject->getProjectId()->getCreatedBy()->getId(),
                        'from_emp' => $from_emp,
                        'title' => 'Project Estimation',
                        'details' => $from_emp . ' sent estimated hours on ' . $updateBaProject->getProjectId()->getProjectName()
                    ];
                }
            }
            return response()->json(['status' => 'updated', 'data' => $notify]);
        }
        return response()->json(['status' => 'error']);
    }

    function getBaProjectById(Request $request)
    {
        $id = $request->id;
        $data = $this->projectBaRepo->getBaProjectById($id);

        $response['client_email'] = $data['project_id']['client_email'];
        $response['client_name'] = $data['project_id']['client_name'];
        $response['project_id'] = $data['project_id']['id'];
        $response['project_description'] = $data['project_id']['project_description'];
        $response['project_doc'] = $data['project_id']['project_doc'];
        $response['project_name'] = $data['project_id']['project_name'];
        $response['skype_contact'] = $data['project_id']['skype_contact'];

        $response['id'] = $data['id'];
        $response['est_time'] = $data['est_time'];

        $jrBAs = [];
        foreach ($data['project_jr_ba'] as $value) {
            $jrBAs[] = [
                'id' => $value['emp_id']['id'],
                'firstname' => $value['emp_id']['firstname'],
                'lastname' => $value['emp_id']['lastname'],
                'project_jr_ba_id' => $value['id']
            ];
        }
        $response['jr_ba'] = $jrBAs;

        $jrConvs = [];
        $srConvs = [];


        $sr_conv_id = $this->projectConversationRepo->getSrConvId($response['project_id'], $response['id']);

        if (!empty($sr_conv_id)) {
            $sr_id = $sr_conv_id[0]['id'];
            $sr_comments = $this->projectCommentsRepo->getProjectComments($sr_id);
            $srConvs = ['conv_id' => $sr_id, 'comments' => $sr_comments];
        }

        foreach ($jrBAs as $jr) {
            $jr_conv_id = $this->projectConversationRepo->getJrConvId($response['project_id'], $response['id'], $jr['project_jr_ba_id']);
            if (!empty($jr_conv_id)) {
                $jr_id = $jr_conv_id[0]['id'];
                $jr_comments = $this->projectCommentsRepo->getProjectComments($jr_id);
                $jrConvs[] = ['conv_id' => $jr_id, 'comments' => $jr_comments, 'project_jr_ba_id' => $jr['project_jr_ba_id']];
            }
        }

        $response['srConvs'] = $srConvs;
        $response['jrConvs'] = $jrConvs;
        return response()->json($response);
    }
    function getAllJBaSelfBA()
    {
        $data = $this->projectBaRepo->getAllJBaSelfBA();
        return response()->json($data);
    }

    function getAllEmpTimingRecords(Request $request)
    {
        $data = $this->projectBaRepo->getAllEmpTimingRecords($request->project_id);
        $total = $this->projectBaRepo->getAllEmpTimingRecordsWithTotal($request->project_id);
        $max_date = Carbon::parse($total[0]['max_date']);
        $min_date = Carbon::parse($total[0]['min_date']);
        $total['diff_date'] = $max_date->diffInDays($min_date);
        $total['total'] = (int)$total[0]['total'];
        $res['data'] = $data;
        $res['extra'] = $total;
        return response()->json($res);
    }
}

<?php

namespace App\Http\Controllers;

use App\Entities\Permission;
use App\Events\SendStatusUpdate;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProjectController extends Controller
{
    function saveProject(Request $request)
    {
        $notify = [];
        $data = $request->all();
        $assigned_emp = $data['assigned_to'];
        $data['assigned_to'] = $this->userRepo->UserOfId($data['assigned_to']);
        $data['created_by'] = $this->userRepo->UserOfId($data['created_by']);
        if ($data['status_flag']) {
            $data['status_flag'] = $this->optionRepo->OptionOfId($data['status_flag']);
        }

        if ($data['id']) {
            $project_id = $this->projectRepo->ProjectOfId($data['id']);
            $updateProject = $this->projectRepo->update($project_id, $data);
            if ($updateProject) {
                // check if project with ba exists
                $res = $this->projectBaRepo->checkUserProjectExists($data['id']);
                if (!empty($res)) {
                    if ($data['reSubmitted']) {
                        $flag = 135;
                        $project_ba['flag'] = $this->optionRepo->OptionOfId(135);
                    } else {
                        $flag = 134;
                        $project_ba['flag'] = $this->optionRepo->OptionOfId(134);
                    }
                    // $project_ba['flag'] = $this->optionRepo->OptionOfId(122);
                    $project_ba_id = $this->projectBaRepo->ProjectOfId($res[0]['id']);
                    $update = $this->projectBaRepo->update($project_ba_id, $project_ba);
                    if (count($res[0][0]['project_jr_ba']) > 0) {
                        $jrData = $res[0][0]['project_jr_ba'];
                        foreach ($jrData as $row) {
                            $toUpdate['id'] = $this->projectJrBaRepo->ProjectJrBaOfId($row['id']);
                            $toUpdate['flag'] = $this->optionRepo->OptionOfId($flag);
                            $this->projectJrBaRepo->update($toUpdate['id'], $toUpdate);
                        }
                    }
                    return response()->json(['status' => 'updated']);
                }
                return response()->json(['status' => 'updated']);
            }
            return response()->json('error');
        } else {
            $preparedData = $this->projectRepo->prepareData($data);
            $project_create = $this->projectRepo->create($preparedData);
            $project_id = $project_create->getId();
            if ($project_id) {
                $project_ba['project_id'] = $this->projectRepo->ProjectOfId($project_id);
                $project_ba['emp_id'] = $data['assigned_to'];
                $project_ba['flag'] = $this->optionRepo->OptionOfId(122);
                $preparedData = $this->projectBaRepo->prepareData($project_ba);
                $project_ba_id = $this->projectBaRepo->create($preparedData);

                $conv_data['project_id'] = $project_ba['project_id'];
                $conv_data['project_ba_id'] = $this->projectBaRepo->ProjectOfId($project_ba_id);
                $preparedData = $this->projectConversationRepo->prepareData($conv_data);
                $create = $this->projectConversationRepo->create($preparedData);

                if ($create) {
                    $from_emp = $project_create->getCreatedBy()->getFirstname() . ' ' . $project_create->getCreatedBy()->getLastname();
                    $notify[] = [
                        'channel_name' => 'project-update-' . $assigned_emp,
                        'from_emp' => $from_emp,
                        'title' => 'Project Assigned',
                        'details' => $from_emp . ' assigned you ' . $project_create->getProjectName()
                    ];
                    return response()->json(['status' => 'created', 'data' => $notify]);
                }
            }
            return response()->json(['status' => 'error']);
        }
    }

    function getAllProjectsDataTable(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $emp_id = JWTAuth::user();
        $allRecords = $this->projectRepo->countAllProjects($emp_id);
        $filterRecords = $this->projectRepo->getFilterRecords($search,$emp_id);
        $data = $this->projectRepo->getAllProjectsDataTable($order, $column_name, $search, $start, $length, $emp_id);

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    function getProjectById(Request $request)
    {
        $data = $this->projectRepo->getProjectById($request->id);
        return response()->json($data);
    }

    function getAllBASelfSales()
    {
        $data = $this->projectRepo->getAllBASelfSales();
        return response()->json($data);
    }
}

<?php

namespace App\Http\Controllers;

use App\Events\SendStatusUpdate;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProjectJrBaController extends Controller
{
    function getBaProjectById(Request $request)
    {
        $id = $request->id;
        $data = $this->projectJrBaRepo->getJrBaProjectById($id);
        return response()->json($data);
    }

    function saveBaProject(Request $request)
    {
        $data = $request->all();
        $notify = [];
        if ($data['est_time_changed'] && $data['est_time'] > 0) {
            $data['flag'] = $this->optionRepo->OptionOfId(123);
        }
        $data['id'] = $this->projectJrBaRepo->ProjectJrBaOfId($data['id']);
        $data['sr_to_jr_flag'] = $this->optionRepo->OptionOfId(124);
        $updateJrBaProject = $this->projectJrBaRepo->update($data['id'], $data);
        if ($updateJrBaProject) {
            if ($data['est_time_changed']) {
                $from_emp = $updateJrBaProject->getEmpId()->getFirstname() . ' ' . $updateJrBaProject->getEmpId()->getLastname();
                $notify[] = [
                    'channel_name' => 'project-update-' . $updateJrBaProject->getBaProjectId()->getEmpId()->getId(),
                    'from_emp' => $from_emp,
                    'title' => 'Project Estimation',
                    'details' => $from_emp . ' sent estimated hours on ' . $updateJrBaProject->getProjectId()->getProjectName()
                ];
                return response()->json(['status' => 'updated', 'data' => $notify]);
            } else {
                return response()->json(['status' => 'updated']);
            }
        }
        return response()->json(['status' => 'error']);
    }

    function getJrBaDataofProject(Request $request)
    {
        $ba_proj_id = $request->ba_project_id;
        $data = $this->projectJrBaRepo->getJrBaDataofProject($ba_proj_id);
        return response()->json($data);
    }

    function getAllProjectsJrBaDataTable(Request $request)
    {
        $emp_id = JWTAuth::toUser()->getId();
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $allRecords = $this->projectJrBaRepo->countAllProjectsBa($emp_id);
        $filterRecords = $this->projectJrBaRepo->getFilterRecords($search,$emp_id);
        $data = $this->projectJrBaRepo->getAllProjectsBaDataTable($order, $column_name, $search, $start, $length, $emp_id);

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] =count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }
}

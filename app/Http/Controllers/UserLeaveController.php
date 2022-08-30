<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserLeaveController extends Controller
{
    function getLeaveByEmp(Request $request)
    {
        $data = $this->userLeaveRepo->getLeaveByEmp($request->emp_id);
        return response()->json($data);
    }

    function getAllUserLeaves(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $allRecords = $this->userLeaveRepo->countAllUserLeaves();
        $data = $this->userLeaveRepo->getAllUserLeaves($column_name, $order, $search, $start, $length);
        $filteredRows = $this->userLeaveRepo->countFilteredRows($search);
        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filteredRows) > 0 ? count($filteredRows) : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }
    function getLeaveBalance($id)
    {
        $data = $this->userLeaveRepo->getLeaveBalance($id);
        return response()->json($data);
    }
    function saveLeaveBalance(Request $request)
    {
        $data = $request->all();
        $id = $this->userLeaveRepo->UserLeaveOfId($request->id);
        $update = $this->userLeaveRepo->update($id, $data);
        if ($update) {
            return response()->json("Leave Balance Updated SuccesFully");
        }
        return response()->json('error');
    }
}

<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class LeaveApplicationController extends Controller
{
    public function saveLeaveApplication(Request $request)
    {
        $data = $request->all();
        if (isset($data["user_id"])) {
            $data["user_id"] = $this->userRepo->UserOfId($data["user_id"]);
        }

        if (isset($data["leave_type"])) {
            $data["leave_type"] = $this->leavetypeRepo->LeaveTypeOfId($data["leave_type"]["id"]);
        }

        if (isset($data['leave_date'])) {
            $data['leave_date'] = new \DateTime($data['leave_date']);
        }

        if (isset($request->leave_id)) {
            $leave_application = $this->leaveapplicationRepo->Leave_applicationOfId($request->leave_id);
            $this->leaveapplicationRepo->update($leave_application, $data);
            return response()->json("updated", 200);
        } else {
            $data['status'] = 'Pending';
            $prepared_data = $this->leaveapplicationRepo->prepareData($data);
            $create = $this->leaveapplicationRepo->create($prepared_data);
            return response()->json("created", 200);
        }
        return response()->json('error', 200);
    }

    function getAllLeaveApplication(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $durationSearch = $request->columns[2]['search']['value'];
        $endDuration = '';
        if ($durationSearch == 'this_month') {
            $durationSearch = Carbon::today()->startOfMonth();
            $endDuration = Carbon::today()->endOfMonth();
        } else if ($durationSearch == 'last_30_days') {
            $durationSearch = Carbon::today()->subDays(30);
            $endDuration = Carbon::today();
        } else if ($durationSearch == 'last_3_months') {
            $durationSearch = Carbon::today()->subMonths(3)->startOfMonth();
            $endDuration = Carbon::today()->subMonth()->endOfMonth();
        } else if ($durationSearch == 'last_6_months') {
            $durationSearch = Carbon::today()->subMonths(6)->startOfMonth();
            $endDuration = Carbon::today()->subMonth()->endOfMonth();
        } else if ($durationSearch == 'last_year') {
            $durationSearch = Carbon::today()->subYear();
            $endDuration = Carbon::today()->subYear()->endOfYear();
        } else if ($durationSearch == 'today') {
            $durationSearch = Carbon::today();
            $endDuration = Carbon::today();
        } else {
            $durationSearch = 'all';
        }
        $statusSearch = $request->columns[4]['search']['value'];
        $allRecords = $this->leaveapplicationRepo->countAllRow();
        $data = $this->leaveapplicationRepo->getAllLeave_application($column_name, $order, $search, $start, $length, $durationSearch, $endDuration, $statusSearch);
        $filteredRecords = $this->leaveapplicationRepo->countFilteredRow($statusSearch, $endDuration, $durationSearch, $search);
        for ($i = 0; $i < count($data); $i++) {
            $temp = $data[$i]['user_id'];
            unset($data[$i]['user_id']);
            $data[$i]['user_id'] = ['id' => $temp['id'], 'firstname' => $temp['firstname'], 'lastname' => $temp['lastname'], 'profile_image' => $temp['profile_image']];
            $data[$i]['firstname'] = $data[$i]['user_id']['firstname'] . ' ' . $data[$i]['user_id']['lastname'];
        }

        $pending_leaves = $this->leaveapplicationRepo->countPendingLeaves();
        $res = ['data' => $data, 'count' => count($pending_leaves) > 0 ? $pending_leaves[0]['count'] : 0];

        $response['data'] = $res;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filteredRecords) > 0 ? (int)$filteredRecords[0]['count'] : 0;
        $response['recordsTotal'] =  count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    function getPendingLeavesCount()
    {
        $pending_leaves = $this->leaveapplicationRepo->countPendingLeaves();
        return response()->json(['count' => count($pending_leaves) > 0 ? $pending_leaves[0]['count'] : 0]);
    }

    function getFirstApprovedApplications(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'updated_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $durationSearch = $request->columns[2]['search']['value'];
        $endDuration = '';
        if ($durationSearch == 'this_month') {
            $durationSearch = Carbon::today()->startOfMonth();
            $endDuration = Carbon::today()->endOfMonth();
        } else if ($durationSearch == 'last_30_days') {
            $durationSearch = Carbon::today()->subDays(30);
            $endDuration = Carbon::today();
        } else if ($durationSearch == 'last_3_months') {
            $durationSearch = Carbon::today()->subMonths(3)->startOfMonth();
            $endDuration = Carbon::today()->subMonth()->endOfMonth();
        } else if ($durationSearch == 'last_6_months') {
            $durationSearch = Carbon::today()->subMonths(6)->startOfMonth();
            $endDuration = Carbon::today()->subMonth()->endOfMonth();
        } else if ($durationSearch == 'last_year') {
            $durationSearch = Carbon::today()->subYear();
            $endDuration = Carbon::today()->subYear()->endOfYear();
        } else if ($durationSearch == 'today') {
            $durationSearch = Carbon::today();
            $endDuration = Carbon::today();
        } else {
            $durationSearch = 'all';
        }
        $allRecords = $this->leaveapplicationRepo->countFirstApprovedRows();
        $data = $this->leaveapplicationRepo->getFirstApprovedApplications($column_name, $order, $search, $start, $length, $durationSearch, $endDuration);
        $filteredRecords = $this->leaveapplicationRepo->countFirstApprovedFilteredRows($endDuration, $durationSearch, $search);
        for ($i = 0; $i < count($data); $i++) {
            $temp = $data[$i]['user_id'];
            unset($data[$i]['user_id']);
            $data[$i]['user_id'] = ['id' => $temp['id'], 'firstname' => $temp['firstname'], 'lastname' => $temp['lastname'], 'profile_image' => $temp['profile_image']];
            $data[$i]['firstname'] = $data[$i]['user_id']['firstname'] . ' ' . $data[$i]['user_id']['lastname'];
            if ($data[$i]['leave_date'] == Carbon::today()) {
                $data[$i]['today'] = true;
            }
        }
        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filteredRecords) > 0 ? (int)$filteredRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    function getLeaveApplicationByID(Request $request)
    {
        return $this->leaveapplicationRepo->getLeave_applicationById($request->id);
    }

    function updateAcceptStatus(Request $request)
    {
        $data['status'] = 'Accept';
        $leave_application = $this->leaveapplicationRepo->Leave_applicationOfId($request->id);
        $this->leaveapplicationRepo->update($leave_application, $data);
        return response()->json("Updated Successfully", 200);
    }

    function updateRejectStatus(Request $request)
    {
        $data['status'] = 'Reject';
        $data['reject_reason'] = $request->reject_reason;
        $leave_application = $this->leaveapplicationRepo->Leave_applicationOfId($request->id);
        $this->leaveapplicationRepo->update($leave_application, $data);
        return response()->json("Updated Successfully", 200);
    }
    function updateCancelStatus(Request $request)
    {
        $data['status'] = 'Cancel';
        $data['reason'] = $request->reason;
        $leave_application = $this->leaveapplicationRepo->Leave_applicationOfId($request->id);
        $this->leaveapplicationRepo->update($leave_application, $data);
        return response()->json("Updated Successfully", 200);
    }

    function deleteLeaveApplication(Request $request)
    {
        $leaveapplication = $this->leaveapplicationRepo->Leave_applicationOfId($request->id);
        $data = $this->leaveapplicationRepo->delete($leaveapplication);

        return response()->json("deleted", 200);
    }

    function getLeaveRemaining(Request $request)
    {
        $data = $request->all();
        if (isset($data['year'])) {
            $date = new \DateTime();
            $year = $data['year'];
            $data['startdate'] = $date->format("1/1/" . $data['year']);
            $data['enddate'] = $date->format("12/31/" . $data['year']);
            $data['startdate'] = new \DateTime($data['startdate']);
            $data['enddate'] = new \DateTime($data['enddate']);
        }

        $leave_data = $this->leaveapplicationRepo->getuserLeave($data);

        $leave_type = $this->leavetypeRepo->getAllLeaveType();
        if ($leave_data) {
            foreach ($leave_type as $k => $v) {
                $leave_count = 0;
                $temp_data = array();
                foreach ($leave_data as $key => $value) {
                    if ($value['status'] == 'Accept') {
                        if ($v['id'] == $value['leave_type']['id']) {
                            $leave_count += $value['leave_count'];
                        }
                    }
                }
                $leave_remamining = $v['count'] - $leave_count;
                $temp_data['leavetype'] = $v['leavetype'];
                $temp_data['count'] = $leave_remamining;
                $leave_remaminings[] = $temp_data;
            }

            return $leave_remaminings;
        }
    }


    function getEmployeeleaves(Request $request)
    {
        $emp_id = $request->emp_id;
        $data = $this->leaveapplicationRepo->getEmployeeleaves($emp_id);
        return response()->json($data);
    }

    function getSelfLeaveRequiredData(Request $request)
    {
        $emp_id = $request->emp_id;
        //        $finalLeaves = $this->leaveApprovedRepo->getEmpFinalLeaves($emp_id);
        $finalLeaves = $this->leaveApprovedRepo->getSelfEmpFinalLeaves($emp_id);
        $leaveInfoByEmp = $this->userLeaveRepo->getLeaveByEmp($emp_id);
        $leaveTypes = $this->leavetypeRepo->getAllLeaveType();

        if (count($leaveInfoByEmp) > 0) {
            $leaveInfoByEmp = $leaveInfoByEmp[0];
        }

        $data = array(
            'finalLeaves' => $finalLeaves,
            'leaveByEmp' => $leaveInfoByEmp,
            'leaveTypes' => $leaveTypes
        );
        return response()->json($data);
    }

    function getLeaveRequiredData(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $durationSearch = $request->columns[2]['search']['value'];
        $endDuration = '';
        $emp_id = JWTAuth::user()->getId();

        if ($durationSearch == 'this_month') {
            $durationSearch = Carbon::today()->startOfMonth();
            $endDuration = Carbon::today()->endOfMonth();
        } else if ($durationSearch == 'last_30_days') {
            $durationSearch = Carbon::today()->subDays(30);
            $endDuration = Carbon::today();
        } else if ($durationSearch == 'last_3_months') {
            $durationSearch = Carbon::today()->subMonths(3)->startOfMonth();
            $endDuration = Carbon::today()->subMonth()->endOfMonth();
        } else if ($durationSearch == 'last_6_months') {
            $durationSearch = Carbon::today()->subMonths(6)->startOfMonth();
            $endDuration = Carbon::today()->subMonth()->endOfMonth();
        } else if ($durationSearch == 'last_year') {
            $durationSearch = Carbon::today()->subYear();
            $endDuration = Carbon::today()->subYear()->endOfYear();
        } else if ($durationSearch == 'today') {
            $durationSearch = Carbon::today();
            $endDuration = Carbon::today();
        } else {
            $durationSearch = 'all';
        }

        $statusSearch = $request->columns[4]['search']['value'];
        $allRecords = $this->leaveapplicationRepo->countEmployeeLeaves($emp_id, $statusSearch, $endDuration, $durationSearch, $search);
        $filterRecords = $this->leaveapplicationRepo->getFilterRecords($emp_id, $statusSearch, $endDuration, $durationSearch, $search);
        $data = $this->leaveapplicationRepo->getEmployeeLeavesDatatable($emp_id, $column_name, $order, $search, $start, $length, $durationSearch, $endDuration, $statusSearch);

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] =  count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    function getLeaveDataDashboard()
    {
        $today_leaves = $this->leaveapplicationRepo->getTodayLeaves();

        // get double approved Leaves as well

        $approved_leaves = $this->leaveApprovedRepo->getTodayLeaves();

        for ($i = 0; $i < count($approved_leaves); $i++) {
            $approved_leaves[$i][0]['final_approve'] = true;
        }

        foreach ($approved_leaves as $leave) {
            $today_leaves[] = $leave;
        }

        // end

        for ($i = 0; $i < count($today_leaves); $i++) {
            $temp = $today_leaves[$i][0]['user_id'];
            unset($today_leaves[$i][0]['user_id']);
            $today_leaves[$i][0]['user_id'] = ['id' => $temp['id'], 'firstname' => $temp['firstname'], 'lastname' => $temp['lastname'], 'profile_image' => $temp['profile_image']];
        }
        $recent_leaves = $this->leaveapplicationRepo->getRecentLeaves();
        return response()->json(['today_leaves' => $today_leaves, 'recent_leaves' => $recent_leaves]);
    }

    function getLeavesSelfDashboard(Request $request)
    {
        $date = $request->date;
        $emp_id = JWTAuth::toUser()->getId();
        $data = $this->leaveapplicationRepo->getLeavesSelfDashboard($date, $emp_id);
//        return response()->json($data);
        $leader_member = $this->leaveapplicationRepo->getLeavesSelfTeamLeaderDashboard($date, $emp_id);
        return response()->json(['leader' => $data,'team_member' => $leader_member]);
    }
}

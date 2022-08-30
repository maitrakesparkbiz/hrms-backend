<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;


class LeaveApprovedController extends Controller
{
    function finalApproveLeave(Request $request)
    {
        $data = $request->all();
        
        //update in userleave table
        $row = $this->userLeaveRepo->getLeaveByEmp($data["user_id"]["id"]);

        //check user leave balance
        $leavetype = strtolower($data['leave_type']['leavetype']);
        $currentLeaveCount = 0;
        if($leavetype!== 'upl'){
            $currentLeaveCount = $row[0][$leavetype];
        }
        if((float)$currentLeaveCount >= (float)$data['leave_count'] || $leavetype== 'upl')
        {
            if (!empty($row)) {
                $row = $row[0];
                $leave_update = [];
                $leave_id = $this->userLeaveRepo->UserLeaveOfId($row['id']);

                $leave_type = $data['leave_type']['leavetype'];
                if ($leave_type == 'CL') {
                    $leave_update['cl'] = $row['cl'] - $data['leave_count'];
                } else if ($leave_type == 'PL') {
                    $leave_update['pl'] = $row['pl'] - $data['leave_count'];
                } else if ($leave_type == 'SL') {
                    $leave_update['sl'] = $row['sl'] - $data['leave_count'];
                } else {
                    $leave_update['used_upl'] = $row['used_upl'] + $data['leave_count'];
                }
                $res = $this->userLeaveRepo->update($leave_id, $leave_update);
                if (!empty($res)) {
                    $data['status'] = 'Accept';
                    if (isset($data['id'])) {
                        $data['leave_id'] = $this->leaveapplicationRepo->Leave_applicationOfId($data['id']);
                    }
                    if (isset($data["user_id"])) {
                        $data["user_id"] = $this->userRepo->UserOfId($data["user_id"]["id"]);
                    }

                    if (isset($data["leave_type"])) {
                        $data["leave_type"] = $this->leavetypeRepo->LeaveTypeOfId($data["leave_type"]["id"]);
                    }

                    if (isset($data['leave_date'])) {
                        $data['leave_date'] = new \DateTime($data['leave_date']['date']);
                    }
                    $prepare_data = $this->leaveApprovedRepo->prepareData($data);
                    $res = $this->leaveApprovedRepo->create($prepare_data);
                    if (!empty($res)) {
                        $id = $this->leaveapplicationRepo->Leave_applicationOfId($data['id']);
                        $update_data['final_approve'] = 1;
                        $result = $this->leaveapplicationRepo->update($id, $update_data);
                        if (!empty($result)) {
                            return response()->json('success');
                        }
                    }
                }
            }
            return response()->json('error');
        }
        else{
            return response()->json(['error'=>"User does not have sufficient ".$leavetype." leave balance"],500); 
        }
    }

    function saveFinalLeave(Request $request)
    {
        $data = $request->all();
        $row = $this->userLeaveRepo->getLeaveByEmp($data['user_id']);
        if (isset($data['id'])) {
            $data['leave_id'] = $this->leaveapplicationRepo->Leave_applicationOfId($data['id']);
        }
        if (!empty($row)) {
            $row = $row[0];
            $leave_update = [];
            $leave_id = $this->userLeaveRepo->UserLeaveOfId($row['id']);
            $leave_type = $data['leave_type']['leavetype'];
            if ($leave_type == 'CL') {
                $leave_update['cl'] = $row['cl'] - $data['leave_count'];
            } else if ($leave_type == 'PL') {
                $leave_update['pl'] = $row['pl'] - $data['leave_count'];
            } else if ($leave_type == 'SL') {
                $leave_update['sl'] = $row['sl'] - $data['leave_count'];
            } else {
                $leave_update['used_upl'] = $row['used_upl'] + $data['leave_count'];
            }
            $res = $this->userLeaveRepo->update($leave_id, $leave_update);
            if (!empty($res)) {
                $data['leave_date'] = new \DateTime($data['leave_date']);
                $data['user_id'] = $this->userRepo->UserOfId($data['user_id']);
                $data['leave_type'] = $this->leavetypeRepo->LeaveTypeOfId($data['leave_type']['id']);
                $data['status'] = 'Accept';
                $prepare_data = $this->leaveApprovedRepo->prepareData($data);
                $result = $this->leaveApprovedRepo->create($prepare_data);
                if (!empty($result)) {
                    return response()->json('success');
                }
            }
        }
        return response()->json('error');
    }

    function finalRejectLeave(Request $request)
    {
        $data = $request->all();
        $row = $this->userLeaveRepo->getLeaveByEmp($data['user_id']['id']);
        if (!empty($row)) {
            $row = $row[0];
            $leave_update = [];
            $leave_id = $this->userLeaveRepo->UserLeaveOfId($row['id']);
            $leave_type = $data['leave_type']['leavetype'];
            if ($leave_type == 'CL') {
                $leave_update['cl'] = $row['cl'] + $data['leave_count'];
            } else if ($leave_type == 'PL') {
                $leave_update['pl'] = $row['pl'] + $data['leave_count'];
            } else if ($leave_type == 'SL') {
                $leave_update['sl'] = $row['sl'] + $data['leave_count'];
            } else {
                $leave_update['used_upl'] = $row['used_upl'] - $data['leave_count'];
            }
            $res = $this->userLeaveRepo->update($leave_id, $leave_update);
            if (!empty($res)) {
                $id = $this->leaveApprovedRepo->LeaveApprovedOfId($data['id']);
                $leaveData['status'] = 'Reject';
                $leaveData['is_deleted'] = 1;
                $leaveData['reject_reason'] = $data['reject_reason'];
                $res = $this->leaveApprovedRepo->update($id, $leaveData);
                if (!empty($res)) {
                    return response()->json('updated');
                }
            }
        }
        return response()->json('error');
    }

    function getAllApprovedLeaves(Request $request)
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

        $allRecords = $this->leaveApprovedRepo->countAllRow();
        $data = $this->leaveApprovedRepo->getAllApprovedLeaves($column_name, $order, $search, $start, $length, $durationSearch, $endDuration);
        $filteredRows = $this->leaveApprovedRepo->countAllFilteredRows($endDuration, $durationSearch, $search);
        for ($i = 0; $i < count($data); $i++) {
            $temp = $data[$i]['user_id'];
            unset($data[$i]['user_id']);
            $data[$i]['user_id'] = ['id' => $temp['id'], 'firstname' => $temp['firstname'], 'lastname' => $temp['lastname'], 'profile_image' => $temp['profile_image']];
            $data[$i]['firstname'] = $data[$i]['user_id']['firstname'] . ' ' . $data[$i]['user_id']['lastname'];
        }
        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filteredRows) > 0 ? (int)$filteredRows[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    function updateFinalLeave(Request $request)
    {
        $data = $request->all();
        $data['id'] = $this->leaveApprovedRepo->LeaveApprovedOfId($data['id']);
        $update_arr['id'] = $data['id'];
        $update_arr['leave_count'] = $data['leave_count'];
        $update_arr['half_day'] = $data['half_day'];
        if (isset($data['leavetype'])) {
            $update = $this->leaveApprovedRepo->update($update_arr['id'], $update_arr);
        }
        if (isset($data['new_leave_type'])) {
            $update_arr['leave_type'] = $this->leavetypeRepo->LeaveTypeOfId($data['new_leave_type']['id']);
            $update = $this->leaveApprovedRepo->update($update_arr['id'], $update_arr);
        }
        if (!empty($update)) {
            $row = $this->userLeaveRepo->getLeaveByEmp($data['user_id']);
            if (!empty($row)) {
                $row = $row[0];
                $leave_update = [];
                $leave_id = $this->userLeaveRepo->UserLeaveOfId($row['id']);
                if (isset($data['leavetype'])) {
                    $leave_type = $data['leavetype'];
                    if ($data['half_day']) {
                        if ($leave_type == 'CL') {
                            $leave_update['cl'] = $row['cl'] + 0.5;
                        }
                        if ($leave_type == 'PL') {
                            $leave_update['pl'] = $row['pl'] + 0.5;
                        }
                        if ($leave_type == 'SL') {
                            $leave_update['sl'] = $row['sl'] + 0.5;
                        }
                        if ($leave_type == 'UPL') {
                            $leave_update['used_upl'] = $row['used_upl'] - 0.5;
                        }
                    } else {
                        if ($leave_type == 'CL') {
                            $leave_update['cl'] = $row['cl'] - 0.5;
                        }
                        if ($leave_type == 'PL') {
                            $leave_update['pl'] = $row['pl'] - 0.5;
                        }
                        if ($leave_type == 'SL') {
                            $leave_update['sl'] = $row['sl'] - 0.5;
                        }
                        if ($leave_type == 'UPL') {
                            $leave_update['used_upl'] = $row['used_upl'] + 0.5;
                        }
                    }
                }

                if (isset($data['new_leave_type'])) {
                    $old_leave_type = $data['old_leave_type'];
                    $new_leave_type = $data['new_leave_type']['leavetype'];

                    if ($old_leave_type == 'CL') {
                        $leave_update['cl'] = $row['cl'] + 0.5;
                    }
                    if ($old_leave_type == 'PL') {
                        $leave_update['pl'] = $row['pl'] + 0.5;
                    }
                    if ($old_leave_type == 'SL') {
                        $leave_update['sl'] = $row['sl'] + 0.5;
                    }
                    if ($old_leave_type == 'UPL') {
                        $leave_update['used_upl'] = $row['used_upl'] - 0.5;
                    }

                    if ($new_leave_type == 'CL') {
                        $leave_update['cl'] = $row['cl'] - 1;
                    }
                    if ($new_leave_type == 'PL') {
                        $leave_update['pl'] = $row['pl'] - 1;
                    }
                    if ($new_leave_type == 'SL') {
                        $leave_update['sl'] = $row['sl'] - 1;
                    }
                    if ($new_leave_type == 'UPL') {
                        $leave_update['used_upl'] = $row['used_upl'] + 1;
                    }
                }

                $res = $this->userLeaveRepo->update($leave_id, $leave_update);
                if (!empty($res)) {
                    return response()->json('updated');
                }
            }
        }
        return response()->json('error');
    }

    function getEmpFinalLeaves(Request $request)
    {
        $emp_id = $request->emp_id;
        $data = $this->leaveApprovedRepo->getEmpFinalLeaves($emp_id);
        return response()->json($data);
    }

    function getEmpAllTakenLeaves(Request $request)
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

        $emp_id = JWTAuth::user()->getId();

        $allRecords = $this->leaveApprovedRepo->countEmpAllTakenLeaves($emp_id);
        $data = $this->leaveApprovedRepo->getEmpAllTakenLeaves($emp_id, $column_name, $order, $search, $start, $length, $durationSearch, $endDuration);
        $filteredRows = $this->leaveApprovedRepo->countEmpFilteredRows($emp_id, $endDuration, $durationSearch, $search);

        $leave_count = $this->leaveApprovedRepo->sumEmployeeTakenLeaves($emp_id);
        $total_leaves = 0;
        foreach ($leave_count as $leave) {
            $total_leaves += $leave['leave_count'];
        }

        $response['data'] = ['total_leaves' => $total_leaves, 'leaves' => $data];
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filteredRows) > 0 ? (int)$filteredRows[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    function getEmpUPL(Request $request)
    {
        $emp_id = $request->emp_id;
        $data = $this->leaveApprovedRepo->getEmpUPL($emp_id);
        return response()->json($data);
    }

    function getYearMonthFinalLeaves(Request $request)
    {
        $date = $request->date;
        $data = $this->leaveApprovedRepo->getYearMonthFinalLeaves($date);

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['isFinal'] = true;
        }

        $leave_apps = $this->leaveapplicationRepo->getYearMonthLeaves($date);
        foreach ($leave_apps as $leave) {
            $data[] = $leave;
        }
        return response()->json($data);
    }
}

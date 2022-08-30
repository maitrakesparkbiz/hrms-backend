<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;


class CheckInController extends Controller
{
    public function getCheckInDataById(Request $request)
    {
        $data = $this->checkInRepo->getCheckInDataById($request->check_in_id);
        $data[0]['emp_id'] = $data['emp_id'];
        return $data[0];
    }



    //    public function getUserAttendanceByDate(Request $request)
    //    {
    //        $req = $request->all();
    //        $data = $this->checkInRepo->getUserAttendanceByDate($req['emp_id'], $req['start'], $req['end']);
    //        $newArr = [];
    //        $halfDays = 0;
    //        $absentDays = 0;
    //        $lateDays = 0;
    //        $lateDaysRatio = 0;
    //        $absentDaysRatio = 0;
    //        $leaveData = $this->leaveApprovedRepo->getUserLeaveByDate($req['emp_id'], $req['start'], $req['end']);
    //        if (count($leaveData) > 0) {
    //            foreach ($leaveData as $leave) {
    //                if ($leave['half_day']) {
    //                    $halfDays += 1;
    //                } else {
    //                    $absentDays += 1;
    //                }
    //            }
    //        }
    //        $i = 0;
    //        foreach ($data as $row) {
    //            if (!empty($row['check_in_time'])) {
    //                if ($row['is_late']) {
    //                    $lateDays += 1;
    //                }
    //                $newArr[$i]['entry_time'] = $row['check_in_time']->format('h:i a');
    //                $newArr[$i]['date'] = $row['check_in_time']->format('F j,Y');
    //                if (!empty($row['breaks'])) {
    //                    $timestamp = 0;
    //                    foreach ($row['breaks'] as $break) {
    //                        if (!empty($break['break_out_time'])) {
    //                            $timestamp += strtotime($break['break_out_time']->format('H:i:s')) - strtotime($break['break_in_time']->format('H:i:s'));
    //                        }
    //                    }
    //                    $diff = date('H:i', strtotime('00:00:00') + $timestamp);
    //                    $newArr[$i]['break_time'] = $diff;
    //                }
    //                if (!empty($row['check_out_time'])) {
    //                    $newArr[$i]['exit_time'] = $row['check_out_time']->format('h:i a');
    //                    $mainDiff = strtotime($newArr[$i]['exit_time']) - strtotime($newArr[$i]['entry_time']);
    //                    if (isset($newArr[$i]['break_time'])) {
    //                        $newArr[$i]['working_time'] = date('H:i', strtotime('00:00:00') + ($mainDiff - $timestamp));
    //                    } else {
    //                        $newArr[$i]['break_time'] = '00:00';
    //                        $newArr[$i]['working_time'] = date('H:i', strtotime('00:00:00') + $mainDiff);
    //                    }
    //                }
    //                $newArr[$i]['attn_data'] = $row;
    //            }
    //            $i++;
    //        }
    //        $result['data'] = $newArr;
    //        $presentDays = count($newArr);
    //        if($presentDays > 0){
    //            $lateDaysRatio = round((100 * $lateDays) / $presentDays);
    //            $absentDaysRatio = round((100 * $absentDays) / $presentDays);
    //        }
    //        $result['leave_data'] = array(
    //            'presentDays' => $presentDays,
    //            'lateDays' => $lateDays,
    //            'lateDaysRatio' => $lateDaysRatio,
    //            'halfDays' => $halfDays,
    //            'absentDays' => $absentDays,
    //            'absentDaysRatio' => $absentDaysRatio
    //        );
    //        return response()->json($result);
    //    }


    public function getUserAttendanceByDate(Request $request)
    {


        $dateRange = $request->dateRange;
        $start = $request->dataTablesParameters['start'];
        $length = $request->dataTablesParameters['length'];

        $emp_id = JWTAuth::toUser()->getId();

        $allRecords = $this->checkInRepo->countUserAttendanceByDate($emp_id, $dateRange['start_date'], $dateRange['end_date']);
        $data = $this->checkInRepo->getUserAttendanceByDate($emp_id, $start, $length, $dateRange['start_date'], $dateRange['end_date']);

        //create response array
        $newArr = [];
        $halfDays = 0;
        $absentDays = 0;
        $lateDays = 0;
        $lateDaysRatio = 0;
        $absentDaysRatio = 0;
        $leaveData = $this->leaveApprovedRepo->getUserLeaveByDate($emp_id, $dateRange['start_date'], $dateRange['end_date']);

        if (count($leaveData) > 0) {
            foreach ($leaveData as $leave) {
                if ($leave['half_day']) {
                    $halfDays += 1;
                } else {
                    $absentDays += 1;
                }
            }
        }
        $i = 0;
        $emp_total_staffing = 0;
        $defaultBreakTime = $this->companyRepo->getDefaultBreakTime();
        $defaultBreak = strtotime(date('H:i:s', mktime(0,intval($defaultBreakTime))));


        foreach ($data as $row) {
            $halfDaysFlag = false;
            foreach ($leaveData as $l_data) {

                if ($row['check_in_time']->format('Y-m-d') === $l_data['leave_date']->format('Y-m-d') && $l_data['half_day']) {
                    $halfDaysFlag = true;
                }
            }
            if (!empty($row['check_in_time'])) {
                if ($row['is_late']) {
                    $lateDays += 1;
                }
                $newArr[$i]['entry_time'] = $row['check_in_time']->format('h:i A');
                //                $newArr[$i]['date'] = $row['check_in_time']->format('F j,Y');
                $newArr[$i]['date'] = $row['check_in_time'];
                if (!empty($row['breaks'])) {
                    $timestamp = 0;
                    foreach ($row['breaks'] as $break) {
                        if (!empty($break['break_out_time'])) {
                            $timestamp += strtotime($break['break_out_time']->format('H:i:s')) - strtotime($break['break_in_time']->format('H:i:s'));
                        }

                    }


                    $originalBreak = strtotime('00:00:00') + $timestamp;
                    if (!$halfDaysFlag) {
                        if ($row['check_out_time']) {
                            if ($originalBreak > $defaultBreak) {
                                $diff = date('H:i', strtotime('00:00:00') + $timestamp);

                            } else {
                                $diff = date('H:i', $defaultBreak);
                                $timestamp = $defaultBreakTime*60;
                            }

                        } else {
                            $diff = date('H:i', strtotime('00:00:00') + $timestamp);
                        }
                    } else {
                        $diff = date('H:i', strtotime('00:00:00') + $timestamp);
                    }

                    $newArr[$i]['break_time'] = $diff;
                } else {
                    if (!$halfDaysFlag) {
                        if ($row['check_out_time']) {
                            $diff = date('H:i', $defaultBreak);
                            $timestamp = $defaultBreakTime * 60;
                            $newArr[$i]['break_time'] = $diff;
                        }

                    }
                }
                if (!empty($row['check_out_time'])) {
                    $newArr[$i]['exit_time'] = $row['check_out_time']->format('h:i A');
                    $mainDiff = strtotime($newArr[$i]['exit_time']) - strtotime($newArr[$i]['entry_time']);
                    if (isset($newArr[$i]['break_time'])) {
                        $newArr[$i]['working_time'] = date('H:i', strtotime('00:00:00') + ($mainDiff - $timestamp));
                        $newArr[$i]['staffing']['hours'] = (int)date('H', strtotime('00:00:00') + ($mainDiff - $timestamp));
                        $newArr[$i]['staffing']['minutes'] = (int)date('i', strtotime('00:00:00') + ($mainDiff - $timestamp));
                        $emp_total_staffing += ($mainDiff - $timestamp);
                        $newArr[$i]['staffing']['bar_data'] = (($mainDiff - $timestamp) / 3600);
                    } else {
                        if(!$halfDaysFlag) {
                            $newArr[$i]['break_time'] = '00:00';
                            $newArr[$i]['working_time'] = date('H:i', strtotime('00:00:00') + ($mainDiff - $defaultBreak));
                            $newArr[$i]['staffing']['hours'] = (int)date('H', strtotime('00:00:00') + ($mainDiff - $defaultBreak));
                            $newArr[$i]['staffing']['minutes'] = (int)date('i', strtotime('00:00:00') + ($mainDiff - $defaultBreak));
                            $emp_total_staffing += ($mainDiff);
                            $newArr[$i]['staffing']['bar_data'] = ($mainDiff / 3600);
                        }else{
                            $newArr[$i]['break_time'] = '00:00';
                            $newArr[$i]['working_time'] = date('H:i', strtotime('00:00:00') + ($mainDiff));
                            $newArr[$i]['staffing']['hours'] = (int)date('H', strtotime('00:00:00') + ($mainDiff));
                            $newArr[$i]['staffing']['minutes'] = (int)date('i', strtotime('00:00:00') + ($mainDiff));
                            $emp_total_staffing += ($mainDiff);
                            $newArr[$i]['staffing']['bar_data'] = ($mainDiff / 3600);
                        }
                    }
                }
                $newArr[$i]['attn_data'] = $row;
            }
            $i++;
        }

        $presentDays = count($newArr);

        $newArr = array_slice($newArr, $start, $length);
        $result['data'] = $newArr;

        if ($presentDays > 0) {
            $lateDaysRatio = round((100 * $lateDays) / $presentDays);
            $absentDaysRatio = round((100 * $absentDays) / $presentDays);
        }

        $user_batch_data = $this->locationRepo->getUserBatch($emp_id);
        $required_hours = 0;
        $required_minutes = 0;
        $emp_hours = 0;
        $emp_minutes = 0;
        $productivity_ratio = 0;
        $diff = 0;
        $half_day_hours = 0;
        if (count($user_batch_data) > 0) {
            $user_batch_data = $user_batch_data[0];
            if ($emp_total_staffing > 0) {
                $emp_hours = floor($emp_total_staffing / 3600);
                $emp_minutes = floor(($emp_total_staffing / 60) % 60);
            }

            if (isset($user_batch_data['office_start_time']) && isset($user_batch_data['office_end_time'])) {
                $start = $user_batch_data['office_start_time']->format('h:i A');
                $end = $user_batch_data['office_end_time']->format('h:i A');
                $diff = strtotime($end) - strtotime($start) - 3600;
                $staffing = $diff * $presentDays;

                $batch_half_day = $user_batch_data['half_day_hours']->format('h:i A');
                $half_day_hours = (strtotime($batch_half_day) - strtotime('00:00:00'));

                if ($staffing > 0) {
                    if ($halfDays > 0) {
                        $staffing = $staffing - ($half_day_hours * $halfDays);
                    }
                    $required_hours = floor($staffing / 3600);
                    $required_minutes = floor(($staffing / 60) % 60);

                    $productivity_ratio = (($emp_total_staffing * 100) / $staffing);
                }
            }
        }

        $result['leave_data'] = array(
            'presentDays' => $presentDays,
            'lateDays' => $lateDays,
            'lateDaysRatio' => $lateDaysRatio,
            'halfDays' => $halfDays,
            'absentDays' => $absentDays,
            'absentDaysRatio' => $absentDaysRatio,
            'required_staffing' => ['hours' => $required_hours, 'minutes' => $required_minutes],
            'emp_staffing' => ['hours' => $emp_hours, 'minutes' => $emp_minutes],
            'productivity_ratio' => number_format($productivity_ratio, '2'),
            'office_staffing' => ($diff > 0 ? ($diff / 3600) : 0)
        );

        $response['data'] = $result;
        $response['draw'] = $request->dataTablesParameters['draw'];
        $response['recordsFiltered'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($newArr);

        return response()->json($response);
    }


    public function getEmpProductivityRatio(Request $request)
    {
        $emp_id = $request->emp_id;
        $startDate = Carbon::today()->startOfYear()->format('Y-m-d 00:00:00');
        $endDate = Carbon::today()->endOfYear()->format('Y-m-d 23:23:59');
        $all_check_in_data = $this->checkInRepo->getEmpMonthYearData($emp_id, $startDate, $endDate);
        $leaveData = $this->leaveApprovedRepo->getUserLeaveByDate($emp_id, $startDate, $endDate);
        $staffing_arr = [];
        $leave_arr = [];
        $productivity = [];

        for ($i = 1; $i < 13; $i++) {
            $productivity[date('F', mktime(0, 0, 0, $i))] = '';
        }

        foreach ($all_check_in_data as $row) {
            $temp_staffing = 0;
            $temp_month = $row['check_in_time']->format('m');
            if (!empty($row['check_out_time'])) {
                $mainDiff = strtotime($row['check_out_time']->format('h:i A')) - strtotime($row['check_in_time']->format('h:i A'));
                if (!empty($row['breaks'])) {
                    $timestamp = 0;
                    foreach ($row['breaks'] as $break) {
                        if (!empty($break['break_out_time'])) {
                            $timestamp += strtotime($break['break_out_time']->format('H:i:s')) - strtotime($break['break_in_time']->format('H:i:s'));
                        }
                    }
                    $temp_staffing += ($mainDiff - $timestamp);
                } else {
                    $temp_staffing += ($mainDiff);
                }
                if (isset($staffing_arr[$temp_month])) {
                    $staffing_arr[$temp_month]['emp_staffing'] = (int)$staffing_arr[$temp_month]['emp_staffing'] + $temp_staffing;
                    //                    print_r($staffing_arr[$temp_month]['emp_staffing']." existing<br>");
                } else {
                    $staffing_arr[$temp_month]['emp_staffing'] = $temp_staffing;
                    //                    print_r($staffing_arr[$temp_month]['emp_staffing']." new<br>");
                }
            }
            if (isset($staffing_arr[$temp_month]['present_count'])) {
                $staffing_arr[$temp_month]['present_count'] += 1;
            } else {
                $staffing_arr[$temp_month]['present_count'] = 1;
            }
            $i++;
        }

        foreach ($leaveData as $user_leave) {
            $leave_month = $user_leave['leave_date']->format('m');
            if ($user_leave['half_day']) {
                if (isset($leave_arr[$leave_month]['half_days'])) {
                    $leave_arr[$leave_month]['half_days'] = $leave_arr[$leave_month]['half_days'] + 1;
                } else {
                    $leave_arr[$leave_month]['half_days'] = 1;
                }
            } else {
                if (isset($leave_arr[$leave_month]['full_days'])) {
                    $leave_arr[$leave_month]['full_days'] = $leave_arr[$leave_month]['full_days'] + 1;
                } else {
                    $leave_arr[$leave_month]['full_days'] = 1;
                }
            }
        }

        $user_batch_data = $this->locationRepo->getUserBatch($emp_id);
        if (count($user_batch_data) > 0) {
            $user_batch_data = $user_batch_data[0];
            if (isset($user_batch_data['office_start_time']) && isset($user_batch_data['office_end_time'])) {
                $start = $user_batch_data['office_start_time']->format('h:i A');
                $end = $user_batch_data['office_end_time']->format('h:i A');
                $diff = strtotime($end) - strtotime($start) - 3600;
                $batch_half_day = $user_batch_data['half_day_hours']->format('h:i A');
                $half_day_hours = (strtotime($batch_half_day) - strtotime('00:00:00'));
                foreach ($staffing_arr as $key => $value) {
                    $temp_staff = 0;
                    $temp_staff = $diff * $staffing_arr[$key]['present_count'];
                    if (isset($leave_arr[$key]) && isset($leave_arr[$key]['half_days'])) {
                        $temp_staff = $temp_staff - ($half_day_hours * $leave_arr[$key]['half_days']);
                    }
                    $staffing_arr[$key]['required_staffing'] = $temp_staff;
                    if (isset($staffing_arr[$key]['emp_staffing']) && $staffing_arr[$key]['emp_staffing'] > 0 && $staffing_arr[$key]['required_staffing'] > 0) {
                        $productivity[date('F', mktime(0, 0, 0, (int)$key))] = number_format(($staffing_arr[$key]['emp_staffing'] * 100) / $staffing_arr[$key]['required_staffing'], '2');
                    }
                }
            }
        }
        return response()->json($productivity);
        //        for ($i = 0; $i < count($all_check_in_data); $i++) {
        //        for ($i = 0; $i < count($all_check_in_data); $i++) {
        //            if ($staffing_data[$i]['staffing'] == null && !empty($all_check_in_data[$i]['check_out_time'])) {
        //                $all_check_in_data[$i]['staffing_hours'] = date_diff($all_check_in_data[$i]['check_out_time'], $all_check_in_data[$i]['check_in_time'])->format('%H:%I:%S');
        //            } else {
        //                $all_check_in_data[$i]['staffing_hours'] = $staffing_data[$i]['staffing'];
        //            }
        //        }
        //        return $all_check_in_data;
    }

    public function getTeamAttendanceByDate(Request $request)
    {
        $dateRange = $request->dateRange;
        $start = $request->dataTablesParameters['start'];
        $length = $request->dataTablesParameters['length'];

        $emp_id = $request->team_id;
        //
        $allRecords = $this->checkInRepo->countUserAttendanceByDate($emp_id, $dateRange['start_date'], $dateRange['end_date']);
        $data = $this->checkInRepo->getUserAttendanceByDate($emp_id, $start, $length, $dateRange['start_date'], $dateRange['end_date']);

        //create response array
        $newArr = [];
        $halfDays = 0;
        $absentDays = 0;
        $lateDays = 0;
        $lateDaysRatio = 0;
        $absentDaysRatio = 0;

        $leaveStartDate = Carbon::today()->format('Y-m-d 00:00:00');
        $leaveEndDate = Carbon::today()->addDays(15)->format('Y-m-d 00:00:00');

        $leaveData = $this->leaveApprovedRepo->getTeamLeaveByDate($emp_id, $leaveStartDate, $leaveEndDate);

        if (count($leaveData) > 0) {
            foreach ($leaveData as $leave) {
                if ($leave['half_day']) {
                    $halfDays += 1;
                } else {
                    $absentDays += 1;
                }
            }
        }
        $i = 0;
        $emp_total_staffing = 0;

        foreach ($data as $row) {
            if (!empty($row['check_in_time'])) {
                if ($row['is_late']) {
                    $lateDays += 1;
                }
                $newArr[$i]['entry_time'] = $row['check_in_time']->format('h:i A');
                //                $newArr[$i]['date'] = $row['check_in_time']->format('F j,Y');
                $newArr[$i]['date'] = $row['check_in_time'];
                if (!empty($row['breaks'])) {
                    $timestamp = 0;
                    foreach ($row['breaks'] as $break) {
                        if (!empty($break['break_out_time'])) {
                            $timestamp += strtotime($break['break_out_time']->format('H:i:s')) - strtotime($break['break_in_time']->format('H:i:s'));
                        }
                    }
                    $diff = date('H:i', strtotime('00:00:00') + $timestamp);
                    $newArr[$i]['break_time'] = $diff;
                }
                if (!empty($row['check_out_time'])) {
                    $newArr[$i]['exit_time'] = $row['check_out_time']->format('h:i A');
                    $mainDiff = strtotime($newArr[$i]['exit_time']) - strtotime($newArr[$i]['entry_time']);
                    if (isset($newArr[$i]['break_time'])) {
                        $newArr[$i]['working_time'] = date('H:i', strtotime('00:00:00') + ($mainDiff - $timestamp));
                        $newArr[$i]['staffing']['hours'] = (int)date('H', strtotime('00:00:00') + ($mainDiff - $timestamp));
                        $newArr[$i]['staffing']['minutes'] = (int)date('i', strtotime('00:00:00') + ($mainDiff - $timestamp));
                        $emp_total_staffing += ($mainDiff - $timestamp);
                        $newArr[$i]['staffing']['bar_data'] = (($mainDiff - $timestamp) / 3600);
                    } else {
                        $newArr[$i]['break_time'] = '00:00';
                        $newArr[$i]['working_time'] = date('H:i', strtotime('00:00:00') + $mainDiff);
                        $newArr[$i]['staffing']['hours'] = (int)date('H', strtotime('00:00:00') + $mainDiff);
                        $newArr[$i]['staffing']['minutes'] = (int)date('i', strtotime('00:00:00') + $mainDiff);
                        $emp_total_staffing += ($mainDiff);
                        $newArr[$i]['staffing']['bar_data'] = ($mainDiff / 3600);
                    }
                }
                $newArr[$i]['attn_data'] = $row;
            }
            $i++;
        }

        $presentDays = count($newArr);

        $newArr = array_slice($newArr, $start, $length);
        $result['data'] = $newArr;

        if ($presentDays > 0) {
            $lateDaysRatio = round((100 * $lateDays) / $presentDays);
            $absentDaysRatio = round((100 * $absentDays) / $presentDays);
        }

        $user_batch_data = $this->locationRepo->getUserBatch($emp_id);
        $required_hours = 0;
        $required_minutes = 0;
        $emp_hours = 0;
        $emp_minutes = 0;
        $productivity_ratio = 0;
        $diff = 0;
        $half_day_hours = 0;
        if (count($user_batch_data) > 0) {
            $user_batch_data = $user_batch_data[0];
            if ($emp_total_staffing > 0) {
                $emp_hours = floor($emp_total_staffing / 3600);
                $emp_minutes = floor(($emp_total_staffing / 60) % 60);
            }

            if (isset($user_batch_data['office_start_time']) && isset($user_batch_data['office_end_time'])) {
                $start = $user_batch_data['office_start_time']->format('h:i A');
                $end = $user_batch_data['office_end_time']->format('h:i A');
                $diff = strtotime($end) - strtotime($start) - 3600;
                $staffing = $diff * $presentDays;

                $batch_half_day = $user_batch_data['half_day_hours']->format('h:i A');
                $half_day_hours = (strtotime($batch_half_day) - strtotime('00:00:00'));

                if ($staffing > 0) {
                    if ($halfDays > 0) {
                        $staffing = $staffing - ($half_day_hours * $halfDays);
                    }
                    $required_hours = floor($staffing / 3600);
                    $required_minutes = floor(($staffing / 60) % 60);

                    $productivity_ratio = (($emp_total_staffing * 100) / $staffing);
                }
            }
        }

        //        $result['leave_data'] = array(
        //            'presentDays' => $presentDays,
        //            'lateDays' => $lateDays,
        //            'lateDaysRatio' => $lateDaysRatio,
        //            'halfDays' => $halfDays,
        //            'absentDays' => $absentDays,
        //            'absentDaysRatio' => $absentDaysRatio,
        //            'required_staffing' => ['hours' => $required_hours, 'minutes' => $required_minutes],
        //            'emp_staffing' => ['hours' => $emp_hours, 'minutes' => $emp_minutes],
        //            'productivity_ratio' => number_format($productivity_ratio, '2'),
        //            'office_staffing' => ($diff > 0 ? ($diff / 3600) : 0)
        //        );
        $result['leave_data'] = $leaveData;
        $response['data'] = $result;
        $response['draw'] = $request->dataTablesParameters['draw'];
        $response['recordsFiltered'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($newArr);

        return response()->json($response);
    }
}

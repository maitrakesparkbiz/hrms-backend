<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AttendanceController extends Controller
{
    function presentEvent(Request $request)
    {
        $data = $request->all();
        $verifyCheckOut = $this->checkInRepo->verifyCheckOutOfEmp($data['emp_id']);
        if (count($verifyCheckOut) == 0) {
            $currentTime = new \DateTime();
            $today_startdatetime = $currentTime->createFromFormat("Y-m-d H:i:s", date("Y-m-d 00:00:00"));
            $today_enddatetime = $currentTime->createFromFormat("Y-m-d H:i:s", date("Y-m-d 23:59:59"));
            $check_in_data = $this->getCheckInData($data['emp_id'], $today_startdatetime, $today_enddatetime);
            $emp_obj = $this->userRepo->UserOfId($data['emp_id']);

            if ($data['event'] === 'check_in') {
                if (empty($check_in_data)) {
                    $res = $this->checkLateCheckIn($data['emp_id'], $currentTime);
                    if ($res) {
                        if ($res != 'no_data') {
                            $data['is_late'] = $res['is_late'];
                            $data['late_minutes'] = $res['late_minutes'];
                        }
                    }
                    $data['check_in_time'] = $currentTime;
                    $data['check_in_ip'] = $this->getIp();
                    $data['emp_id'] = $emp_obj;
                    $prepareData = $this->checkInRepo->prepareData($data);
                    $check_in_id = $this->checkInRepo->create($prepareData);
                    if (isset($check_in_id)) {
                        return response()->json('both');
                    } else {
                        return response()->json('error');
                    }
                } else {
                    $check_in_data = $check_in_data[0];
                    if ($check_in_data['check_out_time'] == null) {
                        return response()->json('both');
                    } else {
                        return response()->json('end');
                    }
                }
            } else if ($data['event'] === 'check_out') {
                if (!empty($check_in_data)) {
                    $check_in_data = $check_in_data[0];
                    if ($check_in_data['check_out_time'] == null) {
                        $break_in_data = $this->breaksRepo->getBreakInData($data['emp_id'], $today_startdatetime, $today_enddatetime);
                        if (!empty($break_in_data)) {
                            return response()->json('breakout');
                        }
                        $check_in_id = $check_in_data['id'];
                        $id_obj = $this->checkInRepo->CheckInOfId($check_in_id);
                        $data['check_out_time'] = $currentTime;
                        $data['check_out_ip'] = $this->getIp();
                        $data['check_out_emp_id'] = $this->userRepo->UserOfId($data['emp_id']);
                        unset($data['emp_id']);
                        $res = $this->checkInRepo->update($id_obj, $data);
                        if (isset($res)) {
                            return response()->json('end');
                        } else {
                            return response()->json('error');
                        }
                    } else {
                        return response()->json('end');
                    }
                } else {
                    return response()->json('checkin');
                }
            } else if ($data['event'] === 'break_in') {
                if (!empty($check_in_data)) {
                    $check_in_data = $check_in_data[0];
                    if ($check_in_data['check_out_time'] == null) {
                        $break_in_data = $this->breaksRepo->getBreakInData($data['emp_id'], $today_startdatetime, $today_enddatetime);
                        if (empty($break_in_data)) {
                            $check_in_id = $check_in_data['id'];
                            $data['check_in_id'] = $this->checkInRepo->CheckInOfId($check_in_id);
                            $data['emp_id'] = $emp_obj;
                            $data['break_in_time'] = $currentTime;
                            $prepareData = $this->breaksRepo->prepareData($data);
                            $res = $this->breaksRepo->create($prepareData);
                            if (isset($res)) {
                                return response()->json('breakout');
                            } else {
                                return response()->json('error');
                            }
                        } else {
                            return response()->json('breakout');
                        }
                    } else {
                        return response()->json('end');
                    }
                } else {
                    return response()->json('checkin');
                }
            } else {
                if (!empty($check_in_data)) {
                    $check_in_data = $check_in_data[0];
                    if ($check_in_data['check_out_time'] == null) {
                        $break_in_data = $this->breaksRepo->getBreakInData($data['emp_id'], $today_startdatetime, $today_enddatetime);
                        if (!empty($break_in_data)) {
                            $break_in_data = $break_in_data[0];
                            $break_in_id = $this->breaksRepo->BreaksOfId($break_in_data['id']);
                            $data['break_out_time'] = $currentTime;
                            unset($data['emp_id']);
                            $update = $this->breaksRepo->update($break_in_id, $data);
                            if (isset($update)) {
                                return response()->json('both');
                            } else {
                                return response()->json('error');
                            }
                        } else {
                            return response()->json('both');
                        }
                    } else {
                        return response()->json('end');
                    }
                } else {
                    return response()->json('checkin');
                }
            }
        } else {
            return response()->json('no_checkout');
        }
    }

    function getCheckInData($emp_id, $today_startdatetime, $today_enddatetime)
    {
        $data = $this->checkInRepo->getCheckInIdByDate($emp_id, $today_startdatetime, $today_enddatetime);
        return $data;
    }

    function checkCurrentStatus()
    {
        $emp_id = JWTAuth::toUser()->getId();
        $currentTime = new \DateTime();
        $today_startdatetime = $currentTime->createFromFormat("Y-m-d H:i:s", date("Y-m-d 00:00:00"));
        $today_enddatetime = $currentTime->createFromFormat("Y-m-d H:i:s", date("Y-m-d 23:59:59"));

        $verifyCheckOut = $this->checkInRepo->verifyCheckOutOfEmp($emp_id);
        if (count($verifyCheckOut) == 0) {
            // get yesterday's staffing
            $staffing_err = false;
            $staffing_diff = 0;
            $batch_staffing = '00:00';
            $previous_staffing = $this->verifyPreviousStaffing($emp_id);

            $batch_data = $this->locationRepo->getUserBatch($emp_id);
            if (count($batch_data) > 0) {
                $batch_data = $batch_data[0];
                if (isset($batch_data['office_start_time']) && isset($batch_data['office_end_time'])) {
                    $start = $batch_data['office_start_time']->format('h:i A');
                    $end = $batch_data['office_end_time']->format('h:i A');
                    $diff = strtotime($end) - strtotime($start);
                    $batch_staffing = date('H:i', strtotime('00:00:00') + ($diff - 3600));
                }

                if ($previous_staffing) {
                    $staffing_diff = strtotime($batch_staffing) - strtotime($previous_staffing);
                }
                if ($staffing_diff > 0) {
                    $staffing_err = true;
                }
            }
            // end
            $check_in_data = $this->getCheckInData($emp_id, $today_startdatetime, $today_enddatetime);
            if (!empty($check_in_data)) {
                $check_in_data = $check_in_data[0];
                if ($check_in_data['check_out_time'] == null) {
                    $break_in_data = $this->breaksRepo->getBreakInData($emp_id, $today_startdatetime, $today_enddatetime);
                    if (!empty($break_in_data)) {
                        return response()->json(['status' => 'breakout', 'less_staffing' => $staffing_err, 'hours' => $batch_staffing]);
                    } else {
                        return response()->json(['status' => 'both', 'less_staffing' => $staffing_err, 'hours' => $batch_staffing]);
                    }
                } else {
                    return response()->json(['status' => 'end', 'less_staffing' => $staffing_err, 'hours' => $batch_staffing]); //end of today's attendance
                }
            } else {
                return response()->json(['status' => 'checkin', 'less_staffing' => $staffing_err, 'hours' => $batch_staffing]); //start of today's attendance
            }
        } else {
            // no late msg
            return response()->json(['status' => 'no_checkout']);
        }
    }

    function verifyPreviousStaffing($emp_id)
    {
        $previous_check_in = $this->checkInRepo->getPreviousCheckIn($emp_id);
        $newArr['working_time'] = '';
        if ($previous_check_in) {
            $data = $previous_check_in[0];
            $newArr['entry_time'] = $data['check_in_time']->format('h:i A');
            if (!empty($data['breaks'])) {
                $timestamp = 0;
                foreach ($data['breaks'] as $break) {
                    if (!empty($break['break_out_time'])) {
                        $timestamp += strtotime($break['break_out_time']->format('H:i:s')) - strtotime($break['break_in_time']->format('H:i:s'));
                    }
                }
                $diff = date('H:i', strtotime('00:00:00') + $timestamp);
                $newArr['break_time'] = $diff;
            }
            $newArr['exit_time'] = $data['check_out_time']->format('h:i A');
            $mainDiff = strtotime($newArr['exit_time']) - strtotime($newArr['entry_time']);
            if (isset($newArr['break_time'])) {
                $newArr['working_time'] = date('H:i', strtotime('00:00:00') + ($mainDiff - $timestamp));
            } else {
                $newArr['break_time'] = '00:00';
                $newArr['working_time'] = date('H:i', strtotime('00:00:00') + $mainDiff);
            }
        }
        return $newArr['working_time'];
    }

    function generateListings()
    {
        $emp_id = JWTAuth::toUser()->getId();
        $listing = [];
        $currentTime = new \DateTime();
        $today_startdatetime = $currentTime->createFromFormat("Y-m-d H:i:s", date("Y-m-d 00:00:00"));
        $today_enddatetime = $currentTime->createFromFormat("Y-m-d H:i:s", date("Y-m-d 23:59:59"));

        //check yesterday staffing hours
        //        $previous = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($currentTime->format('Y-m-d H:i:s'))));
        //        $previous_start = date('Y-m-d 00:00:00',strtotime($previous));
        //        $previous_end = date('Y-m-d 23:59:59',strtotime($previous));
        //        $data = $this->checkInRepo->getEmpMonthYearData($emp_id, $previous_start, $previous_end);
        //
        //        return $data;


        //        if (!empty($row['user_check_in'])) {
        //            $newArr[$i]['entry_time'] = $row['user_check_in'][0]['check_in_time']->format('h:i a');
        //            if (!empty($row['user_check_in'][0]['breaks'])) {
        //                $timestamp = 0;
        //                foreach ($row['user_check_in'][0]['breaks'] as $break) {
        //                    if (!empty($break['break_out_time'])) {
        //                        $timestamp += strtotime($break['break_out_time']->format('H:i:s')) - strtotime($break['break_in_time']->format('H:i:s'));
        //                    }
        //                }
        //                $diff = date('H:i', strtotime('00:00:00') + $timestamp);
        //                $newArr[$i]['break_time'] = $diff;
        //            }
        //            if (!empty($row['user_check_in'][0]['check_out_time'])) {
        //                $newArr[$i]['exit_time'] = $row['user_check_in'][0]['check_out_time']->format('h:i a');
        //                $mainDiff = strtotime($newArr[$i]['exit_time']) - strtotime($newArr[$i]['entry_time']);
        //                if (isset($newArr[$i]['break_time'])) {
        //                    $newArr[$i]['working_time'] = date('H:i', strtotime('00:00:00') + ($mainDiff - $timestamp));
        //                } else {
        //                    $newArr[$i]['break_time'] = '00:00';
        //                    $newArr[$i]['working_time'] = date('H:i', strtotime('00:00:00') + $mainDiff);
        //                }
        //            }
        //        }


        //end
        $check_in_data = $this->getCheckInData($emp_id, $today_startdatetime, $today_enddatetime);
        $i = 0;
        $allData = ['listings' => [], 'comments' => [], 'ip' => []];
        if (!empty($check_in_data)) {
            $check_in_data = $check_in_data[0];
            $listing[$i]['type'] = 'Checked In';
            $listing[$i][] = $check_in_data['check_in_time'];
            $i++;
            $all_breaks = $this->breaksRepo->getBreaksToday($check_in_data['id']);
            if (!empty($all_breaks)) {
                foreach ($all_breaks as $row) {
                    $listing[$i]['type'] = 'Breaked In';
                    $listing[$i][] = $row['break_in_time'];
                    $i++;
                    if (isset($row['break_out_time'])) {
                        $listing[$i]['type'] = 'Breaked Out';
                        $listing[$i][] = $row['break_out_time'];
                        $i++;
                    }
                }
            }
            if (isset($check_in_data['check_out_time'])) {
                $listing[$i]['type'] = 'Checked Out';
                $listing[$i][] = $check_in_data['check_out_time'];
            }
            $comments_data = $this->commentsRepo->getCommentsToday($check_in_data['id']);
            $allData['listings'] = $listing;
            $allData['comments'] = $comments_data;
            $allData['ip'] = $this->getIp();
            return response()->json($allData);
        } else {
            $allData['ip'] = $this->getIp();
            return response()->json($allData);
        }
    }

    function getIp()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    function getEmpMonthYearData(Request $request)
    {
        if ($request->emp_id) {
            $emp_id = $request->emp_id;
            $currentTime = new \DateTime($request->month_year);
            $startDate = $currentTime->format('Y-m-01 00:00:00');
            $days = cal_days_in_month(CAL_GREGORIAN, $currentTime->format('m'), $currentTime->format('y'));
            $endDate = $currentTime->format('Y-m-' . $days . ' 23:23:59');
            $all_check_in_data = $this->checkInRepo->getEmpMonthYearData($emp_id, $startDate, $endDate);
            $final_approve_leave = $this->leaveApprovedRepo->getFinalApprovedLeave($emp_id, $startDate, $endDate);
//            if(count($final_approve_leave) > 0)
//            {
//                foreach ($all_check_in_data as $k=>$v)
//                {
//                    $all_check_in_data[$k]['final_approve_leave'] = $final_approve_leave;
//                }
//            }
//            return $final_approve_leave;

                foreach ($all_check_in_data as $k => $v) {
                    $halfDaysFlag = false;
                    $all_check_in_data[$k]['half_day'] = $halfDaysFlag;
                    foreach ($final_approve_leave as $l_data) {


                            if ($v['check_in_time']->format('Y-m-d') === $l_data[0]['leave_date']->format('Y-m-d') && $l_data[0]['half_day']) {
                                $halfDaysFlag = true;
                                $all_check_in_data[$k]['half_day'] = $halfDaysFlag;
                            }

                    }
                }


             $staffing_data = $this->checkInRepo->getStaffingHours($emp_id, $startDate, $endDate);

            $defaultBreakTime = $this->companyRepo->getDefaultBreakTime();
            $defaultBreak = strtotime(date('H:i:s', mktime(0,intval($defaultBreakTime))));


            $batch_data = $this->locationRepo->getUserBatch($emp_id);
            if (count($batch_data) > 0) {
                $batch_data = $batch_data[0];
                if (isset($batch_data['office_start_time']) && isset($batch_data['office_end_time'])) {
                    $start = $batch_data['office_start_time']->format('h:i A');
                    $end = $batch_data['office_end_time']->format('h:i A');
                    $diff = strtotime($end) - strtotime($start);
                    $batch_staffing = date('H:i:s', strtotime('00:00:00') + ($diff - 3600));
                    //$batch_staffing = date('H:i:s', strtotime('00:00:00') + ($diff - ($defaultBreakTime*60)));

                }
            }



            for ($i = 0; $i < count($all_check_in_data); $i++) {
                if (!empty($all_check_in_data[$i]['check_out_time'])) {
                    if(!$all_check_in_data[$i]['half_day']) {
                        if (strtotime($staffing_data[$i]['break']) > $defaultBreak) {
                            $initial_staffing = date_create_from_format('H:i:s', $staffing_data[$i]['initialStaffing']);
                            $total_breaktime = date_create_from_format('H:i:s', $staffing_data[$i]['break']);
                            $all_check_in_data[$i]['staffing_hours'] = date_diff($initial_staffing, $total_breaktime)->format('%h:%i:%s');

//                        $all_check_in_data[$i]['late_hours']= date('H:i:s', strtotime('00:00:00')+(strtotime($all_check_in_data[$i]['staffing_hours']) - strtotime($batch_staffing)));
                            $tempSecs = (strtotime($all_check_in_data[$i]['staffing_hours']) - strtotime($batch_staffing));

                            if ($tempSecs < 0) {
                                $lateHours = ceil(abs($tempSecs) / 60);
                                if ($lateHours < 60) {
                                    $all_check_in_data[$i]['red_mark'] = 1;
                                }else {
                                    $all_check_in_data[$i]['red_mark'] = 2;
                                }
                            }

                        } else {

                            $diff = date('H:i:s', $defaultBreak);
                            $initial_staffing = date_create_from_format('H:i:s', $staffing_data[$i]['initialStaffing']);
                            $defalubrk_time = date_create_from_format('H:i:s', $diff);
                            $all_check_in_data[$i]['staffing_hours'] = date_diff($initial_staffing, $defalubrk_time)->format('%h:%i:%s');

                            $tempSecs = (strtotime($all_check_in_data[$i]['staffing_hours']) - strtotime($batch_staffing));

                            if ($tempSecs < 0) {
                                $lateHours = ceil(abs($tempSecs) / 60);
                                if ($lateHours < 60) {
                                    $all_check_in_data[$i]['red_mark'] = 1;
                                }else {
                                    $all_check_in_data[$i]['red_mark'] = 2;
                                }
                            }
                        }
                    }
                    else{
                        if (strtotime($staffing_data[$i]['break']) > $defaultBreak) {
                            $initial_staffing = date_create_from_format('H:i:s', $staffing_data[$i]['initialStaffing']);
                            $total_breaktime = date_create_from_format('H:i:s', $staffing_data[$i]['break']);
                            $all_check_in_data[$i]['staffing_hours'] = date_diff($initial_staffing, $total_breaktime)->format('%h:%i:%s');
                        }else{
                        $diff = date('H:i:s', $defaultBreak);
                        $initial_staffing = date_create_from_format('H:i:s', $staffing_data[$i]['initialStaffing']);
                        $defalubrk_time = date_create_from_format('H:i:s', $diff);
                        $all_check_in_data[$i]['staffing_hours'] = date_diff($initial_staffing, $defalubrk_time)->format('%h:%i:%s');
                        }
                    }
                }
            }
//            return $all_check_in_data;

//            for ($i = 0; $i < count($all_check_in_data); $i++) {
//                if ($staffing_data[$i]['staffing'] == null && !empty($all_check_in_data[$i]['check_out_time'])) {
//                    $all_check_in_data[$i]['staffing_hours'] = date_diff($all_check_in_data[$i]['check_out_time'], $all_check_in_data[$i]['check_in_time'])->format('%H:%I:%S');
//                } else {
//                    $all_check_in_data[$i]['staffing_hours'] = $staffing_data[$i]['staffing'];
//                }
//            }
            for ($i = 0; $i < count($all_check_in_data); $i++) {
                $timestamp = 0;
                foreach ($all_check_in_data[$i]['breaks'] as $break) {
                    if (!empty($break['break_out_time'])) {
                        $timestamp += strtotime($break['break_out_time']->format('H:i:s')) - strtotime($break['break_in_time']->format('H:i:s'));
                        $all_check_in_data[$i]['total_break_time'] = date('H:i', strtotime('00:00:00') + $timestamp);
                    }

                }
            }

            if (!empty($all_check_in_data)) {
                return response()->json(['all_check_in_data' => $all_check_in_data, 'final_approve_leave' => $final_approve_leave]);
//                return response()->json($all_check_in_data);
            }
        }
        return response()->json(null);
    }

    function updateAttendance(Request $request)
    {
        $data = $request->all();
        $ip = $this->getIp();
        $verify_checkin = $this->checkInRepo->verifyTodayCheckin($data['emp_id'], $data['check_in_time']);
        if (count($verify_checkin) > 0 && isset($data['check_in_id'])) {
            $data['check_in_id'] = $this->checkInRepo->CheckInOfId($data['check_in_id']);
            $data['check_in_time'] = new \DateTime($data['check_in_time']);
            $res = $this->checkLateCheckIn($data['emp_id'], $data['check_in_time']);
            if ($res) {
                if ($res != 'no_data') {
                    $data['is_late'] = $res['is_late'];
                    $data['late_minutes'] = $res['late_minutes'];
                }
            } else {
                $data['is_late'] = null;
                $data['late_minutes'] = null;
            }
            //            $batch_data = $this->locationRepo->getUserBatch($data['emp_id']);
            //            if ($batch_data['late_mark_after_minute']) {
            //                $t1 = strtotime($data['check_in_time']->format('H:i:s'));
            //                $t2 = strtotime($batch_data['office_start_time']->format('H:i:s'));
            //                $min_diff = floor(($t1 - $t2) / 60);
            //                if ((int)$min_diff > (int)$batch_data['late_mark_after_minute']) {
            //                    // late
            //                    $data['is_late'] = 1;
            //                    $data['late_minutes'] = (int)$min_diff - (int)$batch_data['late_mark_after_minute'];
            //                }
            //            }
            $data['emp_id'] = $this->userRepo->UserOfId($data['emp_id']);
            if (isset($data['check_out_time'])) {
                $data['check_out_time'] = new \DateTime($data['check_out_time']);
                if (isset($data['check_out_emp_id'])) {
                    $data['check_out_emp_id'] = $this->userRepo->UserOfId($data['check_out_emp_id']);
                    $data['check_out_ip'] = $ip;
                } else {
                    unset($data['check_out_emp_id']);
                }
            }
            $this->checkInRepo->update($data['check_in_id'], $data);
            foreach ($data['breaks'] as $row) {
                if (isset($row['break_in_time'])) {
                    $row['break_in_time'] = new \DateTime($row['break_in_time']);
                    $row['check_in_id'] = $data['check_in_id'];
                    if (isset($row['break_out_time'])) {
                        $row['break_out_time'] = new \DateTime($row['break_out_time']);
                    }
                    if (isset($row['id'])) {
                        $row['emp_id'] = $data['emp_id'];
                        $row['id'] = $this->breaksRepo->BreaksOfId($row['id']);
                        $this->breaksRepo->update($row['id'], $row);
                        //update
                    } else {
                        $row['emp_id'] = $data['emp_id'];
                        $prepare_data = $this->breaksRepo->prepareData($row);
                        $this->breaksRepo->create($prepare_data);
                        //insert
                    }
                }
            }
            if (count($data['comments']) > 0) {
                foreach ($data['comments'] as $cmt) {
                    $cmt['emp_id'] = $data['emp_id'];
                    $cmt['check_in_id'] = $data['check_in_id'];
                    $cmt['id'] = $this->commentsRepo->CommentsOfId($cmt['id']);
                    $this->commentsRepo->update($cmt['id'], $cmt);
                }
            }

            if (count($data['remove_break_ids']) > 0) {
                foreach ($data['remove_break_ids'] as $break_id) {
                    $id = $this->breaksRepo->BreaksOfId($break_id);
                    $this->breaksRepo->delete($id);
                }
            }
            return response()->json('updated');
        } else {
            //insert
            if (isset($data['check_in_time'])) {
                $data['check_in_time'] = new \DateTime($data['check_in_time']);
                $res = $this->checkLateCheckIn($data['emp_id'], $data['check_in_time']);
                if ($res) {
                    if ($res != 'no_data') {
                        $data['is_late'] = $res['is_late'];
                        $data['late_minutes'] = $res['late_minutes'];
                    }
                }
                //                $batch_data = $this->locationRepo->getUserBatch($data['emp_id']);
                //                if ($batch_data['late_mark_after_minute']) {
                //                    $t1 = strtotime($data['check_in_time']->format('H:i:s'));
                //                    $t2 = strtotime($batch_data['office_start_time']->format('H:i:s'));
                //                    $min_diff = floor(($t1 - $t2) / 60);
                //                    if ((int)$min_diff > (int)$batch_data['late_mark_after_minute']) {
                //                        // late
                //                        $data['is_late'] = 1;
                //                        $data['late_minutes'] = (int)$min_diff - (int)$batch_data['late_mark_after_minute'];
                //                    }
                //                }
                $data['check_in_ip'] = $ip;
                if (isset($data['check_out_time'])) {
                    $data['check_out_time'] = new \DateTime($data['check_out_time']);
                    $data['check_out_ip'] = $ip;
                    $data['check_out_emp_id'] = $this->userRepo->UserOfId($data['emp_id']);
                }
                $data['emp_id'] = $this->userRepo->UserOfId($data['emp_id']);
                $prepareData = $this->checkInRepo->prepareData($data);
                $check_in_id = $this->checkInRepo->create($prepareData);

                $check_in_id = $this->checkInRepo->CheckInOfId($check_in_id);
                foreach ($data['breaks'] as $row) {
                    if (isset($row['break_in_time'])) {
                        $row['emp_id'] = $data['emp_id'];
                        $row['check_in_id'] = $check_in_id;
                        $row['break_in_time'] = new \DateTime($row['break_in_time']);
                        if (isset($row['break_out_time'])) {
                            $row['break_out_time'] = new \DateTime($row['break_out_time']);
                        }
                        $prepare_data = $this->breaksRepo->prepareData($row);
                        $this->breaksRepo->create($prepare_data);
                    }
                }
            }
            return response()->json('inserted');
        }
    }

    function checkLateCheckIn($emp_id, $check_in_time)
    {
        $batch_data = $this->locationRepo->getUserBatch($emp_id);
        if (count($batch_data) > 0) {
            $batch_data = $batch_data[0];
            if ($batch_data['late_mark_after_minute']) {
                $t1 = strtotime($check_in_time->format('H:i:s'));
                $t2 = strtotime($batch_data['office_start_time']->format('H:i:s'));
                $min_diff = floor(($t1 - $t2) / 60);
                if ((int)$min_diff > (int)$batch_data['late_mark_after_minute']) {
                    // late
                    $data['is_late'] = 1;
                    $data['late_minutes'] = (int)$min_diff - (int)$batch_data['late_mark_after_minute'];
                    return $data;
                } else {
                    return false;
                }
            }
        }
        return 'no_data';
    }

    public function getAttendanceDashboard()
    {
        $data = $this->userRepo->getAttendanceDashboard(Carbon::today(), Carbon::today()->addHours('23')->addMinutes('59')->addSeconds('59'));
        return response()->json($data);
    }

    function validateDate($date, $format = 'd-m-Y')
    {
        $d = \DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }

    public function sendAttendanceReport(Request $request)
    {
        $date = '';
        if (isset($request->date)) {
            if ($this->validateDate($request->date)) {
                $date = Carbon::parse($request->date);
            } else {
                return 'enter valid date';
            }
        } else {
            $date = Carbon::today();
        }
        if ($date) {
            $data = $this->userRepo->getStaffingDataByDate($date);
        }
        /*else{
            $data = $this->userRepo->getTodayStaffingData();
        }*/
        $newArr = [];
        $present = 0;
        $absent = 0;
        foreach ($data as $row) {
            $info = [];
            $info['name'] = $row['firstname'] . ' ' . $row['lastname'];
            if (!empty($row['user_check_in'])) {
                $present++;
                $checkInData = $row['user_check_in'][0];
                $info['entry_time'] = $checkInData['check_in_time']->format('h:i A');
                if (!empty($checkInData['breaks'])) {
                    $timestamp = 0;
                    foreach ($checkInData['breaks'] as $break) {
                        if (!empty($break['break_out_time'])) {
                            $timestamp += strtotime($break['break_out_time']->format('H:i:s')) - strtotime($break['break_in_time']->format('H:i:s'));
                        }
                    }
                    $diff = date('H:i', strtotime('00:00:00') + $timestamp);
                    $info['break_time'] = $diff;
                }
                if (!empty($checkInData['check_out_time'])) {
                    $info['exit_time'] = $checkInData['check_out_time']->format('h:i A');
                    $mainDiff = strtotime($info['exit_time']) - strtotime($info['entry_time']);
                    if (isset($info['break_time'])) {
                        $info['working_time'] = date('H:i', strtotime('00:00:00') + ($mainDiff - $timestamp));
                    } else {
                        $info['break_time'] = '00:00';
                        $info['working_time'] = date('H:i', strtotime('00:00:00') + $mainDiff);
                    }
                    // check if staffing hours are completed

                    if (isset($row['location']['office_start_time']) && isset($row['location']['office_end_time'])) {
                        $start = $row['location']['office_start_time']->format('h:i A');
                        $end = $row['location']['office_end_time']->format('h:i A');
                        $diff = strtotime('00:00:00') + (strtotime($end) - strtotime($start) - 3600);
                        if (strtotime($info['working_time']) < $diff) {
                            $info['incomplete'] = true;
                        }
                    } else {
                        $info['incomplete'] = false;
                    }
                    // end
                } else {
                    $info['incomplete'] = false;
                }
            } else {
                $absent++;
            }


            if ($row['department']) {
                if (!isset($newArr[$row['department']['id']]['dep_name'])) {
                    $newArr['all'][$row['department']['id']]['dep_name'] = $row['department']['name'];
                }
                if (isset($info['entry_time'])) {
                    $newArr['all'][$row['department']['id']]['users'][] = $info;
                }
            } else {
                if (isset($info['entry_time'])) {
                    $newArr['other'][] = $info;
                }
            }
        }

        $newArr['date'] = $date->format('d/m/Y');
        $newArr['present'] = $present;
        $newArr['absent'] = $absent;
        $html = view('report.report', compact('newArr'))->render();
        if (app('App\Http\Controllers\EmailController')->sendReport($html)) {
            return response()->json('success');
        }
    }

    function getReportDataOfAllUserCommon(Request $request)
    {

        $month = $request->month;
        $year = $request->year;
        $currentTime = new \DateTime($request->month_year);
        $startDate = $currentTime->format('Y-m-01 00:00:00');
        $days = cal_days_in_month(CAL_GREGORIAN, $currentTime->format('m'), $currentTime->format('y'));
        $endDate = $currentTime->format('Y-m-' . $days . ' 23:23:59');

        $users = $this->userRepo->getAttendanceReportDataOfAllUser($startDate, $endDate);
        $holidays = $this->holidayRepo->getHolidaysDashboard();

        //days
        $day = [];
        for ($d = 1; $d <= $days; $d++) {

            $ds=date('D', strtotime($d.'-'.$month.'-'.$year));
            $day[]= $ds;
        }

        $main = [];
        for ($i = 0; $i < count($users); $i++) {
            $main[$i] = [];

            for ($j = 0; $j < count($day); $j++) {
                $leaves = $users[$i]['approved_leave'];
                $checkin = $users[$i]['user_check_in'];
                $workingDays = [];
                if ($users[$i]['location']) {
                    $workingDays = explode(",", $users[$i]['location']['working_days']);
                }


                $is_leave = false;
                $is_checkin = false;
                $is_holiday = false;


                //leave
                if (count($leaves) > 0) {
                    foreach ($leaves as $l_k => $l_v) {

                        if ($l_v['leave_date']->format('d') == ($j + 1) && $l_v['status'] == "Accept") {
                            $main[$i][$j] = $l_v['leave_type']['leavetype'];
                            if ($l_v['half_day']) {
                                $main[$i][$j] = 'HDL' . '-' . $l_v['leave_type']['leavetype'];
                            }

                            $is_leave = true;
                        }
                    }
                }

                //checkin
                if (count($checkin) > 0 && !$is_leave) {
                    foreach ($checkin as $ch_k => $ch_v) {
                        if ($ch_v['check_in_time']->format('d') == ($j + 1)) {
                            $main[$i][$j] = 'P';
                            $is_checkin = true;
                        }
                    }
                }

                //holiday
                if (count($holidays) > 0 && !$is_leave && !$is_checkin) {
                    foreach ($holidays as $hol_k => $hol_v) {
                        if ($hol_v['start_date']->format('m') ===  $month && $hol_v['start_date']->format('d') == ($j + 1)) {
                            $main[$i][$j] = 'H';
                            $is_holiday = true;
                        }
                    }
                }

                $is_custom_holiday = false;
                if (!$is_leave && !$is_checkin && !$is_holiday && count($workingDays) > 0) {
                    if (!in_array(strtolower($day[$j]), $workingDays)) {
                        $main[$i][$j] = 'H';
                        $is_custom_holiday = true;

                    }
                }

                if (!$is_leave && !$is_checkin && !$is_holiday && !$is_custom_holiday) {
                    $main[$i][$j] = 'A';
                }
            }
        }

        $finalData = [];
        $finalData['users'] = $users;
        $finalData['days'] = $day;
        $finalData['main'] = $main;
        return $finalData;
    }

    function getReportDataOfAllUser(Request $request)
    {
        $finalData = $this->getReportDataOfAllUserCommon($request);
        return response()->json($finalData);
    }


    function exportReportToExcel(Request $request)
    {
        $data = $this->getReportDataOfAllUserCommon($request);

        $arr = [];
        for ($i = 1; $i <= count($data['days']); $i++) {
            $arr[] = $i;
        }
        array_unshift($arr, ' ');
        $arr[] = ' ';
        $myarray = [];
        $days = $data['days'];

        array_unshift($days, 'Username');
        $days[] = 'total';
        foreach ($data['main'] as $k_i => $i) {
            $myarray[$k_i] = [];
            foreach ($i as $k_j => $j) {
                if ($k_j == 0) {
                    $myarray[$k_i][$k_j] = $data['users'][$k_i]['firstname'] . ' ' . $data['users'][$k_i]['lastname'];
                    $myarray[$k_i][$k_j + 1] = $data['main'][$k_i][$k_j];
                }
                $myarray[$k_i][$k_j + 1] = $data['main'][$k_i][$k_j];
            }
            $myarray[$k_i][$k_j + 2] = $data['users'][$k_i]['user_check_in'] ? count($data['users'][$k_i]['user_check_in']) : '0';

        }
        array_unshift($myarray, [$days]);

        $file_name = "user_" . str_random(20) . ".xlsx";

        $res = \Excel::store(new UsersImport(['data' => $myarray, 'days' => $arr]), "/public/upload/excel/" . $file_name);

        return response()->json($file_name, 200);
    }
}
 
 
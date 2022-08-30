<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use mysql_xdevapi\Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class CompanyProjectController extends Controller
{
    function getAllProjectsDatatableBaTl(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $emp_id = JWTAuth::user();
        $allRecords = $this->companyProjectRepo->countAllProjectsBaTl();
        $filterRecords = $this->companyProjectRepo->countFilteredProjectsBaTl($search);
        $data = $this->companyProjectRepo->getAllProjectsDatatableBaTl($order, $column_name, $search, $start, $length, $emp_id);

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }
    function getAllClosedProjectsDatatableBaTl(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $emp_id = JWTAuth::user();
        $allRecords = $this->companyProjectRepo->countAllClosedProjectsBaTl();
        $filterRecords = $this->companyProjectRepo->countFilteredClosedProjectsBaTl($search);
        $data = $this->companyProjectRepo->getAllClosedProjectsDatatableBaTl($order, $column_name, $search, $start, $length, $emp_id);

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    function getOwnProjectsDatatableBaTl(Request $request)
    {

        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $emp_id = JWTAuth::user();
        $allRecords = $this->companyProjectRepo->countOwnProjectsBaTl($emp_id);
        $filterRecords = $this->companyProjectRepo->countFilteredOwnProjectsBaTl($search, $emp_id);
        $data = $this->companyProjectRepo->getOwnProjectsDatatableBaTl($order, $column_name, $search, $start, $length, $emp_id);

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    function getProjectDataBaTl(Request $request)
    {
        $project_id = $request->id;
        $data = $this->companyProjectBaRepo->getProjectDataBaTl($project_id);
        return response()->json($data);
    }

    function saveProject(Request $request)
    {
        $data = $request->all();
        $notify = [];
        $currentUser = JWTAuth::user()->getId();
        $createdByUser = $data['created_by'];
        $currentUserObj = $this->userRepo->UserOfId($currentUser);

        $data['created_by'] = $this->userRepo->UserOfId($data['created_by']);
        // $authid = JWTAuth::user()->getId();
        $data['updated_by'] = $this->userRepo->UserOfId($currentUser);

        if ($data['on_hold_changed']) {
            if ($data['on_hold']) {
                $data['flag'] = $this->optionRepo->OptionOfId(138);
            } else {
                $data['flag'] = $this->optionRepo->OptionOfId(132);
            }
        }

        if (isset($data['deadline']) && $data['deadline']) {
            try{
                $data['deadline'] = new \DateTime($data['deadline']);
            }
            catch (Exception $exception)
            {
                $data['deadline'] = null;
            }

        }

        if ((int)$data['extra_hours']) {
            $data['est_time'] = (int)$data['est_time'] + (int)$data['extra_hours'];
        }

        $assigned_ba = $data['assigned_ba'];
        if (count($assigned_ba) > 0) {
            $data['is_own'] = 0;
        } else {
            $data['is_own'] = 1;
        }

        if ($data['id']) {
            $project_id = $this->companyProjectRepo->CompanyProjectOfId($data['id']);
            $updateProject = $this->companyProjectRepo->update($project_id, $data);
            // $from_emp = $updateProject->getCreatedBy()->getFirstname() . ' ' . $updateProject->getCreatedBy()->getLastname();
            $from_emp = JWTAuth::user()->getFirstname() . ' ' . JWTAuth::user()->getLastname();
            if ($updateProject) {
                if ($currentUser == $createdByUser) {
                    $allBaOfProject = $this->companyProjectBaRepo->getAllBaOfProject($updateProject->getId());
                    foreach ($allBaOfProject as $row) {
                        $isFound = false;
                        foreach ($assigned_ba as $ba) {
                            if ($ba == $row['emp_id']) {
                                $isFound = true;
                            }
                        }
                        if (!$isFound) {
                            // delete that ba project row
                            $baProjectOfId = $this->companyProjectBaRepo->CompanyProjectBaOfId($row['id']);
                            $this->companyProjectBaRepo->delete($baProjectOfId);
                            // end

                            // delete conversation id
                            $conv_id = $this->companyProjectConvRepo->getConvId($data['id'], $row['emp_id']);
                            if (!empty($conv_id)) {
                                $c_id = $conv_id[0]['id'];
                                $c_id = $this->companyProjectConvRepo->CompanyProjectConvOfId($c_id);
                                $this->companyProjectConvRepo->delete($c_id);
                            }
                            // end
                        }
                    }

                    foreach ($assigned_ba as $ba) {
                        $exists = false;
                        foreach ($allBaOfProject as $row) {
                            if ($ba == $row['emp_id']) {
                                $exists = true;
                            }
                        }
                        if (!$exists) {
                            $jrData['company_project_id'] = $project_id;
                            $jrData['emp_id'] = $this->userRepo->UserOfId($ba);
                            if ($updateProject->getIsStarted()) {
                                $jrData['flag'] = $this->optionRepo->OptionOfId(133);
                                $jrData['ba_tl_flag'] = $this->optionRepo->OptionOfId(133);
                            } else {
                                $jrData['flag'] = $this->optionRepo->OptionOfId(132);
                                $jrData['ba_tl_flag'] = $this->optionRepo->OptionOfId(132);
                            }
                            $prepareData = $this->companyProjectBaRepo->prepareData($jrData);
                            $create = $this->companyProjectBaRepo->create($prepareData);

                            $conv_data['company_project_id'] = $project_id;
                            $conv_data['user1'] = $currentUserObj;
                            $conv_data['user2'] = $jrData['emp_id'];
                            $prepareData = $this->companyProjectConvRepo->prepareData($conv_data);
                            $create = $this->companyProjectConvRepo->create($prepareData);
                            if ($create) {
                                $title = 'Project Assigned';
                                $details = $from_emp . ' assigned you project ' . $updateProject->getProjectName();
                                $notify[] = [
                                    'channel_name' => 'project-update-' . $ba,
                                    'from_emp' => $from_emp,
                                    'title' => 'Project Assigned',
                                    'details' => $from_emp . ' assigned you project ' . $updateProject->getProjectName()
                                ];
                                $prepareNotify['emp_id'] = $this->userRepo->UserOfId($ba);
                                $prepareNotify['from_emp'] = $this->userRepo->UserOfId($currentUser);
                                $prepareNotify['title'] = $title;
                                $prepareNotify['details'] = $details;
                                $prepare = $this->activityBoxRepo->prepareData($prepareNotify);
                                $this->activityBoxRepo->create($prepare);
                            }
                        }
                    }
                }
                //--------recordtiming-----------
                $ins_timing = [];

                if (isset($data['record_timing']) && $data['record_timing'] != null) {
                    foreach ($data['record_timing'] as $val) {
                        $ins_timing['emp_id'] = $this->userRepo->UserOfId($val['emp_id']);
                        $ins_timing['created_by'] = $this->userRepo->UserOfId(JWTAuth::user()->getId());
                        $ins_timing['company_project_id'] = $this->companyProjectRepo->CompanyProjectOfId($data['id']);
                        $ins_timing['record_date'] = Carbon::today();
                        $ins_timing['record_hours'] = $val['record_hours'];
                        $ins_timing['redmark_comment'] = $val['redmark_comment'];
                        $prepare_data = $this->companyProjectEmpTimingsRepo->prepareData($ins_timing);
                        $this->companyProjectEmpTimingsRepo->create($prepare_data);
                    }

                    $select_record = $this->companyProjectEmpTimingsRepo->selectRecordByProjectID($data['id']);
                    $project_details = $this->companyProjectRepo->getProjectById($data['id']);
                    $ba_repo = $this->companyProjectBaRepo->getAllBaOfProject($data['id']);
                    //                    $ba_mail = $this->approvedProjectRepo->getBaMailById($project_details['assigned_ba']);
                    $ba_mail = $project_details['created_by']['email'];

                    // BA : 1
                    $email_arr = [];
                    if ($currentUser == $updateProject->getCreatedBy()->getId()) {
                        if ($updateProject->getIsTl()) {
                            // when project is created and updated by tl

                            $email_arr[] = JWTAuth::user()->getEmail();
                        } else {
                            // user is jr ba, so send mail to all tl
                            $allTLMail = $this->companyProjectRepo->getAllTLEmail();
                            foreach ($allTLMail as $mail) {
                                $email_arr[] = $mail['email'];
                            }
                        }
                    } else {
                        // user is assigned ba
                        $email_arr[] = $updateProject->getCreatedBy()->getEmail();
                    }

                    if (isset($data['threshold_limit2']) && $data['threshold_limit2'] != null && ($data['threshold_limit2'] > $data['threshold_limit1']) && (int)$select_record['sum'] >= $data['threshold_limit2']) {
                        $email_arr[] = 'webdeveloper1011@gmail.com';
                        $mail_to = $email_arr;
                        $subject = 'Threshold Limit Exceed';
                        $view_array = [];
                        $view_array['project_name'] = $project_details['project_name'];
                        $view_array['threshold_limit2'] = $data['threshold_limit2'];
                        $view_array['total_hours'] = (float)$select_record['sum'];
                        $html = view('approved_project.limitexceed', compact('view_array'))->render();
                        app('App\Http\Controllers\EmailController')->thresholdSendMail($mail_to, $subject, $html);
                        //                        }
                    } else {
                        if (isset($data['threshold_limit1']) && $data['threshold_limit1'] != null) {
                            if ((int)$select_record['sum'] >= $data['threshold_limit1']) {

                                //                                Log::info('t1');
                                $mail_to = $email_arr;
                                $subject = 'Threshold Limit Exceed';
                                $view_array = [];
                                $view_array['project_name'] = $project_details['project_name'];
                                $view_array['threshold_limit1'] = $data['threshold_limit1'];
                                $view_array['total_hours'] = (float)$select_record['sum'];
                                $html = view('approved_project.limitexceedmain', compact('view_array'))->render();
                                app('App\Http\Controllers\EmailController')->thresholdSendMail($mail_to, $subject, $html);
                            }
                        }
                    }
                }
                //-------end----------------------
                return response()->json(['status' => 'updated', 'data' => $notify]);
            }
            return response()->json('error');
        } else {
            $data['flag'] = $this->optionRepo->OptionOfId(132);

            $preparedData = $this->companyProjectRepo->prepareData($data);
            $project_create = $this->companyProjectRepo->create($preparedData);
            $from_emp = $project_create->getCreatedBy()->getFirstname() . ' ' . $project_create->getCreatedBy()->getLastname();
            if ($project_create) {
                if (count($data['assigned_ba']) > 0) {
                    foreach ($data['assigned_ba'] as $id) {
                        $baData['company_project_id'] = $this->companyProjectRepo->CompanyProjectOfId($project_create->getId());
                        $baData['emp_id'] = $this->userRepo->UserOfId($id);
                        $baData['flag'] = $this->optionRepo->OptionOfId(132);
                        $baData['ba_tl_flag'] = $this->optionRepo->OptionOfId(132);
                        $prepare = $this->companyProjectBaRepo->prepareData($baData);
                        $createBa = $this->companyProjectBaRepo->create($prepare);

                        $title = 'Project Assigned';
                        $details = $from_emp . ' assigned you ' . $project_create->getProjectName();
                        $notify[] = [
                            'channel_name' => 'project-update-' . $createBa->getEmpId()->getId(),
                            'from_emp' => $from_emp,
                            'title' => $title,
                            'details' => $details
                        ];

                        // create conversation id
                        $convData['company_project_id'] = $baData['company_project_id'];
                        $convData['user1'] = $data['created_by'];
                        $convData['user2'] = $baData['emp_id'];
                        $prepareConv = $this->companyProjectConvRepo->prepareData($convData);
                        $this->companyProjectConvRepo->create($prepareConv);
                        // end

                        // create activity box data
                        $prepareNotify['emp_id'] = $baData['emp_id'];
                        $prepareNotify['from_emp'] = $this->userRepo->UserOfId($currentUser);
                        $prepareNotify['title'] = $title;
                        $prepareNotify['details'] = $details;
                        $prepare = $this->activityBoxRepo->prepareData($prepareNotify);
                        $this->activityBoxRepo->create($prepare);
                        // end
                    }
                    return response()->json(['status' => 'created', 'data' => $notify]);
                }
                return response()->json(['status' => 'created']);
            }
            return response()->json(['status' => 'error']);
        }
    }
    function saveProjectBa(Request $request)
    {
        $data = $request->all();
        $notify = [];
        $currentUser = JWTAuth::user()->getId();
        $createdByUser = $data['created_by'];
        $currentUserObj = $this->userRepo->UserOfId($currentUser);

        $data['created_by'] = $this->userRepo->UserOfId($data['created_by']);
        // $authid = JWTAuth::user()->getId();
        $data['updated_by'] = $this->userRepo->UserOfId($currentUser);

        if ($data['on_hold_changed']) {
            if ($data['on_hold']) {
                $data['flag'] = $this->optionRepo->OptionOfId(138);
            } else {
                $data['flag'] = $this->optionRepo->OptionOfId(132);
            }
        }

        if (isset($data['deadline']) && $data['deadline']) {
            $data['deadline'] = new \DateTime($data['deadline']);
        }

        if ((int)$data['extra_hours']) {
            $data['est_time'] = (int)$data['est_time'] + (int)$data['extra_hours'];
        }

        $assigned_ba = $data['assigned_ba'];
        if (count($assigned_ba) > 0) {
            $data['is_own'] = 0;
        } else {
            $data['is_own'] = 1;
        }

        if ($data['id']) {
            $project_id = $this->companyProjectRepo->CompanyProjectOfId($data['id']);
            $updateProject = $this->companyProjectRepo->update($project_id, $data);
            // $from_emp = $updateProject->getCreatedBy()->getFirstname() . ' ' . $updateProject->getCreatedBy()->getLastname();
            $from_emp = JWTAuth::user()->getFirstname() . ' ' . JWTAuth::user()->getLastname();
            if ($updateProject) {
                if ($currentUser == $createdByUser) {
                    $allBaOfProject = $this->companyProjectBaRepo->getAllBaOfProject($updateProject->getId());
                    foreach ($allBaOfProject as $row) {
                        $isFound = false;
                        foreach ($assigned_ba as $ba) {
                            if ($ba == $row['emp_id']) {
                                $isFound = true;
                            }
                        }
                        if (!$isFound) {
                            // delete that ba project row
                            $baProjectOfId = $this->companyProjectBaRepo->CompanyProjectBaOfId($row['id']);
                            $this->companyProjectBaRepo->delete($baProjectOfId);
                            // end

                            // delete conversation id
//                            $conv_id = $this->companyProjectConvRepo->getConvId($data['id'], $row['emp_id']);
//                            if (!empty($conv_id)) {
//                                $c_id = $conv_id[0]['id'];
//                                $c_id = $this->companyProjectConvRepo->CompanyProjectConvOfId($c_id);
//                                $this->companyProjectConvRepo->delete($c_id);
//                            }
                            // end
                        }
                    }

                    foreach ($assigned_ba as $ba) {
                        $exists = false;
                        foreach ($allBaOfProject as $row) {
                            if ($ba == $row['emp_id']) {
                                $exists = true;
                            }
                        }
                        if (!$exists) {
                            $jrData['company_project_id'] = $project_id;
                            $jrData['emp_id'] = $this->userRepo->UserOfId($ba);
                            if ($updateProject->getIsStarted()) {
                                $jrData['flag'] = $this->optionRepo->OptionOfId(133);
                                $jrData['ba_tl_flag'] = $this->optionRepo->OptionOfId(133);
                            } else {
                                $jrData['flag'] = $this->optionRepo->OptionOfId(132);
                                $jrData['ba_tl_flag'] = $this->optionRepo->OptionOfId(132);
                            }
                            $prepareData = $this->companyProjectBaRepo->prepareData($jrData);
                            $create = $this->companyProjectBaRepo->create($prepareData);
//
//                            $conv_data['company_project_id'] = $project_id;
//                            $conv_data['user1'] = $currentUserObj;
//                            $conv_data['user2'] = $jrData['emp_id'];
//                            $prepareData = $this->companyProjectConvRepo->prepareData($conv_data);
//                            $create = $this->companyProjectConvRepo->create($prepareData);
//                            if ($create) {
                                $title = 'Project Assigned';
                                $details = $from_emp . ' assigned you project ' . $updateProject->getProjectName();
                                $notify[] = [
                                    'channel_name' => 'project-update-' . $ba,
                                    'from_emp' => $from_emp,
                                    'title' => 'Project Assigned',
                                    'details' => $from_emp . ' assigned you project ' . $updateProject->getProjectName()
                                ];
                                $prepareNotify['emp_id'] = $this->userRepo->UserOfId($ba);
                                $prepareNotify['from_emp'] = $this->userRepo->UserOfId($currentUser);
                                $prepareNotify['title'] = $title;
                                $prepareNotify['details'] = $details;
                                $prepare = $this->activityBoxRepo->prepareData($prepareNotify);
                                $this->activityBoxRepo->create($prepare);
//                            }
                        }
                    }
                }
                //--------recordtiming-----------
                $ins_timing = [];

                if (isset($data['record_timing']) && $data['record_timing'] != null) {
                    foreach ($data['record_timing'] as $val) {
                        $ins_timing['emp_id'] = $this->userRepo->UserOfId($val['emp_id']);
                        $ins_timing['created_by'] = $this->userRepo->UserOfId(JWTAuth::user()->getId());
                        $ins_timing['company_project_id'] = $this->companyProjectRepo->CompanyProjectOfId($data['id']);
                        $ins_timing['record_date'] = Carbon::today();
                        $ins_timing['record_hours'] = $val['record_hours'];
                        $ins_timing['redmark_comment'] = $val['redmark_comment'];
                        $prepare_data = $this->companyProjectEmpTimingsRepo->prepareData($ins_timing);
                        $this->companyProjectEmpTimingsRepo->create($prepare_data);
                    }

                    $select_record = $this->companyProjectEmpTimingsRepo->selectRecordByProjectID($data['id']);
                    $project_details = $this->companyProjectRepo->getProjectById($data['id']);
                    $ba_repo = $this->companyProjectBaRepo->getAllBaOfProject($data['id']);
                    //                    $ba_mail = $this->approvedProjectRepo->getBaMailById($project_details['assigned_ba']);
                    $ba_mail = $project_details['created_by']['email'];

                    // BA : 1
                    $email_arr = [];
                    if ($currentUser == $updateProject->getCreatedBy()->getId()) {
                        if ($updateProject->getIsTl()) {
                            // when project is created and updated by tl

                            $email_arr[] = JWTAuth::user()->getEmail();
                        } else {
                            // user is jr ba, so send mail to all tl
                            $allTLMail = $this->companyProjectRepo->getAllTLEmail();
                            foreach ($allTLMail as $mail) {
                                $email_arr[] = $mail['email'];
                            }
                        }
                    } else {
                        // user is assigned ba
                        $email_arr[] = $updateProject->getCreatedBy()->getEmail();
                    }

                    if (isset($data['threshold_limit2']) && $data['threshold_limit2'] != null && ($data['threshold_limit2'] > $data['threshold_limit1']) && (int)$select_record['sum'] >= $data['threshold_limit2']) {
                        $email_arr[] = 'webdeveloper1011@gmail.com';
                        $mail_to = $email_arr;
                        $subject = 'Threshold Limit Exceed';
                        $view_array = [];
                        $view_array['project_name'] = $project_details['project_name'];
                        $view_array['threshold_limit2'] = $data['threshold_limit2'];
                        $view_array['total_hours'] = (float)$select_record['sum'];
                        $html = view('approved_project.limitexceed', compact('view_array'))->render();
                        app('App\Http\Controllers\EmailController')->thresholdSendMail($mail_to, $subject, $html);
                        //                        }
                    } else {
                        if (isset($data['threshold_limit1']) && $data['threshold_limit1'] != null) {
                            if ((int)$select_record['sum'] >= $data['threshold_limit1']) {

                                //                                Log::info('t1');
                                $mail_to = $email_arr;
                                $subject = 'Threshold Limit Exceed';
                                $view_array = [];
                                $view_array['project_name'] = $project_details['project_name'];
                                $view_array['threshold_limit1'] = $data['threshold_limit1'];
                                $view_array['total_hours'] = (float)$select_record['sum'];
                                $html = view('approved_project.limitexceedmain', compact('view_array'))->render();
                                app('App\Http\Controllers\EmailController')->thresholdSendMail($mail_to, $subject, $html);
                            }
                        }
                    }
                }
                //-------end----------------------
                return response()->json(['status' => 'updated', 'data' => $notify]);
            }
            return response()->json('error');
        } else {
            $data['flag'] = $this->optionRepo->OptionOfId(132);
            $preparedData = $this->companyProjectRepo->prepareData($data);
            $project_create = $this->companyProjectRepo->create($preparedData);
            $from_emp = $project_create->getCreatedBy()->getFirstname() . ' ' . $project_create->getCreatedBy()->getLastname();
            if ($project_create) {
                if (count($data['assigned_ba']) > 0) {
                    foreach ($data['assigned_ba'] as $id) {
                        $baData['company_project_id'] = $this->companyProjectRepo->CompanyProjectOfId($project_create->getId());
                        $baData['emp_id'] = $this->userRepo->UserOfId($id);
                        $baData['flag'] = $this->optionRepo->OptionOfId(132);
                        $baData['ba_tl_flag'] = $this->optionRepo->OptionOfId(132);
                        $prepare = $this->companyProjectBaRepo->prepareData($baData);
                        $createBa = $this->companyProjectBaRepo->create($prepare);

                        $title = 'Project Assigned';
                        $details = $from_emp . ' assigned you ' . $project_create->getProjectName();
                        $notify[] = [
                            'channel_name' => 'project-update-' . $createBa->getEmpId()->getId(),
                            'from_emp' => $from_emp,
                            'title' => $title,
                            'details' => $details
                        ];

                        // create conversation id
//                        $convData['company_project_id'] = $baData['company_project_id'];
//                        $convData['user1'] = $data['created_by'];
//                        $convData['user2'] = $baData['emp_id'];
//                        $prepareConv = $this->companyProjectConvRepo->prepareData($convData);
//                        $this->companyProjectConvRepo->create($prepareConv);
                        // end

                        // create activity box data
                        $prepareNotify['emp_id'] = $baData['emp_id'];
                        $prepareNotify['from_emp'] = $this->userRepo->UserOfId($currentUser);
                        $prepareNotify['title'] = $title;
                        $prepareNotify['details'] = $details;
                        $prepare = $this->activityBoxRepo->prepareData($prepareNotify);
                        $this->activityBoxRepo->create($prepare);
                        // end
                    }
                    return response()->json(['status' => 'created', 'data' => $notify]);
                }
                return response()->json(['status' => 'created']);
            }
            return response()->json(['status' => 'error']);
        }
    }

    function projectAction(Request $request)
    {
        $notify = [];
        $currentUserId = JWTAuth::user()->getId();
        $event = $request->event;
        $msgText = '';
        $project_id = $this->companyProjectRepo->CompanyProjectOfId($request->id);
        if ($event == 'project_started') {
            $msgText = 'started';
            $data['flag'] = $this->optionRepo->OptionOfId(133);
            $data['is_started'] = 1;
            $data['is_closed'] = 0;
            $data['on_hold'] = 0;
        } else if ($event == 'project_closed') {
            $msgText = 'closed';
            $data['flag'] = $this->optionRepo->OptionOfId(139);
            $data['is_closed'] = 1;
            $data['is_started'] = 0;
            $data['on_hold'] = 0;
        } else {
            $msgText = 'reopened';
            $data['flag'] = $this->optionRepo->OptionOfId(140);
            $data['is_started'] = 1;
            $data['is_closed'] = 0;
            $data['on_hold'] = 0;
        }
        $update = $this->companyProjectRepo->update($project_id, $data);
        $baProjects = $this->companyProjectBaRepo->getAllBaOfProject($update->getId());

        // update ba project flags
        foreach ($baProjects as $project) {
            $p_id = $this->companyProjectBaRepo->CompanyProjectBaOfId($project['id']);
            $updateData['flag'] = $data['flag'];
            $updateData['ba_tl_flag'] = $data['flag'];
            $this->companyProjectBaRepo->update($p_id, $updateData);
        }
        // end

        $baProjects[] = ['emp_id' => $update->getCreatedBy()->getId()];
        $from_emp = JWTAuth::user()->getFirstname() . ' ' . JWTAuth::user()->getLastname();
        if ($update) {
            foreach ($baProjects as $row) {
                if ($row['emp_id'] !== $currentUserId) {

                    $titleArr = explode('_', $event);
                    foreach ($titleArr as $key => $value) {
                        $titleArr[$key] = ucfirst($value);
                    }
                    $title = implode(' ', $titleArr);
                    $details = $from_emp . ' ' . $msgText . ' the ' . $update->getProjectName();
                    $notify[] = [
                        'channel_name' => 'project-update-' . $row['emp_id'],
                        'from_emp' => $from_emp,
                        'title' => $title,
                        'details' => $from_emp . ' ' . $msgText . ' the ' . $update->getProjectName()
                    ];

                    // create activity box data
                    $prepareNotify['emp_id'] = $this->userRepo->UserOfId($row['emp_id']);
                    $prepareNotify['from_emp'] = $this->userRepo->UserOfId($currentUserId);
                    $prepareNotify['title'] = $title;
                    $prepareNotify['details'] = $details;
                    $prepare = $this->activityBoxRepo->prepareData($prepareNotify);
                    $this->activityBoxRepo->create($prepare);
                    // end
                }
            }
            return response()->json(['status' => 'success', 'data' => $notify]);
        }
    }

    function getProjectByID(Request $request)
    {
        $emp_id = JWTAuth::user()->getId();
        $jrBAs = [];
        $convs = [];
        if ($request->isBa) {
            $data = $this->companyProjectRepo->getProjectByIdBA((int)$request->id);
            $data['created_by'][0] = [
                'id' => $data['created_by']['id'],
                'firstname' => $data['created_by']['firstname'],
                'lastname' => $data['created_by']['lastname']
            ];
            $conv_id = $this->companyProjectConvRepo->getConvId($data['id'], $emp_id, true);
            if (!empty($conv_id)) {
                $comments = $this->companyProjectCommentRepo->getProjectComments($conv_id[0]['id']);
                $convs[] = [
                    'conv_id' => $conv_id[0]['id'],
                    'comments' => $comments,
                    'ba_project_id' => $data['assigned_ba'][0]['id'],
                    'second_user' => $conv_id[0]['firstname'] . ' ' . $conv_id[0]['lastname']
                ];
            }
            $data['convs'] = $convs;
            $data['BAs'] = $data['created_by'];
            $data['created_by'] = $data['created_by'][0]['id'];
            unset($data['assigned_ba']);
            // unset($data['created_by']);
        } else {
            $data = $this->companyProjectRepo->getProjectById((int)$request->id);

            $data['created_by'] = $data['created_by']['id'];
            foreach ($data['assigned_ba'] as $value) {
                $jrBAs[] = [
                    'id' => $value['emp_id']['id'],
                    'firstname' => $value['emp_id']['firstname'],
                    'lastname' => $value['emp_id']['lastname']
                ];
                unset($data['assigned_ba']);
                $conv_id = $this->companyProjectConvRepo->getConvId($data['id'], $value['emp_id']['id']);
                if (!empty($conv_id)) {
                    $comments = $this->companyProjectCommentRepo->getProjectComments($conv_id[0]['id']);
                    $convs[] = [
                        'conv_id' => $conv_id[0]['id'],
                        'comments' => $comments,
                        'ba_project_id' => $value['id'],
                        'second_user' => $conv_id[0]['firstname'] . ' ' . $conv_id[0]['lastname']
                    ];
                }
            }
            $data['BAs'] = $jrBAs;
            $data['convs'] = $convs;
        }
        //recordtiming
        $timing = $this->companyProjectEmpTimingsRepo->getAllEmpTimingRecords($request->id);
        $total = $this->companyProjectEmpTimingsRepo->getAllEmpTimingRecordsWithTotal($request->id);
        $max_date = Carbon::parse($total[0]['max_date']);
        $min_date = Carbon::parse($total[0]['min_date']);
        $total['diff_date'] = $max_date->diffInDays($min_date);
        $total['total'] = (float)$total[0]['total'];
        //------------
        return response()->json(['data' => $data, 'timing' => [$timing, $total]]);
    }
    function getBaProjectById(Request $request)
    {
        $emp_id = JWTAuth::user()->getId();
        $jrBAs = [];
        $convs = [];
        if ($request->isBa) {
            $data = $this->companyProjectRepo->getBaProjectByIdBA((int)$request->id);
//            return $data;
            $data['created_by'][0] = [
                'id' => $data['created_by']['id'],
                'firstname' => $data['created_by']['firstname'],
                'lastname' => $data['created_by']['lastname']
            ];
            $conv_id = $this->companyProjectConvRepo->getConvId($data['id'], $emp_id, true);
            if (!empty($conv_id)) {
                $comments = $this->companyProjectCommentRepo->getProjectComments($conv_id[0]['id']);
                $convs[] = [
                    'conv_id' => $conv_id[0]['id'],
                    'comments' => $comments,
                    'ba_project_id' => $data['assigned_ba'][0]['id'],
                    'second_user' => $conv_id[0]['firstname'] . ' ' . $conv_id[0]['lastname']
                ];
            }
            $data['convs'] = $convs;
            $data['BAs'] = $data['created_by'];
            $data['AssignedBAs'] = $data['assigned_ba'];
            $data['created_by'] = $data['created_by'][0]['id'];
            unset($data['assigned_ba']);
            // unset($data['created_by']);
        } else {
            $data = $this->companyProjectRepo->getProjectById((int)$request->id);

            $data['created_by'] = $data['created_by']['id'];
            foreach ($data['assigned_ba'] as $value) {
                $jrBAs[] = [
                    'id' => $value['emp_id']['id'],
                    'firstname' => $value['emp_id']['firstname'],
                    'lastname' => $value['emp_id']['lastname']
                ];
                unset($data['assigned_ba']);
                $conv_id = $this->companyProjectConvRepo->getConvId($data['id'], $value['emp_id']['id']);
                if (!empty($conv_id)) {
                    $comments = $this->companyProjectCommentRepo->getProjectComments($conv_id[0]['id']);
                    $convs[] = [
                        'conv_id' => $conv_id[0]['id'],
                        'comments' => $comments,
                        'ba_project_id' => $value['id'],
                        'second_user' => $conv_id[0]['firstname'] . ' ' . $conv_id[0]['lastname']
                    ];
                }
            }
            $data['BAs'] = $jrBAs;
            $data['convs'] = $convs;
        }
        //recordtiming
        $timing = $this->companyProjectEmpTimingsRepo->getAllEmpTimingRecords($request->id);
        $total = $this->companyProjectEmpTimingsRepo->getAllEmpTimingRecordsWithTotal($request->id);
        $max_date = Carbon::parse($total[0]['max_date']);
        $min_date = Carbon::parse($total[0]['min_date']);
        $total['diff_date'] = $max_date->diffInDays($min_date);
        $total['total'] = (float)$total[0]['total'];
        //------------
        return response()->json(['data' => $data, 'timing' => [$timing, $total]]);
    }

    function getAllEmpTimingRecords(Request $request)
    {
        $data = $this->companyProjectEmpTimingsRepo->getAllEmpTimingRecords($request->project_id);
        $total = $this->companyProjectEmpTimingsRepo->getAllEmpTimingRecordsWithTotal($request->project_id);
        $max_date = Carbon::parse($total[0]['max_date']);
        $min_date = Carbon::parse($total[0]['min_date']);
        $total['diff_date'] = $max_date->diffInDays($min_date);
        $total['total'] = (int)$total[0]['total'];
        $res['data'] = $data;
        $res['extra'] = $total;
        return response()->json($res);
    }
    function checkProjectNameExist(Request $request)
    {
        $check = $this->companyProjectRepo->checkProjectNameExist($request->projectName, $request->proj_id);

        if ($check) {
            return response()->json(true);
        } else {
            return response()->json(false);
        }
    }
    function getReportByEmp(Request $request)
    {
        $dateRange = $request->dateRange;
        $start = $dateRange['start_date'];
        $end_date = $dateRange['end_date'];

        $formData=$request->formData;
        $emp_id=$formData['emp_id'];
        $project_id = $formData['project_id'];

        $data = $this->companyProjectRepo->getReportByEmp($start,$end_date,$emp_id,$project_id);

        $total = $this->companyProjectEmpTimingsRepo->getAllEmpTimingRecordsWithTotalEmpId($start,$end_date,$emp_id,$project_id);
        $max_date = Carbon::parse($total[0]['max_date']);
        $min_date = Carbon::parse($total[0]['min_date']);
        $total['diff_date'] = $max_date->diffInDays($min_date);
        $total['total'] = (float)$total[0]['total'];
        return response()->json(['timing' => [$data, $total]]);
    }
    function getAllProjectsList()
    {
        $data = $this->companyProjectRepo->getAllProjectsList();
        return response()->json($data);
    }
    function getTotalHoursByProject(Request $request)
    {
        $data = $this->companyProjectRepo->getTotalHoursByProject($request->id);
        return response()->json($data);
    }
}

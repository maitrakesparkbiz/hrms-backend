<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

ini_set('max_execution_time', 300);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ApprovedProjectController extends Controller
{
    function saveFinalApproveProject(Request $request)
    {
        $data = $request->all();
        $notify = [];
        if ($data['id']) {
            $user = JWTAuth::user()->getId();
            $data['created_by'] = $this->userRepo->UserOfId($user);
            $data['assigned_ba'] = $this->userRepo->UserOfId($data['emp_id']);
            $data['main_project_id'] = $this->projectRepo->ProjectOfId($data['id']);
            $data['project_doc'] = implode(',', $data['project_doc']);
            $prepare_data = $this->approvedProjectRepo->prepareData($data);
            $createProject = $this->approvedProjectRepo->create($prepare_data);
            if ($createProject) {
                $projectId = $this->approvedProjectRepo->ApprovedProjectOfId($createProject->getId());

                // add flags
                $flags['project_id'] = $projectId;
                $flags['flag_sales'] = $this->optionRepo->OptionOfId(132);
                $flags['flag_ba'] = $this->optionRepo->OptionOfId(132);
                $prepare = $this->approvedProjectFlagRepo->prepareData($flags);
                $create = $this->approvedProjectFlagRepo->create($prepare);
                // end

                // assign conv id
                $conv_data['project_id'] = $projectId;
                $conv_data['user1'] = $data['created_by'];
                $conv_data['user2'] = $data['assigned_ba'];
                $prepare_data = $this->approvedProjectConvRepo->prepareData($conv_data);
                $create = $this->approvedProjectConvRepo->create($prepare_data);
                // end

                // update old project flag
                $project['final_approved'] = 1;
                $project['status_flag'] = $this->optionRepo->OptionOfId(131);
                $update = $this->projectRepo->update($data['main_project_id'], $project);
                // end

                if ($create) {
                    $from_emp = $createProject->getCreatedBy()->getFirstname() . ' ' . $createProject->getCreatedBy()->getLastname();
                    $notify[] = [
                        'channel_name' => 'project-update-' . $data['emp_id'],
                        'from_emp' => $from_emp,
                        'title' => 'Project Assigned',
                        'details' => $from_emp . ' assigned you ' . $createProject->getProjectName()
                    ];
                    $data['data'] = $notify;
                    $data['id'] = $createProject->getId();
                    return response()->json(['status' => 'success', 'data' => $data]);
                } else {
                    return response()->json(['status' => 'error']);
                }
            }
        }
        return response()->json(['status' => 'error']);
    }

    function updateFinalApproveProject(Request $request)
    {
        $data = $request->all();
        $notify = [];
        if ($data['id']) {
            $project_id = $this->approvedProjectRepo->ApprovedProjectOfId($data['id']);

            if (isset($data['created_by'])) {
                $created_by = $data['created_by'];
                $data['created_by'] = $this->userRepo->UserOfId($data['created_by']);
            }

            if (isset($data['assigned_ba'])) {
                $assigned_ba = $data['assigned_ba'];
                $data['assigned_ba'] = $this->userRepo->UserOfId($data['assigned_ba']);
            }
            if (isset($data['assigned_jr_ba'])) {
                $assigned_jr_ba = $data['assigned_jr_ba'];
                $data['assigned_jr_ba'] = $this->userRepo->UserOfId($data['assigned_jr_ba']);
            }
            if (isset($data['deadline']) && $data['deadline']) {
                $data['deadline'] = new \DateTime($data['deadline']);
            }
            if (isset($data['approved_extra_hours']) || isset($data['approved_extra_hours_reason'])) {
                if ($data['approved_extra_hours'] == '' || $data['approved_extra_hours_reason'] == '') {
                    $data['approved_extra_hours'] = null;
                    $data['approved_extra_hours_reason'] = '';
                }
            }


            $updateProject = $this->approvedProjectRepo->update($project_id, $data);
            $from_emp = $updateProject->getAssignedBa()->getFirstname() . ' ' . $updateProject->getAssignedBa()->getLastname();

            if (isset($data['deadline_changed']) && $data['deadline_changed']) {
                $notify[] = [
                    'channel_name' => 'project-update-' . $updateProject->getCreatedBy()->getId(),
                    'from_emp' => $from_emp,
                    'title' => 'Project Deadline Updated',
                    'details' => $from_emp . ' updated deadline of ' . $updateProject->getProjectName()
                ];

                if ($updateProject->getAssignedJrBa()) {
                    $notify[] = [
                        'channel_name' => 'project-update-' . $updateProject->getAssignedJrBa()->getId(),
                        'from_emp' => $from_emp,
                        'title' => 'Project Deadline Updated',
                        'details' => $from_emp . ' updated deadline of ' . $updateProject->getProjectName()
                    ];
                }
            }

            if (isset($data['thlimit1_changed']) && $data['thlimit1_changed']) {
                if ($updateProject->getAssignedJrBa()) {
                    $notify[] = [
                        'channel_name' => 'project-update-' . $updateProject->getAssignedJrBa()->getId(),
                        'from_emp' => $from_emp,
                        'title' => 'Project Threshold Limit 1 Updated',
                        'details' => $from_emp . ' updated threshold limit 1 of ' . $updateProject->getProjectName()
                    ];
                }
            }

            if (isset($data['thlimit2_changed']) && $data['threshold_limit2']) {
                if ($updateProject->getAssignedJrBa()) {
                    $notify[] = [
                        'channel_name' => 'project-update-' . $updateProject->getAssignedJrBa()->getId(),
                        'from_emp' => $from_emp,
                        'title' => 'Project Threshold Limit 2 Updated',
                        'details' => $from_emp . ' updated threshold limit 2 of ' . $updateProject->getProjectName()
                    ];
                }
            }
            if ($updateProject) {
                if (isset($data['ba_changed']) && $data['ba_changed']) {
                    // check if conv id exists
                    $conv_id = $this->approvedProjectConvRepo->getSrConvId($data['id'], $created_by, $data['old_ba']);
                    if (!empty($conv_id)) {
                        $c_id = $conv_id[0]['id'];
                        $c_id = $this->approvedProjectConvRepo->ApprovedProjectConvOfId($c_id);
                        $this->approvedProjectConvRepo->delete($c_id);
                    }
                    // end

                    // create conversation
                    $conv_data['project_id'] = $project_id;
                    $conv_data['user1'] = $data['created_by'];
                    $conv_data['user2'] = $data['assigned_ba'];
                    $prepare_data = $this->approvedProjectConvRepo->prepareData($conv_data);
                    $create = $this->approvedProjectConvRepo->create($prepare_data);
                    // end

                    // update flags if project is not started

                    $flagData = $this->approvedProjectFlagRepo->getFlagId($data['id']);
                    if (!empty($flagData)) {
                        $flagID = $flagData[0]['flag_id'];
                        $flagID = $this->approvedProjectFlagRepo->ApprovedProjectFlagOfId($flagID);
                        if ($updateProject->getisStarted()) {
                            $updateData['flag_sales'] = $this->optionRepo->OptionOfId(133);
                            $updateData['flag_ba'] = $this->optionRepo->OptionOfId(133);
                        } else {
                            $updateData['flag_sales'] = $this->optionRepo->OptionOfId(132);
                            $updateData['flag_ba'] = $this->optionRepo->OptionOfId(132);
                        }
                        $update = $this->approvedProjectFlagRepo->update($flagID, $updateData);
                        if (!$update) {
                            return response()->json(['status' => 'error']);
                        }
                    }
                    // end
                    if ($create) {
                        $from_emp = $updateProject->getCreatedBy()->getFirstname() . ' ' . $updateProject->getCreatedBy()->getLastname();
                        $notify[] = [
                            'channel_name' => 'project-update-' . $updateProject->getAssignedBa()->getId(),
                            'from_emp' => $from_emp,
                            'title' => 'Project Assigned',
                            'details' => $from_emp . ' assigned you ' . $updateProject->getProjectName()
                        ];
                        return response()->json(['status' => 'updated', 'data' => $notify]);
                    }
                    return response()->json(['status' => 'error']);
                }

                if (isset($data['jr_ba_changed']) && $data['jr_ba_changed']) {
                    // check if conv id exists
                    $conv_id = $this->approvedProjectConvRepo->getSrConvId($data['id'], $assigned_ba, $data['old_jr_ba']);
                    if (!empty($conv_id)) {
                        $c_id = $conv_id[0]['id'];
                        $c_id = $this->approvedProjectConvRepo->ApprovedProjectConvOfId($c_id);
                        $this->approvedProjectConvRepo->delete($c_id);
                    }
                    // end

                    // create conversation
                    $conv_data['project_id'] = $project_id;
                    $conv_data['user1'] = $data['assigned_ba'];
                    $conv_data['user2'] = $data['assigned_jr_ba'];
                    $prepare_data = $this->approvedProjectConvRepo->prepareData($conv_data);
                    $create = $this->approvedProjectConvRepo->create($prepare_data);
                    // end

                    // change flags
                    $flagID = $this->approvedProjectFlagRepo->ApprovedProjectFlagOfId($data['flag_id']);
                    if ($updateProject->getisStarted()) {
                        $updateData['flag_jr_ba'] = $this->optionRepo->OptionOfId(133);
                        $updateData['flag_ba_to_jr'] = $this->optionRepo->OptionOfId(133);
                    } else {
                        $updateData['flag_jr_ba'] = $this->optionRepo->OptionOfId(132);
                        $updateData['flag_ba_to_jr'] = $this->optionRepo->OptionOfId(132);
                    }
                    $update = $this->approvedProjectFlagRepo->update($flagID, $updateData);
                    if (!$update) {
                        return response()->json(['status' => 'error']);
                    }
                    // end
                    if ($create) {
                        $from_emp = $updateProject->getAssignedBa()->getFirstname() . ' ' . $updateProject->getAssignedBa()->getLastname();
                        $notify[] = [
                            'channel_name' => 'project-update-' . $updateProject->getAssignedJrBa()->getId(),
                            'from_emp' => $from_emp,
                            'title' => 'Project Assigned',
                            'details' => $from_emp . ' assigned you ' . $updateProject->getProjectName()
                        ];
                        return response()->json(['status' => 'updated', 'data' => $notify]);
                    }
                }
                //--------record timing-----------
                $ins_timing  = [];

                if (isset($data['record_timing']) && $data['record_timing'] != null) {
                    foreach ($data['record_timing'] as $val) {
                        $ins_timing['emp_id'] = $this->userRepo->UserOfId($val['emp_id']);
                        $ins_timing['created_by'] = $this->userRepo->UserOfId(JWTAuth::user()->getId());
                        $ins_timing['project_id'] = $val['project_id'];
                        $ins_timing['record_date'] = Carbon::today();
                        $ins_timing['record_hours'] = $val['record_hours'];
                        $ins_timing['redmark_comment'] = $val['redmark_comment'];
                        $prepare_data = $this->approvedProjectEmpTimingRepo->prepareData($ins_timing);
                        $this->approvedProjectEmpTimingRepo->create($prepare_data);
                    }

                    $select_record = $this->approvedProjectEmpTimingRepo->selectRecordByProjectID($data['id']);
                    $project_details = $this->approvedProjectRepo->getFinalApproveProjectById($data['id']);
                    $ba_mail = $this->approvedProjectRepo->getBaMailById($project_details['assigned_ba']);               
                    if (isset($data['threshold_limit2']) && $data['threshold_limit2'] != null && $data['threshold_limit2'] > $data['threshold_limit1']) {
                        if ((int)$select_record['sum'] >= $data['threshold_limit2']) {
//                            Log::info('t2');
                            $mail_to = [$ba_mail, 'test@gmail.com'];
                            $subject = 'Threshold Limit Exceed';
                            $view_array = [];
                            $view_array['project_name'] = $project_details[0]['project_name'];
                            $view_array['threshold_limit2'] = $data['threshold_limit2'];
                            $view_array['total_hours'] = (int)$select_record['sum'];
                            $html = view('approved_project.limitexceed', compact('view_array'))->render();
                            $this->sendMail($mail_to, $subject, $html);
                        }
                    } else {
                        if (isset($data['threshold_limit1']) && $data['threshold_limit1'] != null) {
                            if ((int)$select_record['sum'] >= $data['threshold_limit1']) {
//                                Log::info('t1');
                                $mail_to = [$ba_mail];
                                $subject = 'Threshold Limit Exceed';
                                $view_array = [];
                                $view_array['project_name'] = $project_details[0]['project_name'];
                                $view_array['threshold_limit1'] = $data['threshold_limit1'];
                                $view_array['total_hours'] = (int)$select_record['sum'];
                                $html = view('approved_project.limitexceedmain', compact('view_array'))->render();
                                $this->sendMail($mail_to, $subject, $html);
                            }
                        }
                    }
                }
                //-------end----------------------
                return response()->json(['status' => 'updated', 'data' => $notify]);
            }
        }
        return response()->json(['status' => 'error']);
    }

    function getFinalApproveProjectById(Request $request)
    {
        $id = (int)$request->id;
        $data = $this->approvedProjectRepo->getFinalApproveProjectById($id);
        if ($request->sr_ba) {
            $sr_id = null;
            $jr_id = null;
            $sr_comments = [];
            $jr_comments = [];

            if ($data['assigned_ba']) {
                $sr_conv_id = $this->approvedProjectConvRepo->getSrConvId($id, $data['created_by'], $data['assigned_ba']);
                if (!empty($sr_conv_id)) {
                    $sr_id = $sr_conv_id[0]['id'];
                    $sr_comments = $this->approvedProjectCommentsRepo->getProjectComments($sr_id);
                }
            }
            if ($data['assigned_jr_ba']) {
                $jr_conv_id = $this->approvedProjectConvRepo->getSrConvId($id, $data['assigned_ba'], $data['assigned_jr_ba']);
                if (!empty($jr_conv_id)) {
                    $jr_id = $jr_conv_id[0]['id'];
                    $jr_comments = $this->approvedProjectCommentsRepo->getProjectComments($jr_id);
                }
            }

            $data[0]['assigned_ba'] = $data['assigned_ba'];
            $data[0]['assigned_jr_ba'] = $data['assigned_jr_ba'];
            $data[0]['created_by'] = $data['created_by'];
            $data[0]['flag_id'] = $data['flag_id'];
            $response = $data[0];
            $response['sr_conv_id'] = $sr_id;
            $response['jr_conv_id'] = $jr_id;
            $response['sr_comments'] = $sr_comments;
            $response['jr_comments'] = $jr_comments;
        } else if ($request->jr_ba) {
            unset($data[0]['client_name']);
            unset($data[0]['client_email']);
            unset($data[0]['skype_contact']);
            unset($data[0]['est_time']);
            $data[0]['flag_id'] = $data['flag_id'];
            $response = $data[0];
        } else {
            $response = $data[0];
            unset($data[0]);
            foreach ($data as $key => $value) {
                $response[$key] = $value;
            }
        }
        return response()->json($response);
    }

    function getAllApprovedProjectsDatatable(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $emp_id = JWTAuth::user();
        $allRecords = $this->approvedProjectRepo->countAllApprovedProjects($emp_id);
        $filterRecords = $this->approvedProjectRepo->getSalesFilterRecords($search,$emp_id);
        $data = $this->approvedProjectRepo->getAllApprovedProjectsDataTable($order, $column_name, $search, $start, $length, $emp_id);

        for ($i = 0; $i < count($data); $i++) {
            $data[$i][0]['firstname'] = $data[$i]['firstname'];
            $data[$i][0]['lastname'] = $data[$i]['lastname'];
            $data[$i][0]['emp_id'] = $data[$i]['emp_id'];
            $data[$i][0]['profile_image'] = $data[$i]['profile_image'];
            $data[$i][0]['key_text'] = $data[$i]['key_text'];
            $data[$i][0]['value_text'] = $data[$i]['value_text'];
            $data[$i][0]['project_flag_id'] = $data[$i]['project_flag_id'];
            $data[$i] = $data[$i][0];
            unset($data[$i][0]);
        }

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    function getAllApprovedProjectsBaDataTable(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $emp_id = JWTAuth::user();
        $allRecords = $this->approvedProjectRepo->countAllApprovedProjectsBa($emp_id);
        $filterRecords = $this->approvedProjectRepo->getFilterRecords($search,$emp_id);
        $data = $this->approvedProjectRepo->getAllApprovedProjectsBaDataTable($order, $column_name, $search, $start, $length, $emp_id);

        for ($i = 0; $i < count($data); $i++) {
            foreach ($data[$i][0] as $key => $value) {
                $data[$i][$key] = $value;
            }
            unset($data[$i][0]);
        }

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    function getAllApprovedProjectsJrBaDataTable(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $emp_id = JWTAuth::user();
        $allRecords = $this->approvedProjectRepo->countAllApprovedProjectsJrBa($emp_id);
        $filterRecords = $this->approvedProjectRepo->getJrBaFilterRecords($search,$emp_id);
        $data = $this->approvedProjectRepo->getAllApprovedProjectsJrBaDataTable($order, $column_name, $search, $start, $length, $emp_id);

        for ($i = 0; $i < count($data); $i++) {
            foreach ($data[$i][0] as $key => $value) {
                $data[$i][$key] = $value;
            }
            unset($data[$i]['client_name']);
            unset($data[$i]['client_email']);
            unset($data[$i]['skype_contact']);
            unset($data[$i]['est_time']);
            unset($data[$i][0]);
        }

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] =count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    function startApprovedProject(Request $request)
    {
        $data = $request->all();
        $notify = [];
        $id = $data['id'];
        $updateData['is_started'] = 1;
        $project_id = $this->approvedProjectRepo->ApprovedProjectOfId($id);
        $updateProject = $this->approvedProjectRepo->update($project_id, $updateData);
        if ($updateProject) {
            $flagId = $this->approvedProjectFlagRepo->ApprovedProjectFlagOfId($data['project_flag_id']);
            $flagData['flag_sales'] = $this->optionRepo->OptionOfId(133);
            $flagData['flag_ba'] = $this->optionRepo->OptionOfId(133);
            if ($updateProject->getAssignedJrBa()) {
                $flagData['flag_jr_ba'] = $this->optionRepo->OptionOfId(133);
                $flagData['flag_ba_to_jr'] = $this->optionRepo->OptionOfId(133);
            }
            $update = $this->approvedProjectFlagRepo->update($flagId, $flagData);
            if ($update) {
                $from_emp = $updateProject->getCreatedBy()->getFirstname() . ' ' . $updateProject->getCreatedBy()->getLastname();
                $notify[] = [
                    'channel_name' => 'project-update-' . $updateProject->getAssignedBa()->getId(),
                    'from_emp' => $from_emp,
                    'title' => 'Project Started',
                    'details' => $from_emp . ' started the ' . $updateProject->getProjectName()
                ];

                if ($updateProject->getAssignedJrBa()) {
                    $notify[] = [
                        'channel_name' => 'project-update-' . $updateProject->getAssignedJrBa()->getId(),
                        'from_emp' => $from_emp,
                        'title' => 'Project Started',
                        'details' => $from_emp . ' started the ' . $updateProject->getProjectName()
                    ];
                }
                return response()->json(['status' => 'updated', 'data' => $notify]);
            }
            return response()->json(['status' => 'error']);
        }
        return response()->json(['status' => 'error']);
    }

    public function sendMail($email, $subject, $html)
    {

        $from_address = '';
        $to_address = [];
        $cc_address = [];

        $mail = null;
        $mail = new PHPMailer(true); // notice the \  you have to use root namespace here
        try {
            $mail->isSMTP(); // tell to use smtp
            //            $mail->isSendMail();
            $mail->CharSet = "utf-8"; // set charset to utf8
            //            $mail->SMTPDebug = 2;
            $mail->SMTPAuth = true;  // use smpt auth
            //            $mail->SMTPSecure = config('MAIL_ENCRYPTION'); // or ssl
            $mail->Host = 'mail.eworkdemo.com';
            $mail->Port = 587; // most likely something different for you. This is the mailtrap.io port i use for testing.
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            $mail->Username = 'noreply@eworkdemo.com';
            $mail->Password = '1NSuZrEGNvWd';
            $mail->setFrom("noreply@eworkdemo.com", "eSparkBiz");

            //                    $mail->setFrom($from_address, "eSparkBiz");

            $mail->Subject = $subject;
            $mail->MsgHTML($html);
            //                $mail->addAddress($email);

            foreach ($email as $item) {
                $mail->addAddress($item);
            }

            //                    $mail->addCC('info@sacramento4kids.com');
            foreach ($cc_address as $cc) {
                $mail->addCC($cc);
            }

            //                    $mail->addBCC('hr@esparkinfo.com');
            //                    $mail->addBCC('webdeveloper1011@gmail.com');
            $mail->send();
        } catch (phpmailerException $e) {
            return 0;
        } catch (Exception $e) {
            return 0;
        }
        return 1;
    }

    //================================================ updated module ===================================//

    public function getAllApprovedProjectsBaTl(Request $request){
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $emp_id = JWTAuth::user();
        $allRecords = $this->approvedProjectRepo->countAllApprovedProjectsBaTl($emp_id);
        $filterRecords = $this->approvedProjectRepo->getBaTlFilteredRecords($search,$emp_id); 
        $data = $this->approvedProjectRepo->getAllApprovedProjectsBaTl($order, $column_name, $search, $start, $length, $emp_id);

        for ($i = 0; $i < count($data); $i++) {
            $data[$i][0]['firstname'] = $data[$i]['firstname'];
            $data[$i][0]['lastname'] = $data[$i]['lastname'];
            $data[$i][0]['emp_id'] = $data[$i]['emp_id'];
            $data[$i][0]['profile_image'] = $data[$i]['profile_image'];
            $data[$i][0]['key_text'] = $data[$i]['key_text'];
            $data[$i][0]['value_text'] = $data[$i]['value_text'];
            $data[$i][0]['project_flag_id'] = $data[$i]['project_flag_id'];
            $data[$i] = $data[$i][0];
            unset($data[$i][0]);
        }

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }
    

}
 
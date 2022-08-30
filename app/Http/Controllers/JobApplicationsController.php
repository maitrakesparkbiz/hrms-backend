<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Mail;
use Illuminate\View\View;
use App\Http\Controllers\EmailController;
use Tymon\JWTAuth\Facades\JWTAuth;


class JobApplicationsController extends Controller
{
    function getAllJobApplications(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $statusSearch = $request->columns[3]['search']['value'];

        $allRecords = $this->jobApplicationsRepo->countJobApplications();
        $data = $this->jobApplicationsRepo->getAllJobApplications($column_name, $order, $search, $start, $length, $statusSearch);
        $filteredRows = $this->jobApplicationsRepo->countFilteredRowsAll($statusSearch, $search);
        $countNewAps = $this->jobApplicationsRepo->countPendingjobs();

        $res = ['data' => $data, 'count' => count($countNewAps) > 0 ? $countNewAps[0]['count'] : 0];

        $response['data'] = $res;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filteredRows) > 0 ? (int)$filteredRows[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    function getAllJobApplicationsInt(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;

        $allRecords = $this->jobApplicationsRepo->countJobApplicationsInt();
        $data = $this->jobApplicationsRepo->getAllJobApplicationsInt($column_name, $order, $search, $start, $length);
        $filteredRows = $this->jobApplicationsRepo->countFilteredRowsJobsInt($search);
        foreach ($data as $key => $row) {
            $count = $this->jobInterviewRepo->getRescheduleCount($row[0]['id']);
            if (!empty($count)) {
                $data[$key]['re_count'] = (int)$count['re_count'] - 1;
            }
        }

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = $filteredRows;
        $response['recordsTotal'] = $allRecords;
        return response()->json($response);
    }

    function getAllJobApplicationsTodayInt(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;

        $allRecords = $this->jobApplicationsRepo->countJobApplicationsTodayInt();
        $data = $this->jobApplicationsRepo->getAllJobApplicationsTodayInt($column_name, $order, $search, $start, $length);
        $filteredRows = $this->jobApplicationsRepo->countFilteredRowsTodayInt($search);
        foreach ($data as $key => $row) {
            $count = $this->jobInterviewRepo->getRescheduleCount($row[0]['id']);
            $data[$key]['re_count'] = (int)$count['re_count'] - 1;
        }
        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filteredRows) > 0 ? (int)$filteredRows[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    function getAllInterviews(Request $request)
    {
        $data = $this->jobInterviewRepo->getAllInterviews($request->id);
        return response()->json($data);
    }

    function saveApplication(Request $request)
    {
        $data = $request->all();
        $data['job_id'] = $this->jobOpeningRepo->jobOfId($data['job_id']);
        $data['stage'] = $this->jobStageRepo->stageOfId((int)$data['stage']);
        if (isset($data['assoc_emp_id'])) {
            $data['assoc_emp_id'] = $this->userRepo->UserOfId((int)$data['assoc_emp_id']);
        }
        $stage_name = $data['stage']->getStageName();
        if (isset($data['id'])) {
            $applicantId = $this->jobApplicationsRepo->applicationsOfId($data['id']);
            $update_id = $this->jobApplicationsRepo->updateApplication($applicantId, $data);
            if ($stage_name == 'interview') {
                if (isset($data['interview_date'])) {
                    $data['interview_date'] = new \DateTime($data['interview_date']);
                }
                if (isset($data['interview_time'])) {
                    $data['interview_time'] = new \DateTime($data['interview_time']);
                }
                $select_data = $this->jobInterviewRepo->getJobIntById($update_id);
                if ($select_data) {
                    $interview_id = $this->jobInterviewRepo->InterviewOfId($select_data[0]['id']);
                    $update = $this->jobInterviewRepo->updateInterview($interview_id, $data);
                } else {
                    $data['applicant_id'] = $applicantId;
                    $prepare_in_data = $this->jobInterviewRepo->prepareData($data);
                    $result = $this->jobInterviewRepo->create($prepare_in_data);
                    $email = $data['contact_email'];
                    $intDate = '';
                    if ($data['interview_date']) {
                        $intDate = $data['interview_date']->format('d-m-Y l');
                        $subject = $data['applicant_name'] . ', eSparkBiz has scheduled your interview on Date - ' . $intDate;
                    } else {
                        $subject = $data['applicant_name'] . ', eSparkBiz has scheduled your interview';
                    }
                    $view_array = [];
                    $company_address = '';
                    $company_site = '';
                    $hr_names = [];
                    $hr_numbers = [];
                    $hr_emails = [];
                    $view_array['email'] = $email;
                    if ($data['job_id']) {
                        $view_array['opening_name'] = $data['job_id']->getRole();
                    }
                    $view_array['name'] = $data['applicant_name'];
                    if (isset($data['interview_date'])) {
                        $view_array['interview_date'] = $data['interview_date']->format('d-m-Y l');
                    } else {
                        $view_array['interview_date'] = '';
                    }
                    if (isset($data['interview_time'])) {
                        $view_array['interview_time'] = $data['interview_time']->format('h:i a');
                    } else {
                        $view_array['interview_time'] = '';
                    }

                    $email_settings = $this->emailRepo->getEmailSettings();

                    $company_site = $email_settings[0]['company_site'];
                    $company_address = $email_settings[0]['company_address'];
                    $hr_names = explode(',', $email_settings[0]['hr_name']);
                    $hr_emails = explode(',', $email_settings[0]['hr_emails']);
                    $hr_numbers = explode(',', $email_settings[0]['hr_contact_number']);

                    $view_array['company_site'] = $company_site;
                    $view_array['company_address'] = $company_address;
                    $view_array['hr_emails'] = $hr_emails;
                    $view_array['hr_names'] = implode(' / ', $hr_names);
                    $view_array['hr_numbers'] = implode(' / ', $hr_numbers);

                    $html = view('email.demo', compact('view_array'))->render();
                    if (app('App\Http\Controllers\EmailController')->sendMail($email, $subject, $html)) {
                        return response()->json('success');
                    }
                }
            }
            return response()->json("updated");
        } else {
            $prepare_data = $this->jobApplicationsRepo->prepareData($data);
            $res = $this->jobApplicationsRepo->create($prepare_data);
            return response()->json("success");
        }
    }

    function tempMail()
    {
        $html = view('email.demo')->render();
        return $html;
        if (app('App\Http\Controllers\EmailController')->sendMail('webdeveloper1011@gmail.com', 'Activate Your Account', $html)) {
            return response()->json('success');
        }
        //        if(app('App\Http\Controllers\EmailController')->testSendMail()) {
        //            return response()->json('success');
        //        }
    }

    function getApplicationById(Request $request)
    {
        $edit_id = $request->id;
        $data = $this->jobApplicationsRepo->getApplicationById($edit_id);
        if ($data['stage_name'] == 'interview') {
            $int_data = $this->jobInterviewRepo->getIntByApplicantId($edit_id);
            $re_count = $this->jobInterviewRepo->getRescheduleCount($edit_id);
        }
        $arr['job_id'] = $data['job_id'];
        $arr['role'] = $data['role'];
        $arr['exp_required'] = $data['exp_required'];
        $arr['stage_name'] = $data['stage_name'];
        $arr['stage'] = $data['stage_id'];
        $data = $data[0];
        unset($data[0]);
        foreach ($arr as $k => $v) {
            $data[$k] = $v;
        }
        if (!empty($int_data) && isset($int_data)) {
            foreach ($int_data[0] as $key => $value) {
                $data[$key] = $value;
            }
            if (!empty($re_count)) {
                $data['re_count'] = $re_count['re_count'];
            }
        }
        return response()->json($data);
    }

    function getApplicationByEmpId(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $emp_id = JWTAuth::toUser()->getId();
        $statusSearch = $request->columns[3]['search']['value'];

        $allRecords = $this->jobApplicationsRepo->countJobApplicationsByEmpId($emp_id);
        $data = $this->jobApplicationsRepo->getApplicationByEmpId($emp_id, $column_name, $order, $search, $start, $length, $statusSearch);
        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($data);
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    function rescheduleInterview(Request $request)
    {
        $data = $request->all();
        $update_ids = $this->jobInterviewRepo->getInterviewIdsByApplicantId($data['applicant_id']);
        if ($update_ids) {
            foreach ($update_ids as $id) {
                $obj_id = $this->jobInterviewRepo->InterviewOfId($id);
                $this->jobInterviewRepo->changeInterviewStatus($obj_id);
            }
        }

        if (isset($data['interview_date'])) {
            $data['interview_date'] = new \DateTime($data['interview_date']);
        }
        if (isset($data['interview_time'])) {
            $data['interview_time'] = new \DateTime($data['interview_time']);
        }
        $applicant_data = $this->jobApplicationsRepo->getApplicantInfo($data['applicant_id']);
        $data['applicant_id'] = $this->jobApplicationsRepo->applicationsOfId($data['applicant_id']);
        $prepare_data = $this->jobInterviewRepo->prepareData($data);
        $res = $this->jobInterviewRepo->create($prepare_data);

        //email
        $email = $applicant_data['contact_email'];
        $subject = $data['subject'];
        $view_array = [];
        $view_array['email'] = $email;
        $view_array['name'] = $applicant_data['applicant_name'];
        $view_array['interview_date'] = $data['interview_date']->format('d-m-Y l');
        $view_array['interview_time'] = $data['interview_time']->format('h:i a');

        if (!empty($applicant_data['job_id'])) {
            $view_array['opening_name'] = $applicant_data['job_id']['role'];
        }


        $email_settings = $this->emailRepo->getEmailSettings();

        $company_site = $email_settings[0]['company_site'];
        $company_address = $email_settings[0]['company_address'];
        $hr_names = explode(',', $email_settings[0]['hr_name']);
        $hr_emails = explode(',', $email_settings[0]['hr_emails']);
        $hr_numbers = explode(',', $email_settings[0]['hr_contact_number']);

        $view_array['company_site'] = $company_site;
        $view_array['company_address'] = $company_address;
        $view_array['hr_emails'] = $hr_emails;
        $view_array['hr_names'] = implode(' / ', $hr_names);
        $view_array['hr_numbers'] = implode(' / ', $hr_numbers);

        $html = view('email.demo', compact('view_array'))->render();
        if (app('App\Http\Controllers\EmailController')->sendMail($email, $subject, $html)) {
            return response()->json('success');
        }
        //email end
        return response()->json('error');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class CompanyController extends Controller
{
    public function saveCompany(Request $request)
    {
        $data = $request->all();
        if (isset($data["country"])) {
            $data["country"] = $this->optionRepo->OptionOfId($data["country"]);
        }
        if (isset($data["financial_year_start_month"])) {
            $data["financial_year_start_month"] = $this->optionRepo->OptionOfId($data["financial_year_start_month"]);
        }

        if (array_key_exists('leave_start_month', $data)) {
            if (isset($data["leave_start_month"])) {
                $data["leave_start_month"] = $this->optionRepo->OptionOfId($data["leave_start_month"]);
            }
        }

        if (isset($data["currency"])) {
            $data["currency"] = $this->optionRepo->OptionOfId($data["currency"]);
        }
        if (isset($data["timezone"])) {
            $data["timezone"] = $this->optionRepo->OptionOfId($data["timezone"]);
        }
        if (isset($data["datetimeformat"])) {
            $data["datetimeformat"] = $this->optionRepo->OptionOfId($data["datetimeformat"]);
        }

        if(isset($data['default_break_time'])){
            $data["default_break_time"] = $data["default_break_time"];
        }

        if ($request->id) {
            $company = $this->companyRepo->CompanyOfId($request->id);
            $res = $this->companyRepo->update($company, $data);
            return $this->jsonResponse($res);
        } else {
            $prepared_data = $this->companyRepo->prepareData($data);
            $create = $this->companyRepo->create($prepared_data);
            return $this->jsonResponse($create);
        }
    }

    public function getCompany(Request $request)
    {
        return $this->companyRepo->getCompanyById($request->id);
    }

    function uploadlogo(Request $request)
    {
        ini_set('upload_max_filesize', '20M');
        $file = $request->file('file');
        $size = \File::size($file);

        $destinationPath = public_path() . '/upload/';

        $random = mt_rand(100000, 999999);
        $filename = $random . '_' . $request->file('file')->getClientOriginalName();
        //        $extension = $file->getClientOriginalExtension();
        //        $filename = str_random(25) . '.' . $extension;
        $allowed = array('png', 'jpg', 'Jpeg', 'JPG', 'PNG');

        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        $upload_success = $request->file('file')->move($destinationPath, $filename);

        if ($upload_success && in_array($ext, $allowed)) {
            return response()->json(['filename' => $filename, 'size' => $size]);
        } else if ($upload_success) {
            return response()->json(['filename' => $filename, 'size' => $size]);
        } else {
            return 'YEP: Problem in file upload';
        }
    }

    function getlogopath(Request $request)
    {
        $path = $this->companyRepo->getPath();
        //        $path2 = url('upload/' . $path);
        return response()->json(((count($path) > 0) ? $path[0]['logo'] : null));
    }

    function getDateTimeFormat()
    {
        $data = $this->companyRepo->getDateFormat();
        return response()->json((count($data) > 0 ? $data[0]['value_text'] : null));
    }

    function getPendingCount()
    {
        $emp_id = JWTAuth::user()->getId();
        $pending_leaves = $this->leaveapplicationRepo->countPendingLeaves();
        $pending_claims = $this->expenseRepo->countPendingClaims();
        $pending_jobs = $this->jobApplicationsRepo->countPendingjobs();
        $today_leaves = $this->leaveapplicationRepo->countTodayLeaves();
        $is_leader = $this->teamRepo->checkLeader($emp_id);
        $news_read = $this->newsempRepo->checkIsRead($emp_id);

        $data = [
            'leaves' => count($pending_leaves) > 0 ? $pending_leaves[0]['count'] : 0,
            'claims' => count($pending_claims) > 0 ? $pending_claims[0]['count'] : 0,
            'jobs' => count($pending_jobs) > 0 ? $pending_jobs[0]['count'] : 0,
            'is_leader' => count($is_leader) > 0 ? true : false,
            'is_news_read' => count($news_read) > 0 ? true : false,
            'today_leaves' => count($today_leaves) > 0 ? $today_leaves[0]['count'] : 0
        ];
        return response()->json($data);
    }
    function getPendingCountSelf()
    {
        $emp_id = JWTAuth::user()->getId();

        $news_read = $this->newsempRepo->checkIsRead($emp_id);

        $data = [
            'is_news_read' => count($news_read) > 0 ? true : false
        ];
        return response()->json($data);
    }
}

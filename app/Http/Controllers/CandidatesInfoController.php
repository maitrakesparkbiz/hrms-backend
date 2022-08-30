<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Mail;
use Illuminate\View\View;
use App\Http\Controllers\EmailController;
use Tymon\JWTAuth\Facades\JWTAuth;


class CandidatesInfoController extends Controller
{
    function getAllCandidatesInfo(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $statusSearch = $request->columns[3]['search']['value'];
        $cat_filter = $request->columns[2]['search']['value'];
        $sal_filter = $request->columns[4]['search']['value'];

        if($sal_filter){
            $sal = explode('-', $sal_filter);
        }else{
            $sal = [];
        }

        $allRecords = $this->candidatesInfoRepo->countCandidatesInfo();

        $data = $this->candidatesInfoRepo->getAllCandidatesInfo($column_name, $order, $search, $start, $length, $statusSearch,$cat_filter,$sal_filter,count($sal) > 0 ? $sal : []);

//        $filteredRows = $this->candidatesInfoRepo->countFilteredRowsAll($statusSearch, $search);
        $filteredRows = $this->candidatesInfoRepo->countFilteredRowsAll($column_name, $order, $search, $start, $length, $statusSearch,$cat_filter,$sal_filter,count($sal) > 0 ? $sal : []);


        $res = ['data' => $data];

        $response['data'] = $res;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filteredRows) > 0 ? (int)$filteredRows[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }


    function saveCandidatesInfo(Request $request)
    {

        $data = $request->all();
        $data['category'] = $this->jobOpeningRepo->jobOfId($data['category']);


        if (isset($data['id'])) {
            $candidateId = $this->candidatesInfoRepo->candidatesInfoOfId($data['id']);
            $update_id = $this->candidatesInfoRepo->updateCandidatesInfo($candidateId, $data);

            return response()->json("updated");
        } else {
            $prepare_data = $this->candidatesInfoRepo->prepareData($data);
            $res = $this->candidatesInfoRepo->create($prepare_data);
            return response()->json("success");
        }
    }
    function getCandidatesInfoById(Request $request)
    {
        $edit_id = $request->id;
        $data = $this->candidatesInfoRepo->getCandidatesInfoById($edit_id);

        return response()->json($data);
    }

    function sendCandidateMail(Request $request)
    {

        $data = $request->all();
        $body=$request->body;

        $description = $body['description'];
        $title = $body['title'];


        foreach ($data['data'] as $key => $value) {
            if (isset($value['isSelected']) && $value['isSelected'] == 1) {
                $candidates[] = $this->candidatesInfoRepo->getCandidatesInfoById($value['id']);
            }
        }

        foreach ($candidates as $candidate)
        {
            $subject = $title;
            $view_array = [];
            $view_array['title'] = $title;
            $view_array['candidate_name'] = $candidate['candidate_name'];
            $view_array['description'] = $description;

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

            $html = view('candidate_info.sendmail', compact('view_array'))->render();
            if(app('App\Http\Controllers\EmailController')->candidateSendMail($candidate['contact_email'], $subject, $html))
            {
                return response()->json(['response'=>'success']);
            }
            return false;
        }

    }


}

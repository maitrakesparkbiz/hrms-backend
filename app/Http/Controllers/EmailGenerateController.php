<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class EmailGenerateController extends Controller
{
    function getAllGeneratedEmailsDatatable(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $allRecords = $this->emailGenerateRepo->countAllGeneratedEmails();
        $filterRecords = $this->emailGenerateRepo->countFilteredRecords($search);
        $data = $this->emailGenerateRepo->getAllGeneratedEmailsDatatable($order, $column_name, $search, $start, $length);

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }


    function saveGeneratedEmail(Request $request)
    {
        $data = $request->all();
        $currentUser = JWTAuth::user()->getId();
        $data['emp_id'] = $this->userRepo->UserOfId($data['emp_id']);
        $data['email_template_id'] = $this->emailTemplatesRepo->EmailTemplatesOfId($data['email_template_id']);
        if ($data['id']) {
            $data['id'] = $this->emailGenerateRepo->EmailGenerateOfId($data['id']);
            $update = $this->emailGenerateRepo->update($data['id'], $data);
            if ($update) {
                return response()->json('updated');
            }
        } else {
            $data['created_by'] = $this->userRepo->UserOfId($currentUser);
            $prepareData = $this->emailGenerateRepo->prepareData($data);
            $create = $this->emailGenerateRepo->create($prepareData);
            if ($create) {
                return response()->json('created');
            }
            return response()->json('error');
        }
    }

    function getGeneratedEmailById(Request $request)
    {
        $id = $request->id;
    }
}

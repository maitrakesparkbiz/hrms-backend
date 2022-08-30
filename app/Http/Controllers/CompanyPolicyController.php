<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class CompanyPolicyController extends Controller
{
    public function companyPolicyById(Request $request)
    {
        $data =  $this->companyPolicyRepo->companyPolicyById($request->id);
        return response()->json($data);
    }
    public function savePolicy(Request $request)
    {
        $data = $request->all();
        if ($data['id']) {
            $company_policy = $this->companyPolicyRepo->CompanyPolicyOfId($data['id']);
            $res = $this->companyPolicyRepo->update($company_policy, $data);
            if ($res) {
                return response()->json("updated");
            }
        } else {
            $prepared_data = $this->companyPolicyRepo->prepareData($data);
            $create = $this->companyPolicyRepo->create($prepared_data);
            if ($create) {
                return response()->json("created");
            }
        }
    }

    public function getAllCompanyPolicyDatatable(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $allRecords = $this->companyPolicyRepo->countCompanyPolicy();
        $filterRecords = $this->companyPolicyRepo->countFilteredCompanyPolicy($search);
        $data = $this->companyPolicyRepo->getCompanyPolicyDatatable($order, $column_name, $search, $start, $length);

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    public function getAllCompanyPolicySelfDatatable(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $allRecords = $this->companyPolicyRepo->countCompanyPolicy(true);
        $filterRecords = $this->companyPolicyRepo->countFilteredCompanyPolicy($search, true);
        $data = $this->companyPolicyRepo->getCompanyPolicyDatatable($order, $column_name, $search, $start, $length, true);

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    public function deletePolicy(Request $request)
    {
        $id = $this->companyPolicyRepo->CompanyPolicyOfId($request->id);
        if ($id) {
            $this->companyPolicyRepo->delete($id);
            return response()->json('success');
        }
        return response()->json('error');
    }
}

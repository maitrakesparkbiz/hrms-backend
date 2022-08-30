<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CompanyProjectBaController extends Controller
{
    function getAllProjectsDatatableBa(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $emp_id = JWTAuth::user();
        $allRecords = $this->companyProjectBaRepo->countAllProjectsBa($emp_id);
        $filterRecords = $this->companyProjectBaRepo->countFilteredProjectsBa($search, $emp_id);
        $data = $this->companyProjectBaRepo->getAllProjectsDatatableBa($order, $column_name, $search, $start, $length, $emp_id);
        $flags = $this->companyProjectBaRepo->getBaProjectFlags($order, $column_name, $search, $start, $length, $emp_id);
//        return $flags;
        foreach ($data as $key => $value) {
            foreach ($flags[$key] as $fkey => $fvalue) {
                $data[$key][$fkey] = $fvalue;
            }
        }
//        foreach ($data as $key => $value) {
//            foreach ($value[0] as $rowKey => $rowValue) {
//                $data[$key][$rowKey] = $rowValue;
//            }
//            unset($data[$key][0]);
//        }
        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    function getAllClosedProjectsDatatableBa(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $emp_id = JWTAuth::user();
        $allRecords = $this->companyProjectBaRepo->countAllProjectsBa($emp_id, true);
        $filterRecords = $this->companyProjectBaRepo->countFilteredProjectsBa($search, $emp_id, true);
        $data = $this->companyProjectBaRepo->getAllProjectsDatatableBa($order, $column_name, $search, $start, $length, $emp_id, true);

        foreach ($data as $key => $value) {
            foreach ($value[0] as $rowKey => $rowValue) {
                $data[$key][$rowKey] = $rowValue;
            }
            unset($data[$key][0]);
        }

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

}

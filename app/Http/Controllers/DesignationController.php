<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function saveDesignation(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            if ($value['name'] != '') {
                $value['dep_id'] = $this->departmentRepo->DepartmentOfId($value['dep_id']);
                if (isset($value['id'])) {
                    $designation = $this->designationRepo->DesignationOfId($value['id']);
                    $data = $this->designationRepo->update($designation, $value);
                } else {
                    // $this->checkDept($value['name']);
                    $prepared_data = $this->designationRepo->prepareData($value);
                    $data = $this->designationRepo->create($prepared_data);
                }
            }
        }
        return $this->jsonResponse($data);
    }

    public function getAllDesignation()
    {
        $data = $this->designationRepo->getAllDesignation();
        for ($i = 0; $i < count($data); $i++) {
            if (!empty($data[$i][0]['employees'])) {
                $data[$i]['employees'] = count($data[$i][0]['employees']);
            } else {
                $data[$i]['employees'] = 0;
            }
            $data[$i]['id'] = $data[$i][0]['id'];
            $data[$i]['name'] = $data[$i][0]['name'];
            unset($data[$i][0]);
        }
        return $this->jsonResponse($data);
    }

    public function getAllDesDatatable(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $allRecords = $this->designationRepo->countAllDes();
        $filterRecords = $this->designationRepo->getFilterRecords($search);
        $data = $this->designationRepo->getAllDesDatatable($column_name, $order, $search, $start, $length);

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    public function deleteDesignation(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            if (isset($value['isSelected']) && $value['isSelected'] == 1) {
                $designation = $this->designationRepo->DesignationOfId($value['id']);
                $data = $this->designationRepo->delete($designation);
            }
        }
        return $this->jsonResponse($data);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function saveDepartment(Request $request)
    {
        $data = $request->all();
        $dept_errors = [];
        foreach ($data as $key => $value) {
            if ($value['name'] != '') {
                if (isset($value['id'])) {
                    $department = $this->departmentRepo->DepartmentOfId($value['id']);
                    $data = $this->departmentRepo->update($department, $value);
                } else {
                    // $this->checkDept($value['name']);
                    $prepared_data = $this->departmentRepo->prepareData($value);
                    $data = $this->departmentRepo->create($prepared_data);
                }
            }
        }
        return $this->jsonResponse($data);
    }

    public function getAllDepartment()
    {
        $data = $this->departmentRepo->getAllDepartment();
        for ($i = 0; $i < count($data); $i++) {
            if (!empty($data[$i]['employees'])) {
                $data[$i]['employees'] = count($data[$i]['employees']);
            } else {
                $data[$i]['employees'] = 0;
            }
        }
        return $this->jsonResponse($data);
    }

    public function getAllDeptDatatable(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $allRecords = $this->departmentRepo->countAllDept();
        $filterRecords = $this->departmentRepo->getFilterRecored($search);
        $data = $this->departmentRepo->getAllDeptDatatable($order, $column_name, $search, $start, $length);

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    public function deleteDepartment(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            if (isset($value['isSelected']) && $value['isSelected'] == 1) {
                $department = $this->departmentRepo->DepartmentOfId($value['id']);
                $data = $this->departmentRepo->delete($department);
            }
        }
        return $this->jsonResponse($data);
    }
}

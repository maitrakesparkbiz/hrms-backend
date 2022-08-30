<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function saveExpense_category(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            if ($value['name'] != '' && $value['description'] != '') {
                if (isset($value['id'])) {
                    $expense = $this->expense_categoryRepo->ExpenseOfId($value['id']);
                    $data = $this->expense_categoryRepo->update($expense, $value);
                } else {
                    // $this->checkDept($value['name']);
                    $prepared_data = $this->expense_categoryRepo->prepareData($value);
                    $data = $this->expense_categoryRepo->create($prepared_data);
                }
            }
        }
        return $this->jsonResponse($data);
    }


    public function getAllExpense_category()
    {
        $data = $this->expense_categoryRepo->getAllExpense();
        return $this->jsonResponse($data);
    }

    public function getAllCatDatatable(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $allRecords = $this->expense_categoryRepo->countAllCat();
        $filterRecords = $this->expense_categoryRepo->getFilterRecords($search);
        $data = $this->expense_categoryRepo->getAllCatDatatable($column_name, $order, $search, $start, $length);

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    public function deleteExpense_category(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            if (isset($value['isSelected']) && $value['isSelected'] == 1) {
                $expense = $this->expense_categoryRepo->ExpenseOfId($value['id']);
                $data = $this->expense_categoryRepo->delete($expense);
            }
        }
        return $this->jsonResponse($data);
    }
}

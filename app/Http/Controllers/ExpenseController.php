<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ExpenseController extends Controller
{
    function saveExpense(Request $request)
    {
        $data = $request->all();
        if ($request->id) {
            if (isset($data['bill_date'])) {
                $data['bill_date'] = new \DateTime($data['bill_date']);
                $data['bill_date']->add(new \DateInterval('P1D'));
            }
            $data['category_id'] = $this->expense_categoryRepo->ExpenseOfId($data['category_id']);
            $expense = $this->expenseRepo->ExpenseOfId($request->id);
            if (isset($data['emp_id'])) {
                $data['emp_id'] = $this->userRepo->UserOfId($data['emp_id']);
            }
            if (isset($data['actioned_by'])) {
                $data['actioned_by'] = $this->userRepo->UserOfId($data['actioned_by']);
            }
            if (isset($data['rotation'])) {
                $data['rotation'] = $this->optionRepo->OptionOfId($data['rotation']);
            }
            $this->expenseRepo->update($expense, $data);
            return response()->json("Expense Update succesfully");
        } else {
            $data['category_id'] = $this->expense_categoryRepo->ExpenseOfId($data['category_id']);
            if (isset($data['bill_date'])) {
                $data['bill_date'] = new \DateTime($data['bill_date']);
                $data['bill_date']->add(new \DateInterval('P1D'));
            }
            if (isset($data['emp_id'])) {
                $data['emp_id'] = $this->userRepo->UserOfId($data['emp_id']);
            }
            if (isset($data['rotation'])) {
                $data['rotation'] = $this->optionRepo->OptionOfId($data['rotation']);
            }
            $prepared_data = $this->expenseRepo->prepareData($data);
            $expense_id = $this->expenseRepo->create($prepared_data);
            return response()->json("Expense Add succesfully");
        }
    }

    function getAllExpense(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $allRecords = $this->expenseRepo->countAllExpense();
        $data = $this->expenseRepo->getAllExpense($column_name, $order, $search, $start, $length);
        $filteredRows = $this->expenseRepo->countFilteredRowsAll($search);
        for ($i = 0; $i < count($data); $i++) {
            $temp = $data[$i]['emp_id'];
            unset($data[$i]['emp_id']);
            $data[$i]['emp_id'] = ['id' => $temp['id'], 'firstname' => $temp['firstname'], 'lastname' => $temp['lastname'], 'profile_image' => $temp['profile_image']];
            if ($data[$i]['actioned_by']) {
                $temp = $data[$i]['actioned_by'];
                unset($data[$i]['actioned_by']);
                $data[$i]['actioned_by'] = ['id' => $temp['id'], 'firstname' => $temp['firstname'], 'lastname' => $temp['lastname'], 'profile_image' => $temp['profile_image']];
            }
            $data[$i]['name'] = $data[$i]['category_id']['name'];
        }
        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filteredRows) > 0 ? (int)$filteredRows[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    function approveClaim(Request $request)
    {
        $data['status'] = "approve";
        $data['actioned_by'] = $this->userRepo->UserOfId($request->actioned_by);
        $expense = $this->expenseRepo->ExpenseOfId($request->id);
        $this->expenseRepo->update($expense, $data);
        return response()->json("Approve Claim succesfully");
    }

    function rejectClaim(Request $request)
    {
        $data['status'] = "reject";
        $data['actioned_by'] = $this->userRepo->UserOfId($request->actioned_by);
        $expense = $this->expenseRepo->ExpenseOfId($request->id);
        $this->expenseRepo->update($expense, $data);
        return response()->json("Reject Claim succesfully");
    }

    function getAllClaims(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $statusSearch = $request->columns[5]['search']['value'];
        $allRecords = $this->expenseRepo->countAllClaims();
        $data = $this->expenseRepo->getAllClaims($column_name, $order, $search, $start, $length, $statusSearch);
        $filteredRows = $this->expenseRepo->countFilteredRowsClaims($statusSearch, $search);

        for ($i = 0; $i < count($data); $i++) {
            $temp = $data[$i]['emp_id'];
            unset($data[$i]['emp_id']);
            $data[$i]['emp_id'] = ['id' => $temp['id'], 'firstname' => $temp['firstname'], 'lastname' => $temp['lastname'], 'profile_image' => $temp['profile_image']];
            if ($data[$i]['actioned_by']) {
                $temp = $data[$i]['actioned_by'];
                unset($data[$i]['actioned_by']);
                $data[$i]['actioned_by'] = ['id' => $temp['id'], 'firstname' => $temp['firstname'], 'lastname' => $temp['lastname'], 'profile_image' => $temp['profile_image']];
            }
            $data[$i]['firstname'] = $data[$i]['emp_id']['firstname'] . ' ' . $data[$i]['emp_id']['lastname'];
            $data[$i]['name'] = $data[$i]['category_id']['name'];
        }

        $pending_claims_count = $this->expenseRepo->countPendingClaims();

        $res = ['data' => $data, 'count' => count($pending_claims_count) > 0 ? $pending_claims_count[0]['count'] : 0];

        $response['data'] = $res;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filteredRows) > 0 ? (int)$filteredRows[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }
    function getClaimsSelf(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'updated_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $statusSearch = $request->columns[5]['search']['value'];
        $emp_id = JWTAuth::toUser()->getId();
        $allRecords = $this->expenseRepo->getRowCountSelf($emp_id);
        $filteredRows = $this->expenseRepo->countFilteredSelfClaims($search, $statusSearch, $emp_id);
        $data = $this->expenseRepo->getClaimsSelf($emp_id, $column_name, $order, $search, $start, $length, $statusSearch);
        for ($i = 0; $i < count($data); $i++) {
            unset($data[$i]['emp_id']);
            if ($data[$i]['actioned_by']) {
                $temp = $data[$i]['actioned_by'];
                unset($data[$i]['actioned_by']);
                $data[$i]['actioned_by'] = ['id' => $temp['id'], 'firstname' => $temp['firstname'], 'lastname' => $temp['lastname'], 'profile_image' => $temp['profile_image']];
            }
            $data[$i]['name'] = $data[$i]['category_id']['name'];
        }
        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filteredRows) > 0 ? (int)$filteredRows[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    function deleteExpense(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            if (isset($value['isSelected']) && $value['isSelected'] == 1) {
                $expense = $this->expenseRepo->ExpenseOfId($value['id']);
                $data = $this->expenseRepo->delete($expense);
            }
        }
        return $this->jsonResponse($data);
    }

    function getExpenseById(Request $request)
    {
        return $this->expenseRepo->getExpenseById($request->id);
    }

    function getPendingClaims()
    {
        $data = $this->expenseRepo->getPendingClaims();
        return response()->json($data);
    }

    function getPendingClaimsCount()
    {
        $pending_claims = $this->expenseRepo->countPendingClaims();
        return response()->json(['count' => count($pending_claims) > 0 ? $pending_claims[0]['count'] : 0]);
    }

    function getExpenseByYear(Request $request)
    {
        $cat_id = $request->cat_id;
        $year = $request->year;
        if (isset($cat_id)) {
            $data = $this->expenseRepo->getExpenseByYear($cat_id, $year);
        } else {
            $data = $this->expenseRepo->getExpenseByYearOnly($year);
        }
        return response()->json($data);
    }
}

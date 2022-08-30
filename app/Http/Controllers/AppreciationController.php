<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppreciationController extends Controller
{
    public function saveAppreciation(Request $request)
    {
        $data = $request->all();
        if (isset($data["award_id"])) {
            $data["award_id"] = $this->awardRepo->AwardOfId($data["award_id"]);
        }
        if (isset($data["emp_id"])) {
            $data["emp_id"] = $this->userRepo->UserOfId($data["emp_id"]);
        }
        if (isset($data['date'])) {
            $data['date'] = new \DateTime($data['date']);
            $data['date']->add(new \DateInterval('P1D'));
        }
        if ($request->id) {
            $appreciation = $this->appreciationRepo->AppreciationOfByID($request->id);
            $res = $this->appreciationRepo->update($appreciation, $data);
            return response()->json("appreciation Update succesfully");
        } else {
            $prepared_data = $this->appreciationRepo->prepareData($data);
            $create = $this->appreciationRepo->create($prepared_data);
            return response()->json("appreciation Add succesfully");
        }
    }

    public function getAllAppreciation()
    {
        $appreciation_data = $this->appreciationRepo->getAllAppreciation();
        return $this->jsonResponse($appreciation_data);
    }

    public function getAllAppreciationDatatable(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;

        $allRecords = $this->appreciationRepo->countAllAppreciation();
        $data = $this->appreciationRepo->getAllAppreciationDatatable($column_name, $order, $search, $start, $length);
        $filteredRecords = $this->appreciationRepo->countFilteredRows($search);

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filteredRecords) > 0 ? (int)$filteredRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    public function deleteAppreciation(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            if (isset($value['isSelected']) && $value['isSelected'] == 1) {
                $appreciation = $this->appreciationRepo->AppreciationOfByID($value['id']);
                $res = $this->appreciationRepo->delete($appreciation);
            }
        }
        return response()->json("Deleted Successfully");
    }

    public function getAppreciationById(Request $request)
    {
        return $this->appreciationRepo->getAppreciationById($request->id);
    }

    public function getAppreciationByEmpId(Request $request)
    {
        $data = $this->appreciationRepo->getAppreciationByEmpId($request->emp_id);
        return response()->json($data);
    }
}

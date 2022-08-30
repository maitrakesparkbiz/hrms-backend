<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AwardController extends Controller
{
    public function saveAward(Request $request)
    {
        $data = $request->all();
        if (isset($data['status'])) {
            $data["status"] = $this->optionRepo->OptionOfId($data["status"]);
        }
        if ($request->id) {
            $award = $this->awardRepo->AwardOfId($request->id);
            $res = $this->awardRepo->update($award, $data);
            return $this->jsonResponse($res);
        } else {
            $prepared_data = $this->awardRepo->prepareData($data);
            $create = $this->awardRepo->create($prepared_data);
            return $this->jsonResponse($create);
        }
    }

    public function getAllAward()
    {
        $user_data = $this->awardRepo->getAllAward();
        return $this->jsonResponse($user_data);
    }

    public function getAllAwardDatatable(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $allRecords = $this->awardRepo->countAllAwards();
        $filterRecords = $this->awardRepo->getFilterRecords($search);
        $data = $this->awardRepo->getAllAwardDatatable($column_name, $order, $search, $start, $length);

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] =  count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    public function deleteAward(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            if (isset($value['isSelected']) && $value['isSelected'] == 1) {
                $award = $this->awardRepo->AwardOfId($value['id']);
                $res = $this->awardRepo->delete($award);
            }
        }
        return $this->jsonResponse($res);
    }

    public function getAward(Request $request)
    {
        return $this->awardRepo->getAwardById($request->id);
    }
}

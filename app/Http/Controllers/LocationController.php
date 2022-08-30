<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class LocationController extends Controller
{
    public function saveLocation(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            if (isset($value['leave_start_month'])) {
                $value['leave_start_month'] = $this->optionRepo->OptionOfId($value['leave_start_month']);
            }
            if (isset($value['office_start_time'])) {
                $value['office_start_time'] = new \DateTime($value['office_start_time']);
            }
            if (isset($value['office_end_time'])) {
                $value['office_end_time'] = new \DateTime($value['office_end_time']);
            }
            if (isset($value['half_day_hours'])) {
                $value['half_day_hours'] = new \DateTime($value['half_day_hours']);
            }
            if (isset($value['clock_reminder_time'])) {
                $value['clock_reminder_time'] = new \DateTime($value['clock_reminder_time']);
            }
            if ($value['name'] != '') {
                if (isset($value['id'])) {
                    $location = $this->locationRepo->LocationOfId($value['id']);
                    $data = $this->locationRepo->update($location, $value);
                } else {
                    // $this->checkDept($value['name']);
                    $prepared_data = $this->locationRepo->prepareData($value);
                    $data = $this->locationRepo->create($prepared_data);
                }
            }
        }
        return $this->jsonResponse($data);
    }

    public function getAllLocation()
    {
        $data = $this->locationRepo->getAllLocation();
        for ($i = 0; $i < count($data); $i++) {
            if (!empty($data[$i]['employees'])) {
                $data[$i]['employees'] = count($data[$i]['employees']);
            } else {
                $data[$i]['employees'] = 0;
            }
        }
        return $this->jsonResponse($data);
    }

    public function getAllLocationDatatable(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $allRecords = $this->locationRepo->countAllLocation();
        $filterRecords = $this->locationRepo->getFilterRecords($search);
        $data = $this->locationRepo->getAllLocationDatatable($order, $column_name, $search, $start, $length);

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] =count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    public function deleteLocation(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            if (isset($value['isSelected']) && $value['isSelected'] == 1) {
                $location = $this->locationRepo->LocationOfId($value['id']);
                $data = $this->locationRepo->delete($location);
            }
        }
        return $this->jsonResponse($data);
    }

    public function getAllLocationOpt()
    {
        $data = $this->locationRepo->getAllLocationOpt();
        return response()->json($data);
    }

    public function getUserBatch()
    {
        $user = JWTAuth::user()->getId();
        $data = $this->userRepo->getUserLocation($user);
        return response()->json($data);
    }
}

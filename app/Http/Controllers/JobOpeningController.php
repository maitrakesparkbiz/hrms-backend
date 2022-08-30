<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JobOpeningController extends Controller
{
    //    function getAllOpenings(){
    //        $data = $this->jobOpeningRepo->getAllOpenings();
    //        return $this->jsonResponse($data);
    //    }

    function getAllOpenings(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $statusSearch = $request->columns[5]['search']['value'];

        $allRecords = $this->jobOpeningRepo->countAllOpenings();
        $data = $this->jobOpeningRepo->getAllOpenings($column_name, $order, $search, $start, $length, $statusSearch);
        $filteredRows = $this->jobOpeningRepo->countFilteredOpenings($statusSearch, $search);

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filteredRows) > 0 ? (int)$filteredRows[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    function saveOpening(Request $request)
    {
        $data = $request->all();
        $data['last_date'] = new \DateTime($data['last_date']);
        $data['posted_date'] = new \DateTime($data['posted_date']);
        if (isset($data['id'])) {
            $job_id = $this->jobOpeningRepo->jobOfId($data['id']);
            $response = $this->jobOpeningRepo->updateJob($job_id, $data);
            if ($response) {
                return response()->json('updated');
            }
        } else {
            $prepare_data = $this->jobOpeningRepo->prepareData($data);
            $job_id = $this->jobOpeningRepo->create($prepare_data);
            return response()->json('success');
        }
    }

    function getOpeningById(Request $request)
    {
        $data = $this->jobOpeningRepo->getOpeningById($request->edit_id);
        return response()->json($data);
    }

    function getPublicOpeningById(Request $request){
        $data = $this->jobOpeningRepo->getOpeningById($request->edit_id);
        return response()->json($data);
    }

    function deleteOpening(Request $request)
    {
        $jobdata = $this->jobOpeningRepo->jobOfId($request->delete_id);
        $data = $this->jobOpeningRepo->deleteOpening($jobdata);
        return response()->json('deleted');
    }

    function getJobOptionsData()
    {
        $data = $this->jobOpeningRepo->getJobOptionsData();
        return response()->json($data);
    }

    function getAllOpeningsSelf(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;

        $allRecords = $this->jobOpeningRepo->countAllSelfOpenings();
        $data = $this->jobOpeningRepo->getAllOpeningsSelf($column_name, $order, $search, $start, $length);
        $filteredRows = $this->jobOpeningRepo->countFilteredRowsOpeningsSelf($search);
        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filteredRows) > 0 ? (int)$filteredRows[0]['count'] : 0;
        $response['recordsTotal'] =  count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    function getOpeningsPublic()
    {
        $data = $this->jobOpeningRepo->getOpeningsPublic();
        return response()->json($data);
    }
}
 
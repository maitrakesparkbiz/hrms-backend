<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class TeamController extends Controller
{
    public $order;

    public function getAllTeams(Request $request)
    {
        global $order;
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;

        $allRecords = $this->teamRepo->countAllTeams();
        $data = $this->teamRepo->getAllTeams($column_name, $order, $search, $start, $length);
        $filteredRecords = $this->teamRepo->countFilteredTeams($search);
        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filteredRecords) > 0 ? (int) $filteredRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }

    public function addTeam(Request $request)
    {
        $data = $request->data;
        if ($data['id']) {
            $team_id = $this->teamRepo->teamOfId($data['id']);
            $this->teamEmpRepo->delete($data['id']);
            $data['leader'] = $this->userRepo->UserOfId($data['leader']);
            $update = $this->teamRepo->update($team_id, $data);
            if ($update) {
                foreach ($data['member'] as $member) {
                    $member_id = $this->userRepo->UserOfId($member);
                    $create['team_id'] = $team_id;
                    $create['member'] = $member_id;
                    $prepare_data = $this->teamEmpRepo->prepareData($create);
                    $res = $this->teamEmpRepo->create($prepare_data);
                }
            }
            return response()->json('updated');
        } else {
            $data['leader'] = $this->userRepo->UserOfId($data['leader']);
            $prepare_data = $this->teamRepo->prepareData($data);
            $create_id = $this->teamRepo->create($prepare_data);
            if ($create_id) {
                $team_id = $this->teamRepo->teamOfId($create_id);
                $create = [];
                foreach ($data['member'] as $member) {
                    $member_id = $this->userRepo->UserOfId($member);
                    $create['team_id'] = $team_id;
                    $create['member'] = $member_id;
                    $prepare_data = $this->teamEmpRepo->prepareData($create);
                    $res = $this->teamEmpRepo->create($prepare_data);
                }
            }
            return response()->json('created');
        }
    }

    public function getTeamById(Request $request)
    {
        $id = $request->id;
        $data = $this->teamRepo->getTeamById($id);
        if ($data['team_employee']) {
            $temp = $data['leader'];
            unset($data['leader']);
            $data['leader']['id'] = $temp['id'];
            $data['leader']['firstname'] = $temp['firstname'];
            $data['leader']['lastname'] = $temp['lastname'];
            $data['leader']['profile_image'] = $temp['profile_image'];
            for ($i = 0; $i < count($data['team_employee']); $i++) {
                $temp = $data['team_employee'][$i]['member'];
                unset($data['team_employee'][$i]['member']);
                $data['team_employee'][$i]['member']['id'] = $temp['id'];
                $data['team_employee'][$i]['member']['firstname'] = $temp['firstname'];
                $data['team_employee'][$i]['member']['lastname'] = $temp['lastname'];
                $data['team_employee'][$i]['member']['profile_image'] = $temp['profile_image'];
            }
        }
        return response()->json($data);
    }

    public function deleteTeam(Request $request)
    {
        $id = $request->id;
        $this->teamRepo->deleteTeam($id);
        return response()->json('success');
    }

    public function getSelfTeamData(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;

        $emp_id = JWTAuth::user()->getId();

        $allRecords = $this->teamEmpRepo->countSelfTeam($emp_id);
        $data = $this->teamEmpRepo->getSelfTeamData($column_name, $order, $search, $start, $length, $emp_id);
        $filteredRecords = $this->teamEmpRepo->countFilteredRows($search, $emp_id);
        for ($i = 0; $i < count($data); $i++) {
            $temp = $data[$i]['member'];
            unset($data[$i]['member']);
            $data[$i]['emp_id'] = $temp['id'];
            $data[$i]['firstname'] = $temp['firstname'];
            $data[$i]['lastname'] = $temp['lastname'];
            $data[$i]['profile_image'] = $temp['profile_image'];
            $data[$i]['department'] = $temp['department'] ? $temp['department']['name'] : '';
        }

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filteredRecords) > 0 ? (int)$filteredRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['count'] : 0;
        return response()->json($response);
    }
}
 
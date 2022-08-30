<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function saveLeaveType(Request $request)
    {
        $data = $request->all();

        if (isset($data['status']))
        {
            $data["status"] = $this->optionRepo->OptionOfId($data["status"]);
        }
        if (isset($data['over_utilization']))
        {
            $data["over_utilization"] = $this->optionRepo->OptionOfId($data["over_utilization"]);
        }
        if (isset($data['gender']))
        {
            $data["gender"] = $this->optionRepo->OptionOfId($data["gender"]);
        }
        if ($request->id)
        {
            $leavetype = $this->leavetypeRepo->LeaveTypeOfId($request->id);
            $res=$this->leavetypeRepo->update($leavetype, $data);
            return $this->jsonResponse($res);
        }
        else
        {
            $prepared_data = $this->leavetypeRepo->prepareData($data);
            $create = $this->leavetypeRepo->create($prepared_data);
            return $this->jsonResponse($create);
        }
    }
    public function getLeaveType(Request $request)
    {
        return $this->leavetypeRepo->getLeaveTypeById($request->id);
    }
    public function getAllLeaveType()
    {
        return $this->leavetypeRepo->getAllLeaveType();
    }
    public function deleteleavetype(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value)
        {
            if (isset($value['isSelected']) && $value['isSelected'] == 1)
            {
                $leavetype = $this->leavetypeRepo->LeaveTypeOfId($value['id']);
                $res=$this->leavetypeRepo->delete($leavetype);
            }
        }
        return $this->jsonResponse($res);
    }


}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalenderController extends Controller
{
    public function saveCalenderMonth(Request $request)
    {
        $data = $request->calendermonth;
        if (isset($data["calendermonth"])) {
            $data["calendermonth"] = $this->optionRepo->OptionOfId($data["calendermonth"]);

        }
        if ($request->calendermonth['id']) {
            $company = $this->calenderRepo->CalenderOfId($request->calendermonth['id']);
            $res = $this->calenderRepo->update($company, $data);
            return $this->jsonResponse($res);
            //  return response()->json("Updated Successfully", 200);
        } else {
            $prepared_data = $this->calenderRepo->prepareData($data);
            $create = $this->calenderRepo->create($prepared_data);
            return $this->jsonResponse($create);

        }
    }

    function getCalenderById(Request $request)
    {
        return $this->calenderRepo->getCalenderByID($request->id);
    }
}

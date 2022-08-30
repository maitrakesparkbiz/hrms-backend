<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    function saveComment(Request $request)
    {
        $data = $request->all();
        $currentTime = new \DateTime();
        $today_startdatetime = $currentTime->createFromFormat("Y-m-d H:i:s", date("Y-m-d 00:00:00"));
        $today_enddatetime = $currentTime->createFromFormat("Y-m-d H:i:s", date("Y-m-d 23:59:59"));
        $check_in_data = $this->checkInRepo->getCheckInIdByDate($data['emp_id'], $today_startdatetime, $today_enddatetime);
        if (!empty($check_in_data)) {
            $check_in_data = $check_in_data[0];
            $data['check_in_id'] = $this->checkInRepo->CheckInOfId($check_in_data['id']);
            $data['emp_id'] = $this->userRepo->UserOfId($data['emp_id']);
            $prepareData = $this->commentsRepo->prepareData($data);
            $res = $this->commentsRepo->create($prepareData);
            if (!empty($res)) {

                return response()->json('success', 200);
            } else {
                return response()->json('error');
            }
        }
    }
}
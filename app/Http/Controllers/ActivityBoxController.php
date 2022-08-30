<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ActivityBoxController extends Controller
{
    function getEmpNotifications()
    {
        $emp_id = JWTAuth::user()->getId();
        $data = $this->activityBoxRepo->getEmpNotifications($emp_id);
        return response()->json($data);
    }
    function readAllNotificationsEmp()
    {
        $emp_id = JWTAuth::user()->getId();
        $data = $this->activityBoxRepo->getEmpUnReadNotifications($emp_id);
        $udpate['is_read'] = 1;
        foreach ($data as $row) {
            $id = $this->activityBoxRepo->ActivityBoxOfId($row['id']);
            $this->activityBoxRepo->update($id, $udpate);
        }
        return response()->json('success');
    }

    function deleteNotification(Request $request)
    {
        $id = $this->activityBoxRepo->ActivityBoxOfId($request->id);
        $this->activityBoxRepo->delete($id);
        return response()->json('success');
    }
}

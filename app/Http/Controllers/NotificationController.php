<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\SendStatusUpdate;

class NotificationController extends Controller
{
    function sendNotifications(Request $request)
    {
        $data = $request->all();
        foreach ($data as $row) {
//            event(new SendStatusUpdate($row['channel_name'],$row['from_emp'],$row['title'],$row['details']));
        }
        return response()->json('success');
    }
}


?>
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NewsEmployeeController extends Controller
{
    //
    function setIsReadNews(Request $request)
    {
        if($request->emp_id) {

            $data = $this->newsempRepo->setIsRead($request->emp_id);
            return response()->json(true);
        }
    }
}

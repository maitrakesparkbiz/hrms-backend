<?php

namespace App\Http\Controllers;
use Carbon\Carbon;

use Illuminate\Http\Request;

class EmployeeTimingsController extends Controller
{

    public function saveEmpTimings(Request $request)
    {
        $ins_timing = [];
        $data = $request->all();
        if (isset($data['record_timing']) && $data['record_timing'] != null) {
            foreach ($data['record_timing'] as $val) {
                $ins_timing['emp_id'] = $this->userRepo->UserOfId($val['emp_id']);
                $ins_timing['project_id'] = $this->companyProjectRepo->CompanyProjectOfId($val['project_id']);
                $ins_timing['record_date'] = Carbon::today();
                $ins_timing['record_hours'] = $val['record_hours'];
                $ins_timing['comment'] = $val['comment'];
                $prepare_data = $this->employeeTimingsRepo->prepareData($ins_timing);
                $this->employeeTimingsRepo->create($prepare_data);
                return response()->json(['res'=>'created']);
            }
        }
    }
    function getEmpTimingRecordById(Request $request)
    {

        $timing = $this->employeeTimingsRepo->getEmpTimingRecordsByEmp($request->emp_id);
        $total = $this->employeeTimingsRepo->getEmpTimingRecordsWithTotal($request->emp_id);
        $max_date = Carbon::parse($total[0]['max_date']);
        $min_date = Carbon::parse($total[0]['min_date']);
        $total['diff_date'] = $max_date->diffInDays($min_date);
        $total['total'] = (float)$total[0]['total'];
        //------------
        return response()->json(['timing' => [$timing, $total]]);
    }
}

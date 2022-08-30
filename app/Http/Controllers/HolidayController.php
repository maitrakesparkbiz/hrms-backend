<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class HolidayController extends Controller
{
    function getHolidayData(Request $request)
    {        
        if (\Auth::user()->hasPermissionTo(['SELF-CALENDER.VIEW', 'HOLIDAY-EVENTS.VIEW'])){
            $year = $request->year;
            $data = $this->holidayRepo->getHolidayDataByYear($year);
            if (isset($data)) {
                for ($i = 0; $i < count($data); $i++) {
                    $data[$i]['assoc_emp_id'] = $this->holidayEmployeeRepo->getAssocEmpIds($data[$i]['id']);
                }
                return response()->json($data);
            }
        }
        return response()->json(null);
    }

    function getHolidaysDatatable(Request $request)
    {
        $year = $request->year;
        $parameters = $request->parameters;
        $column_name = count($parameters['order']) > 0 ? $parameters['columns'][$parameters['order'][0]['column']]['name'] : 'created_at';
        $order = count($parameters['order']) > 0 ? $parameters['order'][0]['dir'] : 'DESC';
        $search = $parameters['search']['value'];
        $start = $parameters['start'];
        $length = $parameters['length'];
        $allRecords = $this->holidayRepo->countYearHolidays($year, $search);
        $filterRecords = $this->holidayRepo->getFilterRecords($year, $search);
        $data = $this->holidayRepo->getHolidaysDatatable($year, $column_name, $order, $search, $start, $length);

        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filterRecords) > 0 ? (int)$filterRecords[0]['count'] : 0;
        $response['recordsTotal'] = count($data);
        return response()->json($response);
    }

    function saveHoliday(Request $request)
    {
        $data = $request->all();
        $data['start_date'] = new \DateTime($data['start_date']);
        if (isset($data['is_company_event'])) {
            $data['end_date'] = new \DateTime($data['end_date']);
        } else {
            $data['end_date'] = $data['start_date'];
        }
        if (isset($data['id'])) {
            $id = $this->holidayRepo->HolidayOfId($data['id']);
            $this->holidayRepo->updateHolidayInfo($id, $data);
            return response()->json("updated");
        } else {
            if (isset($data['edit_id'])) {
                $res_data = $this->holidayEmployeeRepo->getAllRowOfId($data['edit_id']);
                foreach ($res_data as $value) {
                    $he_id = $this->holidayEmployeeRepo->HolidayEmployeeOfId($value[0]['id']);
                    $this->holidayEmployeeRepo->deleteRow($he_id);
                }
                $insert['holiday_id'] = $this->holidayRepo->HolidayOfId($data['edit_id']);
                foreach ($data['emp_id'] as $row) {
                    $insert['emp_id'] = $this->userRepo->UserOfId($row['item_id']);
                    $prepare_data = $this->holidayEmployeeRepo->prepareData($insert);
                    $this->holidayEmployeeRepo->create($prepare_data);
                }
                $update_data = $this->holidayRepo->updateHolidayInfo($insert['holiday_id'], $data);
                return response()->json('updated');
            } else {
                $prepare_data = $this->holidayRepo->prepareData($data);
                $holiday_id = $this->holidayRepo->create($prepare_data);
                $newData['holiday_id'] = $this->holidayRepo->HolidayOfId($holiday_id);
                if (isset($data['is_company_event'])) {
                    foreach ($data['emp_id'] as $value) {
                        $newData['emp_id'] = $this->userRepo->UserOfId($value['item_id']);
                        $prepare_data = $this->holidayEmployeeRepo->prepareData($newData);
                        $res = $this->holidayEmployeeRepo->create($prepare_data);
                    }
                    return response()->json("created");
                } else {
                    return response()->json("created");
                }
            }
        }
    }

    function getHolidayInfo(Request $request)
    {
        $id = $request->id;
        $data = $this->holidayRepo->getHolidayInfo($id);
        return $data;
    }

    function deleteHolidays(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            if (isset($value['isSelected']) && $value['isSelected'] == 1) {
                $holiday = $this->holidayRepo->HolidayOfId($value['id']);
                $data = $this->holidayRepo->delete($holiday);
            }
        }
        return response()->json('deleted');
    }

    function deleteEvent(Request $request)
    {
        $delete_id = $request->id;
        $holidayOdId = $this->holidayRepo->HolidayOfId($delete_id);
        $this->holidayEmployeeRepo->deleteAllHolidayEmp($delete_id);
        $this->holidayRepo->delete($holidayOdId);
        return response()->json('deleted');
    }

    function getHolidaysDashboard()
    {
        $data = $this->holidayRepo->getHolidaysDashboard();
        return response()->json($data);
    }
}
 
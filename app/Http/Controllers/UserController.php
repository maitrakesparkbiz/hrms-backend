<?php

namespace App\Http\Controllers;

use App\Entities\User;
use Carbon\Carbon;
use Exception;
use function GuzzleHttp\Psr7\parse_response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{

    function getUserById($id)
    {
        $user_id = $this->userRepo->getUser($id);
        return $user_id;
    }

    function login(Request $request)
    {

        $loginField = $request->input('email');
        $loginType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'employee_id';
        \request()->merge([$loginType => $loginField]);
        $credential = \request([$loginType, 'password']);
        $credential['user_exit_status'] = 1;
        $credential['allowed_login'] = 1;
        $token = null;
        try {
            if (!$token = JWTAuth::attempt($credential)) {
                return $this->errorResponse(['message' => 'invalid email or password...!']);
            }
        } catch (Exception $e) {
            return $this->errorResponse([
                'message' => 'Something went wrong..!',
                'error' => $e
            ]);
        }

        JWTAuth::setToken($token);
        $user = JWTAuth::toUser();
        $user_location = $this->userRepo->getUserLocation($user->getId());
        $data = [
            'token' => $token,
            'user' => [
                'firstname' => $user->getFirstName(),
                'email' => $user->getEmail(),
                'id' => $user->getId(),
                'permission' => $user->getPermissions(),
                'profile' => $user->getProfileImage(),
                'role' => ['name' => $user->getRoles()[0]->getName(), 'id' => $user->getRoles()[0]->getId()],
                'location_id' => $user_location['id'],
                'batch_data' => $user_location
            ]
        ];

        return $this->jsonResponse($data);
    }

    function register(Request $request)
    {
        $data = $request->only('email', 'password', 'name');
        $user = new User($data);
        $this->userRepo->saveUser($user);
        if ($user->getId() === null) {
            return $this->errorResponse(['message' => 'something went wrong']);
        }
        return $this->jsonResponse(['message' => 'register Successfully, login with your credentials!']);
    }


    function profile(Request $request)
    {
        if (Gate::denies('user.viewown')) {
            return $this->accessDeniedResponse();
        }

        $user = JWTAuth::parseToken()->authenticate();
        if ($user === null) {
            return $this->errorResponse(['message' => 'something went wrong!']);
        }

        $userProfileData = $this->userRepo->getProfile($user->getId());
        $data = ['user' => $userProfileData];
        return $this->jsonResponse($data);
    }

    function unlock(Request $request)
    {
        $password = $request->only('password')['password'];

        if (Hash::check($password, Auth::user()->getAuthPassword())) {
            return $this->jsonResponse(['message' => 'app unlock']);
        }
        return $this->errorResponse(['message' => 'Invalid Password']);
    }

    function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return $this->jsonResponse(['message' => 'Logout successful']);
    }

    function renewToken()
    {
        $token = JWTAuth::getToken();
        $new_token = JWTAuth::refresh($token);
        $data = [
            'token' => $new_token,
        ];
        return $this->jsonResponse($data);
    }


    function getCaptcha(Request $request)
    {
        return captcha_img('');
        //  return captcha_layout_stylesheet_url();
    }

    function backupnow()
    {
        $path = storage_path('app\laravel\\' . date('Y-m-d') . '.zip');
        if (!file_exists($path)) {
            Artisan::call('backup:run');
            //            return Response::download($path);
        } else {
            //            return Response::download($path);
        }
    }

    function myTestAddToLog()
    {
        LogActivity::addToLog("insert");
        //dd("inserted succesfully");
    }

    function getEmployeeById(Request $request)
    {
        $id = $request->employee_id;
        $data = $this->userRepo->getEmployeeByID($id);
        $temp = [
            'id' => $data['report_to']['id'],
            'firstname' => $data['report_to']['firstname'],
            'lastname' => $data['report_to']['lastname'],
            'profile_image' => $data['report_to']['profile_image'],
        ];
        unset($data['report_to']);
        $data['report_to'] = $temp;
        return $data;
    }

    function getEmployeeByIdSelf(Request $request)
    {
        $id = $request->id;
        return $this->userRepo->getEmployeeByIdSelf($id);
    }

    function getAllUsers()
    {
        $data = $this->userRepo->getUsers();
        return $this->jsonResponse($data);
    }

    function getAllUsersSelf()
    {
        $data = $this->userRepo->getAllUsersSelf();
        return response()->json($data);
    }

    function getDatatableUsers(Request $request)
    {
        $column_name = count($request->order) > 0 ? $request->columns[$request->order[0]['column']]['name'] : 'created_at';
        $order = count($request->order) > 0 ? $request->order[0]['dir'] : 'DESC';
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $durationSearch = $request->columns[4]['search']['value'];
        $statusSearch = $request->columns[0]['search']['value'];
        $endDuration = Carbon::today();
        $durationFlag = false;
        if ($durationSearch == 'this_month') {
            $durationSearch = Carbon::today()->startOfMonth();
            $endDuration = Carbon::today()->endOfMonth();
        } else if ($durationSearch == 'last_30_days') {
            $durationSearch = Carbon::today()->subDays(30);
            $endDuration = Carbon::today();
        } else if ($durationSearch == 'last_3_months') {
            $durationSearch = Carbon::today()->subMonths(3)->startOfMonth();
            $endDuration = Carbon::today()->subMonth()->endOfMonth();
        } else if ($durationSearch == 'last_6_months') {
            $durationSearch = Carbon::today()->subMonths(6)->startOfMonth();
            $endDuration = Carbon::today()->subMonth()->endOfMonth();
        } else if ($durationSearch == 'last_year') {
            $durationSearch = Carbon::today()->subYear();
            $endDuration = Carbon::today()->subYear()->endOfYear();
        } else if ($durationSearch == 'last_2_year') {
            $durationSearch = Carbon::today()->subYears(2);
            $endDuration = Carbon::today()->subYear()->endOfYear();
        } else if ($durationSearch == 'last_4_year') {
            $durationSearch = Carbon::today()->subYears(4);
            $endDuration = Carbon::today()->subYear()->endOfYear();
        } else {
            $durationSearch = 'all';
            $durationFlag = true;
        }

        $allRecords = $this->userRepo->countActiveEmployee();
        $data = $this->userRepo->getDatatableUsers($column_name, $order, $search, $start, $length, $durationSearch, $statusSearch, $endDuration, $durationFlag);
        $filteredRecords = $this->userRepo->countFilteredRecords($endDuration, $durationSearch, $statusSearch, $search, $durationFlag);
        // count attendance required attendance and leaves
        for ($i = 0; $i < count($data); $i++) {
            $total_days = 0;
            $leaves = 0;
            if ($data[$i]['probation_end_date']) {
                $joining_date = Carbon::parse(date_format($data[$i]['joining_date'], 'Y-m-d'));
                $prob_end_date = Carbon::parse(date_format($data[$i]['probation_end_date'], 'Y-m-d'));
                $temp_prob_date = Carbon::parse(date_format($data[$i]['probation_end_date'], 'Y-m-d'));
                $weekend_dates = [];

                if ($prob_end_date->lt(Carbon::today())) {
                    if (($prob_end_date->year(Carbon::today()->year))->lt(Carbon::today())) {
                        $mainDate = $temp_prob_date->year(Carbon::today()->year);
                    } else if (($prob_end_date->year(Carbon::today()->year - 1))->gt($joining_date)) {
                        $mainDate = $temp_prob_date->year(Carbon::today()->year - 1);
                    } else {
                        $mainDate = $temp_prob_date;
                    }

                    $satDate = Carbon::parse($mainDate)->next(Carbon::SATURDAY);
                    $sunDate = Carbon::parse($mainDate)->next(Carbon::SUNDAY);
                    $startDate = Carbon::parse($mainDate);
                    $endDate = Carbon::today();

                    $total_days = $startDate->diffInDays($endDate);
                    if (!$sunDate->gte($endDate)) {
                        for ($date = $sunDate; $date->lte($endDate); $date->addWeek()) {
                            $weekend_dates['sun'][] = $date->format('Y-m-d');
                        }
                        // exclude sundays
                        $total_days -= count($weekend_dates['sun']);
                        // end
                    }

                    if (!$satDate->gte($endDate)) {
                        for ($date = $satDate; $date->lte($endDate); $date->addWeek()) {
                            $weekend_dates['sat'][] = $date->format('Y-m-d');
                            $weekend_dates['month_sat'][$date->format('m')][] = $date->format('Y-m-d');
                        }
                        // exclude saturday as per batch
                        if ($data[$i]['alt_sat'] == 1) {
                            $count = 0;
                            foreach ($weekend_dates['month_sat'] as $month_sat) {
                                foreach ($month_sat as $sat) {
                                    $temp_date = Carbon::parse($sat);
                                    $dayMonth = $temp_date->format('d') . '-' . $temp_date->format('m');
                                    $month = $temp_date->format('F');
                                    $year = $temp_date->year;
                                    $str1 = $month . ' ' . $year . ' second saturday';
                                    $str2 = $month . ' ' . $year . ' fourth saturday';
                                    if (
                                        $dayMonth == date('d-m', strtotime($str1)) ||
                                        $dayMonth == date('d-m', strtotime($str2))
                                    ) {
                                        $count++;
                                    }
                                }
                            }
                            $total_days -= (count($weekend_dates['sat']) - $count);
                        } else {
                            $total_days -= count($weekend_dates['sat']);
                        }
                        // end
                        // get leave count
                        $leaveData = $this->leaveApprovedRepo->countLeavesBetweenDates($mainDate, $data[$i]['id']);
                        $leaves = $leaveData[0]['count'];
                        //end
                    }
                }
            }
            $data[$i]['total_days'] = $total_days;
            $data[$i]['leaves'] = $leaves;
        }
        // end


        $response['data'] = $data;
        $response['draw'] = $request->draw;
        $response['recordsFiltered'] = count($filteredRecords) > 0 ? (int)$filteredRecords[0]['active_count'] : 0;
        $response['recordsTotal'] = count($allRecords) > 0 ? (int)$allRecords[0]['active_count'] : 0;
        return response()->json($response);
    }


    function getContactUsers(Request $request)
    {
        $search = $request->search;
        $start = $request->start;
        $length = $request->length;
        $statusSearch = $request->statusSearch;
        $allRecords = $this->userRepo->countContactUsers($search, $statusSearch);
        $data = $this->userRepo->getContactUsers($search, $start, $length, $statusSearch);
        $response['data'] = $data;
        $response['recordsFiltered'] = count($allRecords) > 0 ? (int)$allRecords[0]['active_count'] : 0;
        $response['recordsTotal'] = count($data);
        return response()->json($response);
    }

    function getAllUsersWithLeaves()
    {
        $data = $this->userLeaveRepo->getAllUsersWithLeaves();
        return response()->json($data);
    }

    function getAllUsersWithFinalLeaves()
    {
        $data = $this->userLeaveRepo->getAllUsersWithLeaves();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['final_leaves'] = $this->leaveApprovedRepo->getEmpFinalLeaves($data[$i]['id']);
        }
        return response()->json($data);
    }

    function getManageUser()
    {
        $data = $this->userRepo->getManageUsers();
        return $this->jsonResponse($data);
    }

    function getSelectStatus(Request $request)
    {
        $data = $this->optionRepo->getAllOptionsBySelectId($request->id);
        return $this->jsonResponse($data);
    }

    function getMultipleSelectStatus(Request $request)
    {
        $ids = $request->all();
        $data = [];
        foreach ($ids as $id) {
            $data[$id] = $this->optionRepo->getAllOptionsBySelectId($id);
        }
        return $this->jsonResponse($data);
    }

    public
    function editAuthUser(Request $request)
    {
        $data = $request->all();

        $authuser = $this->userRepo->getUser($request->id);

        if ($authuser->getId()) {
            if ($data['new_password'] == $data['confirm_new_password'] && $data['confirm_new_password'] != '' && \Hash::check($data['password'], $authuser->getPassword())) {
                $data['password'] = bcrypt($request->new_password);
            } else {
                unset($data['password']);
                return response()->json('Not Update', 500);
            }
        }
        $user = $this->userRepo->getUser($request->id);
        $res = $this->userRepo->update($user, $data);
        return $this->jsonResponse($res);
    }

    public
    function checkEmail(Request $request)
    {
        $data = $request->employee_id;
        $res = $this->userRepo->checkEmployeeId($data);
        return response()->json($res);
    }

    public
    function checkEmployeeID(Request $request)
    {
        $data = $request->employee_id;
        $id = $request->id;
        $res = $this->userRepo->checkEmployeeIdUpdate($data, $id);
        return response()->json($res);
    }

    public
    function SaveEmployee(Request $request)
    {
        $data = $request->all();

        $default_role = $this->roleRepo->getEmployeeRoleId('employee');
        if (count($default_role) > 0) {
            $data['role'] = $default_role[0]['id'];
        } else {
            $data['role'] = null;
        }
        if (isset($data['role'])) {
            $data['roles'] = array($this->roleRepo->RoleOfId($data['role']));
        }
        if (isset($data['gender'])) {
            $data['gender'] = $this->optionRepo->OptionOfId($data['gender']);
        }
        if (isset($data['date_of_birth'])) {
            $data['date_of_birth'] = new \DateTime($data['date_of_birth']);
            $data['date_of_birth']->add(new \DateInterval('P1D'));
        }
        if (isset($data['department'])) {
            $data['department'] = $this->departmentRepo->DepartmentOfId($data['department']);
        }
        if (isset($data['designation'])) {
            $data['designation'] = $this->designationRepo->DesignationOfId($data['designation']);
        }
        if (isset($data['location'])) {
            $data['location'] = $this->locationRepo->LocationOfId($data['location']);
        }
        if (isset($data['report_to'])) {
            $data['report_to'] = $this->userRepo->UserOfId($data['report_to']);
        }
        if (isset($data['joining_date'])) {
            $data['joining_date'] = new \DateTime($data['joining_date']);
            //            $data['joining_date']->add(new \DateInterval('P1D'));
        }
        if (isset($data['probation_end_date'])) {
            $data['probation_end_date'] = new \DateTime($data['probation_end_date']);
            //            $data['probation_end_date']->add(new \DateInterval('P1D'));
        }
        if (isset($data['increment_date'])) {
            $data['increment_date'] = new \DateTime($data['increment_date']);
            //            $data['probation_end_date']->add(new \DateInterval('P1D'));
        }

        $prepareddata = $this->userRepo->prepareData($data);
        $user = $this->userRepo->create($prepareddata);
        if ($user) {
            $userLeave['emp_id'] = $this->userRepo->UserOfId($user);
            $prepareData = $this->userLeaveRepo->prepareData($userLeave);
            $this->userLeaveRepo->create($prepareData);
            return response()->json('Employee Added SuccesFully', 200);
        }
        return response()->json('error', 500);
    }

    function UpdateEmployee(Request $request)
    {
        $data = $request->all();

        if (isset($data['status'])) {
            $data['status'] = $this->optionRepo->OptionOfId($data['status']);
        }

        if (!$data['is_manager']) {
            $default_role = $this->roleRepo->getEmployeeRoleId('employee');
            if (count($default_role) > 0) {
                $data['role'] = $default_role[0]['id'];
            } else {
                $data['role'] = null;
            }
        }

        if (isset($data['role'])) {
            $data['roles'] = array($this->roleRepo->RoleOfId($data['role']));
        }

        if (isset($data['gender'])) {
            $data['gender'] = $this->optionRepo->OptionOfId($data['gender']);
        }

        if (isset($data['department'])) {
            $data['department'] = $this->departmentRepo->DepartmentOfId($data['department']);
        }
        if (isset($data['designation'])) {
            $data['designation'] = $this->designationRepo->DesignationOfId($data['designation']);
        }
        if (isset($data['location'])) {
            $data['location'] = $this->locationRepo->LocationOfId($data['location']);
        }

        if (isset($data['report_to'])) {
            $data['report_to'] = $this->userRepo->UserOfId($data['report_to']);
        }
        if (isset($data['probation_end_date'])) {
            $data['probation_end_date'] = new \DateTime($data['probation_end_date']);
            //            $data['probation_end_date']->add(new \DateInterval('P1D'));
        }
        if (isset($data['joining_date'])) {
            $data['joining_date'] = new \DateTime($data['joining_date']);
            //            $data['joining_date']->add(new \DateInterval('P1D'));
        }
        if (isset($data['increment_date'])) {
            $data['increment_date'] = new \DateTime($data['increment_date']);
            //            $data['joining_date']->add(new \DateInterval('P1D'));
        }
        if (isset($data['exit_date'])) {
            $data['exit_date'] = new \DateTime($data['exit_date']);
            $data['exit_date']->add(new \DateInterval('P1D'));
        }

        if (isset($data['date_of_birth'])) {
            $data['date_of_birth'] = new \DateTime($data['date_of_birth']);
            $data['date_of_birth']->add(new \DateInterval('P1D'));
        }
        if (isset($data['marriage_anniversary_date'])) {
            $data['marriage_anniversary_date'] = new \DateTime($data['marriage_anniversary_date']);
            $data['marriage_anniversary_date']->add(new \DateInterval('P1D'));
        }
        if (isset($data['passport_issue_date'])) {
            $data['passport_issue_date'] = new \DateTime($data['passport_issue_date']);
            $data['passport_issue_date']->add(new \DateInterval('P1D'));
        }
        if (isset($data['passport_expiry_date'])) {
            $data['passport_expiry_date'] = new \DateTime($data['passport_expiry_date']);
            $data['passport_expiry_date']->add(new \DateInterval('P1D'));
        }
        if (isset($data['visa_issue_date'])) {
            $data['visa_issue_date'] = new \DateTime($data['visa_issue_date']);
            $data['visa_issue_date']->add(new \DateInterval('P1D'));
        }
        if (isset($data['visa_expiry_date'])) {
            $data['visa_expiry_date'] = new \DateTime($data['visa_expiry_date']);
            $data['visa_expiry_date']->add(new \DateInterval('P1D'));
        }
        if ($request->password != '' && $request->password != '********') {
            $data['password'] = bcrypt($request->password);
        } else {
            unset($data['password']);
        }

        if (isset($data['user_account'])) {
            foreach ($data['user_account'] as $key => $value) {
                if ($value['id'] != '' || $value['account_type'] != '' || $value['account_holder_name'] != '' || $value['account_name'] != '' || $value['bank_code'] != '' || $value['bank_branch'] != '' || $value['bank_name'] != '' || $value['crn_number'] != '' || $value['account_number'] != '') {
                    $value['user'] = $this->userRepo->UserOfId($request->id);
                    if (isset($value['account_type'])) {
                        $value['account_type'] = $this->optionRepo->OptionOfId($value['account_type']);
                    }
                    if (isset($value['id'])) {
                        $user_account = $this->useraccountRepo->User_accountOfId($value['id']);
                        $this->useraccountRepo->update($user_account, $value);
                    } else {
                        $prepared_data = $this->useraccountRepo->prepareData($value);
                        $cr = $this->useraccountRepo->create($prepared_data);
                    }
                }
            }
        }

        if (isset($data['user_work'])) {

            foreach ($data['user_work'] as $key => $value) {

                if ($value['id'] != '' || $value['company_name'] != '' || $value['designation'] != '' || $value['from_date'] != '' || $value['details'] != '' || $value['to_date'] != '') {
                    $value['user'] = $this->userRepo->UserOfId($request->id);

                    if (isset($value['from_date'])) {
                        $value['from_date'] = new \DateTime($value['from_date']);
                        $value['from_date']->add(new \DateInterval('P1D'));
                    }
                    if (isset($value['to_date'])) {
                        $value['to_date'] = new \DateTime($value['to_date']);
                        $value['to_date']->add(new \DateInterval('P1D'));
                    }

                    if (isset($value['id'])) {
                        $user_work = $this->userworkRepo->User_work_experianceOfId($value['id']);
                        $this->userworkRepo->update($user_work, $value);
                    } else {
                        $prepared_data = $this->userworkRepo->prepareData($value);
                        $this->userworkRepo->create($prepared_data);
                    }
                }
            }
        }

        if (isset($data['user_qualification'])) {
            foreach ($data['user_qualification'] as $key => $value) {
                if (!empty($value['id']) || !empty($value['education_type']) || !empty($value['university_name']) || !empty($value['start_date']) || !empty($value['end_date']) || !empty($value['details']) || !empty($value['degree']) || !empty($value['doc_copy'])) {

                    $value['user'] = $this->userRepo->UserOfId($request->id);

                    if (isset($value['education_type'])) {
                        $value['education_type'] = $this->optionRepo->OptionOfId($value['education_type']);
                    }

                    if (isset($value['start_date'])) {
                        $value['start_date'] = new \DateTime($value['start_date']);
                        $value['start_date']->add(new \DateInterval('P1D'));
                    }

                    if (isset($value['end_date'])) {
                        $value['end_date'] = new \DateTime($value['end_date']);
                        $value['end_date']->add(new \DateInterval('P1D'));
                    }

                    if (isset($value['id'])) {
                        $user_qualification = $this->userqualificationRepo->User_qualificationOfId($value['id']);
                        $this->userqualificationRepo->update($user_qualification, $value);
                    } else {
                        $prepared_data = $this->userqualificationRepo->prepareData($value);
                        $this->userqualificationRepo->create($prepared_data);
                    }
                }
            }
        }

        if (array_key_exists('primary_account', $data) && isset($data['primary_account'])) {
            $data['primary_account'] = $this->optionRepo->OptionOfId($data['primary_account']);
        }
        $user = $this->userRepo->UserOfId($request->id);
        $this->userRepo->updateEmployee($user, $data);
        return response()->json('User Updated Successfully', 200);
    }

    function DeleteEmployee(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            if (isset($value['isSelected']) && $value['isSelected'] == 1) {
                $employee = $this->userRepo->getEmployeeByID($value['id']);
                if (isset($employee['user_work'])) {

                    foreach ($employee['user_work'] as $key1 => $value1) {
                        if (isset($value['id'])) {

                            $user_work = $this->userworkRepo->User_work_experianceOfId($value1['id']);
                            $this->userworkRepo->delete($user_work);
                        }
                    }
                }
                if (isset($employee['user_qualification'])) {
                    foreach ($employee['user_qualification'] as $key1 => $value1) {
                        if (isset($value['id'])) {

                            $user_qualification = $this->userqualificationRepo->User_qualificationOfId($value1['id']);
                            $this->userqualificationRepo->delete($user_qualification);
                        }
                    }
                }
                if (isset($employee['user_account'])) {

                    foreach ($employee['user_account'] as $key1 => $value1) {
                        if (isset($value['id'])) {

                            $user_account = $this->useraccountRepo->User_accountOfId($value1['id']);
                            $this->useraccountRepo->delete($user_account);
                        }
                    }
                }
                unset($employee['user_work']);
                unset($employee['user_qualification']);
                unset($employee['user_account']);

                $data = $this->userRepo->UserOfId($employee['id']);
                $res = $this->userRepo->delete($data);
            }
        }
        return response()->json('User deleted Successfully', 200);
    }

    function getAllUsersAttendance(Request $request)
    {
        $parameters = $request->dataTablesParameters;
        $column_name = count($parameters['order']) > 0 ? $parameters['columns'][$parameters['order'][0]['column']]['name'] : 'created_at';
        $order = count($parameters['order']) > 0 ? $parameters['order'][0]['dir'] : 'DESC';
        $search = $parameters['search']['value'];
        $start = $parameters['start'];
        $length = $parameters['length'];
        $currentTime = new \DateTime($request->attn_date);
        $general_data = $this->getGeneralDataAttn($currentTime);
        $startDate = date_format($currentTime, 'Y-m-d 00:00:00');
        $endDate = date_format($currentTime, 'Y-m-d 23:59:59');
        $allRecords = $this->userRepo->countAttnEmployee();
        $data = $this->userRepo->getAllUsersAttendance($column_name, $order, $search, $start, $length, $startDate, $endDate);
        $filteredRows = $this->userRepo->countFilteredRowsAttn($search);
        //create response array

        $newArr = [];
        $i = 0;

        foreach ($data as $row) {

            $break_status = false;

            $newArr[$i]['id'] = $row['id'];
            $newArr[$i]['name'] = $row['firstname'] . " " . $row['lastname'];
            $newArr[$i]['profile_image'] = $row['profile_image'];
            if (!empty($row['user_check_in'])) {
                $newArr[$i]['entry_time'] = $row['user_check_in'][0]['check_in_time']->format('h:i A');
                if (!empty($row['user_check_in'][0]['breaks'])) {
                    $timestamp = 0;
                    foreach ($row['user_check_in'][0]['breaks'] as $break) {
                        if ($break['break_in_time'] && !$break['break_out_time']) {
                            $break_status = true;
                        }
                        if (!empty($break['break_out_time'])) {
                            $timestamp += strtotime($break['break_out_time']->format('H:i:s')) - strtotime($break['break_in_time']->format('H:i:s'));
                        }
                    }
                    $diff = date('H:i', strtotime('00:00:00') + $timestamp);
                    $newArr[$i]['break_time'] = $diff;
                    if ($break_status) {
                        $newArr[$i]['break_pending'] = true;
                    }
                }
                if (!empty($row['user_check_in'][0]['check_out_time'])) {
                    $newArr[$i]['exit_time'] = $row['user_check_in'][0]['check_out_time']->format('h:i A');
                    $mainDiff = strtotime($newArr[$i]['exit_time']) - strtotime($newArr[$i]['entry_time']);
                    if (isset($newArr[$i]['break_time'])) {
                        $newArr[$i]['working_time'] = date('H:i', strtotime('00:00:00') + ($mainDiff - $timestamp));
                    } else {
                        $newArr[$i]['break_time'] = '00:00';
                        $newArr[$i]['working_time'] = date('H:i', strtotime('00:00:00') + $mainDiff);
                    }
                }
            }
            if (count($row['user_check_in']) > 0) {
                $newArr[$i]['attn_data'] = $row['user_check_in'][0];
                $commented = false;
                if (count($row['user_check_in'][0]['comments']) > 0) {
                    foreach ($row['user_check_in'][0]['comments'] as $comment) {
                        if (!$comment['response_text']) {
                            $commented  = true;
                        }
                    }
                }
                $newArr[$i]['commented'] = $commented;
            }
            $i++;
        }
        //end

        $res['data'] = $newArr;
        $res['other_data'] = $general_data;
        $response['data'] = $res;
        $response['draw'] = $parameters['draw'];
        $response['recordsFiltered'] = count($filteredRows) > 0 ? (int)$filteredRows[0]['active_count'] : 0;
        $response['recordsTotal'] =  count($allRecords) > 0 ? (int)$allRecords[0]['active_count'] : 0;

        return response()->json($response);
    }

    function getGeneralDataAttn($currentTime)
    {
        $startDate = date_format($currentTime, 'Y-m-d 00:00:00');
        $endDate = date_format($currentTime, 'Y-m-d 23:59:59');
        $present_count = 0;
        $absent_count = 0;
        $late_count = 0;
        $present_ratio = 0;
        $absent_ratio = 0;
        $late_ratio = 0;
        $data = $this->userRepo->getGeneralDataAttn($startDate, $endDate);


        foreach ($data as $row) {
            if ($row['check_in_id'] !== null) {
                $present_count++;
            } else {
                $absent_count++;
            }
            if ($row['is_late']) {
                $late_count++;
            }
        }

        if ($present_count > 0) {
            $present_ratio = round(($present_count * 100) / count($data));
        }

        if ($absent_count > 0) {
            $absent_ratio = round(($absent_count * 100) / count($data));
        }

        if ($late_count > 0) {
            $late_ratio = round(($late_count * 100) / count($data));
        }


        $res = [
            'present_count' => $present_count, 'present_ratio' => $present_ratio,
            'absent_count' => $absent_count, 'absent_ratio' => $absent_ratio,
            'late_count' => $late_count, 'late_ratio' => $late_ratio
        ];

        return $res;
    }

    function getUpcomingBdays()
    {
        $data = $this->userRepo->getUpcomingBdays();
        $holidays = $this->holidayRepo->getHolidaysDashboard();
        return response()->json(['birthdays' => $data, 'holidays' => $holidays]);
    }

    function getActiveEmployeesCount()
    {
        $count = $this->userRepo->getActiveEmployees();
        return response()->json($count);
    }

    function cronProbUserLeave()
    {
        $today = new \DateTime("now");
        $data = $this->userRepo->getCronUserData($today);
        $current_year = date('Y');

        if (isset($data)) {
            foreach ($data as $row) {
                $leaves = 0;
                //to get year of probation end date
                $end_year = $row['end_date']->format('Y');

                //get first full date of year comes after probation year
                $date_to_compare = date_create('01-01-' . ($end_year + 1));

                //date difference between probation date and next year date
                $date_diff = $row['end_date']->diff($date_to_compare);

                //difference in month
                $month = $date_diff->format('%m');

                if (($end_year + 1) == $current_year) {
                    //after 1 year of probation end

                    //number of days in probation end date month of current year
                    $day_count_in_month = cal_days_in_month(CAL_GREGORIAN, $row['end_date']->format('m'), ($end_year + 1));

                    //day difference between current month days and probation end date days
                    $day_diff = $day_count_in_month - $row['end_date']->format('d');
                    if ($day_count_in_month != ($day_diff + 1)) {
                        $month += 1;
                    }
                    if ($month <= 3) {
                        $leaves += 1;
                    } else {
                        $leaves += ceil($month / 3);
                    }
                    $leave_id = $this->userLeaveRepo->getLeaveIdByEmp($row['id']);
                    $data['id'] = $this->userLeaveRepo->UserLeaveOfId($leave_id['id']);
                    $data['sl'] = $leaves;
                    $data['one_year_completed'] = 1;
                    $this->userLeaveRepo->update($data['id'], $data);

                    //end
                } else {
                    //after probation end

                    $day_count_in_month = cal_days_in_month(CAL_GREGORIAN, $row['end_date']->format('m'), $end_year);
                    $day_diff = $day_count_in_month - $row['end_date']->format('d');

                    $leaves += (int)$month;
                    if ($day_count_in_month != ($day_diff + 1)) {
                        if ($day_diff >= 15) {
                            $leaves += 1;
                        }
                    }
                    $leaves = (float)$leaves / 2;
                    $data = [];
                    $leave_id = $this->userLeaveRepo->getLeaveIdByEmp($row['id']);
                    $data['id'] = $this->userLeaveRepo->UserLeaveOfId($leave_id['id']);
                    $data['cl'] = $leaves;
                    $data['pl'] = $leaves;
                    if ($day_count_in_month != ($day_diff + 1)) {
                        if ($day_diff < 15) {
                            $data['pl'] += 0.5;
                        }
                    }
                    $data['employment_started'] = 1;
                    $this->userLeaveRepo->update($data['id'], $data);
                    //end
                }
            }
            return 'all records updated';
        }
        return 'no data found';
        //end
    }

    function cronAllUserLeave()
    {
        $data = $this->userLeaveRepo->getRegularUsers();
        if (isset($data)) {
            foreach ($data as $row) {
                $data = [];
                $data['id'] = $this->userLeaveRepo->UserLeaveOfId($row['id']);
                $data['cl'] = 6;
                $data['pl'] = 6 + $row['pl'];
                $data['sl'] = 4;
                $data['used_upl'] = 0;
                $this->userLeaveRepo->update($data['id'], $data);
            }
        }
    }

    function getCurrentMonthIncrementEmp()
    {
        $data = $this->userRepo->getCurrentMonthIncrementEmp();
        $active_count = $this->userRepo->getActiveEmployees();
        $onProbation = $this->userRepo->getOnProbationEmployees();
        $today_interview_count = $this->jobInterviewRepo->getTodayInterviewCount();
        $scheduled_count = $this->jobApplicationsRepo->getScheduledCount();
        $total_candidate = $this->jobApplicationsRepo->getTotalCandidatesCount();
        return response()->json(
            [
                'increments' => $data,
                'active_emp' => $active_count[0],
                'on_probation' => $onProbation,
                'jobs' => [
                    'today_interview' => count($today_interview_count) > 0 ? $today_interview_count[0]['count'] : 0,
                    'scheduled_count' => count($scheduled_count) > 0 ? $scheduled_count[0]['count'] : 0,
                    'total_candidate' => count($total_candidate) > 0 ? $total_candidate[0]['count'] : 0
                ]
            ]
        );
    }

    function getMemberData(Request $request)
    {
        $emp_id = $request->emp_id;
        $attendance = $this->checkInRepo->getMemberTodayCheckin($emp_id);
        if (count($attendance) > 0) {
            $temp = $attendance[0];
            unset($attendance[0]);
            $attendance['check_in_time'] = $temp['check_in_time'];
            $attendance['check_out_time'] = $temp['check_out_time'];
            $attendance['breaks'] = $temp['breaks'];
        }
        $leaves = $this->leaveapplicationRepo->getUpcomingLeavesMember($emp_id);
        return response()->json(['attn' => $attendance, 'leaves' => $leaves]);
    }

    function getAllUsersExceptHR()
    {
        $data = $this->userRepo->getAllUsersExceptHR();
        return response()->json($data);
    }
    function getAllJBaSelfBA()
    {
        $emp_id = JWTAuth::user()->getId();
        $data = $this->userRepo->getAllJBaSelfBA($emp_id);
        return response()->json($data);
    }

    function getUserLetterData(Request $request)
    {
        $emp_id = $request->id;
        $signatory_emp_id = JWTAuth::user()->getId();
        $data = $this->userRepo->getUserLetterData($emp_id);
        if (!empty($data)) {
            $data = $data[0];
            $signatory_data = $this->userRepo->getSignatoryData($signatory_emp_id);
            if (!empty($signatory_data)) {
                $signatory_data = $signatory_data[0];
                foreach ($signatory_data as $key => $value) {
                    $data[$key] = $value;
                }
            }
            $company_data = $this->companyRepo->getCompanyLetterData();
            if (!empty($company_data)) {
                $company_data = $company_data[0];
                foreach ($company_data as $key => $value) {
                    $data[$key] = $value;
                }
            }
        }

        if ($data['EMPLOYEE_DOB']) {
            $data['EMPLOYEE_DOB'] = $data['EMPLOYEE_DOB']->format('d-m-Y');
        }
        if ($data['EMPLOYEE_EXIT_DATE']) {
            $data['EMPLOYEE_EXIT_DATE'] = $data['EMPLOYEE_EXIT_DATE']->format('d-m-Y');
        }
        if ($data['EMPLOYEE_JOINING_DATE']) {
            $data['EMPLOYEE_JOINING_DATE'] = $data['EMPLOYEE_JOINING_DATE']->format('d-m-Y');
        }
        if ($data['EMPLOYEE_PROBATION_END_DATE']) {
            $data['EMPLOYEE_PROBATION_END_DATE'] = $data['EMPLOYEE_PROBATION_END_DATE']->format('d-m-Y');
        }
        if ($data['OFFICE_END_TIME']) {
            $data['OFFICE_END_TIME'] = $data['OFFICE_END_TIME']->format('h:m a');
        }
        if ($data['OFFICE_START_TIME']) {
            $data['OFFICE_START_TIME'] = $data['OFFICE_START_TIME']->format('h:m a');
        }

        return response()->json($data);
    }
}

<?php

use Illuminate\Support\Facades\Route;

/**
 * |--------------------------------------------------------------------------
 * | API Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register API routes for your application. These
 * | routes are loaded by the RouteServiceProvider within a group which
 * | is assigned the "api" middleware group. Enjoy building your API!
 * |
 */

Route::post('/login', 'UserController@login');
Route::get('/testApi', 'apiController@testApi');
Route::post('/register', 'UserController@register');
Route::get('getcaptcha', 'UserController@getCaptcha');
Route::get('/getOpeningsPublic', 'JobOpeningController@getOpeningsPublic');
Route::get('/getOpeningById{edit_id}', 'JobOpeningController@getOpeningById');
//company logo
Route::get('getlogopath', 'CompanyController@getlogopath');
Route::post('/uploadlogo', 'CompanyController@uploadlogo');
//cron probation user leaves
Route::get('cronProbUserLeave', 'UserController@cronProbUserLeave');
Route::get('cronAllUserLeave', 'UserController@cronAllUserLeave');
Route::get('configCache', 'apiController@configCache');
//end

// cron attendance report
Route::get('sendAttendanceReport/{date?}', 'AttendanceController@sendAttendanceReport');
// end
Route::group(['middleware' => ['AuthJWT']], function () {
    //user
    Route::get('/profile', 'UserController@profile');
    Route::delete('/logout', 'UserController@logout');
    Route::post('/unlock', 'UserController@unlock');
    Route::post('/addNewPermission', 'PermissionController@addNewPermission');
    Route::post('user/update_user_password', 'UserController@editAuthUser');
    Route::post('user/check_email', 'UserController@checkEmail');
    Route::post('user/check_employeeID', 'UserController@checkEmployeeID');
    Route::post('user/save_employee', 'UserController@SaveEmployee');
    Route::post('user/update_employee', 'UserController@UpdateEmployee');
    Route::post('user/delete_employee', 'UserController@DeleteEmployee');
    Route::post('user/getEmployeeById', 'UserController@getEmployeeById');
    Route::get('user/getEmployeeByIdSelf{id}', 'UserController@getEmployeeByIdSelf');
    Route::get('user/get_all_users', 'UserController@getAllUsers');
    Route::get('user/get_manager_user', 'UserController@getManageUser');
    Route::get('user/getAllUsersWithLeaves', 'UserController@getAllUsersWithLeaves');
    Route::get('user/getAllUsersWithFinalLeaves', 'UserController@getAllUsersWithFinalLeaves');
    Route::post('user/getAllUsersAttendance', 'UserController@getAllUsersAttendance');
    Route::get('user/getUpcomingBdays', 'UserController@getUpcomingBdays');
    Route::post('user/getMemberData', 'UserController@getMemberData');
    Route::get('user/getAllUsersSelf', 'UserController@getAllUsersSelf');
    Route::get('user/getAllUsersExceptHR', 'UserController@getAllUsersExceptHR');
    Route::post('user/getContactUsers', 'UserController@getContactUsers');
    Route::get('user/getAllJBaSelfBA', 'UserController@getAllJBaSelfBA');
    Route::post('user/getUserLetterData', 'UserController@getUserLetterData');
    Route::post('user/saveEmpTimings', 'EmployeeTimingsController@saveEmpTimings');
    Route::post('user/getEmpTimingsRecordsById', 'EmployeeTimingsController@getEmpTimingRecordById');
    //option-master
    Route::post('option/get_select_options', 'UserController@getSelectStatus');
    Route::post('option/get_multiple_select_options', 'UserController@getMultipleSelectStatus');

    //company
    Route::post('company/get_companys', 'CompanyController@getCompanys');
    Route::post('company/save_company', 'CompanyController@saveCompany');
    Route::post('company/get_company', 'CompanyController@getCompany');
    Route::get('company/getDateTimeFormat', 'CompanyController@getDateTimeFormat');

    //department
    Route::post('department/save_department', 'DepartmentController@saveDepartment');
    Route::post('department/get_departments', 'DepartmentController@getDepartments');
    Route::get('department/get_all_department', 'DepartmentController@getAllDepartment');
    Route::post('department/delete_department', 'DepartmentController@deleteDepartment');

    //designation
    Route::post('designation/delete_designation', 'DesignationController@deleteDesignation');
    Route::post('designation/save_designation', 'DesignationController@saveDesignation');
    Route::get('designation/get_all_designation', 'DesignationController@getAllDesignation');

    //location
    Route::post('location/delete_location', 'LocationController@deleteLocation');
    Route::post('location/save_location', 'LocationController@saveLocation');
    Route::get('location/get_all_location', 'LocationController@getAllLocation');
    Route::get('location/getAllLocationOpt', 'LocationController@getAllLocationOpt');
    Route::get('location/getUserBatch', 'LocationController@getUserBatch');

    //expense_category
    Route::post('expense_category/get_expense_categorys', 'ExpenseCategoryController@getExpense_categorys');
    Route::post('expense_category/delete_expense_category', 'ExpenseCategoryController@deleteExpense_category');
    Route::post('expense_category/get_expense_category', 'ExpenseCategoryController@getExpense_category');
    Route::post('expense_category/save_expense_category', 'ExpenseCategoryController@saveExpense_category');
    Route::get('expense_category/get_all_expense_category', 'ExpenseCategoryController@getAllExpense_category');

    //award
    Route::post('award/delete_award', 'AwardController@deleteAward');
    Route::post('award/get_award', 'AwardController@getAward');
    Route::post('award/save_award', 'AwardController@saveAward');
    Route::get('award/get_all_award', 'AwardController@getAllAward');

    //calender
    Route::post('calender/save_calender', 'CalenderController@saveCalenderMonth');
    Route::post('calender/getCalenderByID', 'CalenderController@getCalenderById');

    //leave_type
    Route::post('leave_type/get_leave_types', 'LeaveTypeController@getLeave_types');
    Route::post('leave_type/delete_leave_type', 'LeaveTypeController@deleteleavetype');
    Route::post('leave_type/get_leave_type', 'LeaveTypeController@getLeaveType');
    Route::post('leave_type/save_leave_type', 'LeaveTypeController@saveLeaveType');
    Route::get('leave_type/get_all_leave_type', 'LeaveTypeController@getAllLeaveType');

    //permission
    Route::get('permission/get_all_permission', 'PermissionController@getAllPermission');

    //role
    Route::post('role/save_role', 'RoleController@saveRole');
    Route::post('role/get_role_permission', 'RoleController@getRolewithPermission');
    Route::post('role/delete_role', 'RoleController@deleteRole');
    Route::get('role/get_all_roles', 'RoleController@getAllRoles');
    Route::get('role/get_roll_without_employee', 'RoleController@getRoleWithoutEmployee');
    Route::get('role/rolePermissions{role_id}', 'RoleController@getRoleData');

    //leave-application
    Route::post('leave_application/save_leave_application', 'LeaveApplicationController@saveLeaveApplication');
    Route::post('leave_application/get_leave_application', 'LeaveApplicationController@getLeaveApplicationByID');
    Route::get('leave_application/get_all_leave_application', 'LeaveApplicationController@getAllLeaveApplication');
    Route::post('leave_application/update_accept_status', 'LeaveApplicationController@updateAcceptStatus');
    Route::post('leave_application/update_reject_status', 'LeaveApplicationController@updateRejectStatus');
    Route::post('leave_application/update_cancel_status', 'LeaveApplicationController@updateCancelStatus');
    Route::post('leave_application/delete_leave_application', 'LeaveApplicationController@deleteLeaveApplication');
    Route::post('leave_application/get_remaining_leave', 'LeaveApplicationController@getLeaveRemaining');
    Route::get('leave_application/getFirstApprovedApplications', 'LeaveApplicationController@getFirstApprovedApplications');
    Route::get('leave_application/getPendingLeavesCount', 'LeaveApplicationController@getPendingLeavesCount');
    Route::post('leave_approved/finalApproveLeave', 'LeaveApprovedController@finalApproveLeave');
    Route::get('leave_approved/getAllApprovedLeaves', 'LeaveApprovedController@getAllApprovedLeaves');
    Route::post('leave_approved/finalRejectLeave', 'LeaveApprovedController@finalRejectLeave');
    Route::post('leave_approved/saveFinalLeave', 'LeaveApprovedController@saveFinalLeave');
    Route::post('leave_approved/updateFinalLeave', 'LeaveApprovedController@updateFinalLeave');
    Route::post('leave_application/getEmployeeleaves', 'LeaveApplicationController@getEmployeeleaves');
    Route::post('leave_approved/getEmpFinalLeaves', 'LeaveApprovedController@getEmpFinalLeaves');
    Route::post('leave_approved/getEmpUPL', 'LeaveApprovedController@getEmpUPL');

    //leave balance
    Route::post('leave_application/saveLeaveBalance', 'UserLeaveController@saveLeaveBalance');
    Route::get('leave_application/getLeaveBalance/{id}', 'UserLeaveController@getLeaveBalance');

    //self leave general data
    Route::post('leave_application/getSelfLeaveRequiredData', 'LeaveApplicationController@getSelfLeaveRequiredData');


    //User leave
    Route::post('user_leave/getLeaveByEmp', 'UserLeaveController@getLeaveByEmp');


    //contact
    Route::post('contact/save_contact', 'ContactController@saveContact');
    Route::post('contact/delete_contact', 'ContactController@deleteContact');
    Route::get('contact/get_contact', 'ContactController@getAllContact');
    Route::post('contact/get_contactByID', 'ContactController@getContactByID');


    //appreciation
    Route::post('appreciation/save_appreciation', 'AppreciationController@saveAppreciation');
    Route::post('appreciation/delete_appreciation', 'AppreciationController@deleteAppreciation');
    Route::get('appreciation/get_appreciation', 'AppreciationController@getAllAppreciation');
    Route::post('appreciation/get_appreciationByID', 'AppreciationController@getAppreciationById');
    Route::get('appreciation/getAppreciationByEmpId{emp_id}', 'AppreciationController@getAppreciationByEmpId');
    Route::post('appreciation/delete_appreciation', 'AppreciationController@deleteAppreciation');

    //news
    Route::post('news/save_news', 'NewsController@saveNews');
    Route::get('news/get_all_news', 'NewsController@getAllNews');
    Route::post('news/get_newsById', 'NewsController@getNewsByID');
    Route::post('news/delete_news', 'NewsController@deletenews');
    Route::post('news/publish_news', 'NewsController@publish_news');
    Route::post('news/setIsReadNews', 'NewsEmployeeController@setIsReadNews');
    Route::get('news/getNewsByEmployee', 'NewsController@getNewsByEmployee');

    //expense
    Route::post('expense/save_expense', 'ExpenseController@saveExpense');
    Route::get('expense/get_allexpense', 'ExpenseController@getAllExpense');
    Route::post('expense/delete_expense', 'ExpenseController@deleteExpense');
    Route::post('expense/get_expenseById', 'ExpenseController@getExpenseById');
    Route::post('expense/get_all_claims', 'ExpenseController@getAllClaims');
    Route::get('expense/get_all_re_expense', 'ExpenseController@getAllReExpense');
    Route::post('expense/approve_claim', 'ExpenseController@approveClaim');
    Route::post('expense/reject_claim', 'ExpenseController@rejectClaim');
    Route::get('expense/getClaimsSelf{emp_id}', 'ExpenseController@getClaimsSelf');
    Route::post('expense/getExpenseByYear', 'ExpenseController@getExpenseByYear');
    Route::get('expense/getPendingClaimsCount', 'ExpenseController@getPendingClaimsCount');

    //jobs
    Route::get('/getAllOpenings', 'JobOpeningController@getAllOpenings');
    Route::post('/saveOpening', 'JobOpeningController@saveOpening');
    Route::get('/deleteOpening{delete_id}', 'JobOpeningController@deleteOpening');
    Route::get('/getJobOptionsData', 'JobOpeningController@getJobOptionsData');
    Route::get('/getAllJobApplications', 'JobApplicationsController@getAllJobApplications');
    Route::get('/getAllJobApplicationsInt', 'JobApplicationsController@getAllJobApplicationsInt');
    Route::post('/saveApplication', 'JobApplicationsController@saveApplication');
    Route::post('/saveCandidatesInfo', 'CandidatesInfoController@saveCandidatesInfo');
    Route::get('/getApplicationById{id}', 'JobApplicationsController@getApplicationById');
    Route::get('/getCandidatesInfoById{id}', 'CandidatesInfoController@getCandidatesInfoById');
    Route::post('/rescheduleInterview', 'JobApplicationsController@rescheduleInterview');
    Route::get('/getAllOpeningsSelf', 'JobOpeningController@getAllOpeningsSelf');
    Route::get('/getApplicationByEmpId{emp_id}', 'JobApplicationsController@getApplicationByEmpId');
    Route::get('/getAllInterviews{id}', 'JobApplicationsController@getAllInterviews');

    //attendance
    Route::post('attendance/presentEvent', 'AttendanceController@presentEvent');
    Route::post('saveComment', 'CommentController@saveComment');
    Route::get('attendance/checkCurrentStatus', 'AttendanceController@checkCurrentStatus');
    Route::get('attendance/generateListings', 'AttendanceController@generateListings');
    Route::post('attendance/getEmpMonthYearData', 'AttendanceController@getEmpMonthYearData');
    Route::post('attendance/updateAttendance', 'AttendanceController@updateAttendance');
    Route::post('checkIn/getCheckInDataById', 'CheckInController@getCheckInDataById');
    Route::post('attendance/getEmpProductivityRatio', 'CheckInController@getEmpProductivityRatio');
    Route::post('attendance/getReportDataOfAllUser', 'AttendanceController@getReportDataOfAllUser');
    Route::post('attendance/exportReportToExcel', 'AttendanceController@exportReportToExcel');
    Route::post('checkIn/getUserAttendanceByDate', 'CheckInController@getUserAttendanceByDate');

    //holiday
    Route::get('/getHolidayData{year}', 'HolidayController@getHolidayData');
    Route::post('/saveHoliday', 'HolidayController@saveHoliday');
    Route::get('/getHolidayInfo{id}', 'HolidayController@getHolidayInfo');
    Route::post('/deleteHolidays', 'HolidayController@deleteHolidays');
    Route::get('/deleteEvent{id}', 'HolidayController@deleteEvent');

    //email settings
    Route::post('/saveEmailSettings', 'EmailSettingController@saveEmailSettings');
    Route::get('/getEmailSettings', 'EmailSettingController@getEmailSettings');

    //email templates
    Route::post('/saveEmailTemplate', 'EmailTemplatesController@saveEmailTemplate');
    Route::post('/deleteTemplate', 'EmailTemplatesController@deleteTemplate');
    Route::post('/getTemplateById', 'EmailTemplatesController@getTemplateById');
    Route::post('/saveTemplate', 'EmailTemplatesController@saveTemplate');
    Route::get('/getEmailTemplate', 'EmailTemplatesController@getEmailTemplate');
    Route::get('/getAllTemplatesOption', 'EmailTemplatesController@getAllTemplatesOption');

    //dashboard admin
    Route::get('dashboard/getAttendanceDashboard', 'AttendanceController@getAttendanceDashboard');
    Route::get('dashboard/getPendingClaims', 'ExpenseController@getPendingClaims');
    Route::get('dashboard/getLeaveDataDashboard', 'LeaveApplicationController@getLeaveDataDashboard');
    Route::post('dashboard/getYearMonthFinalLeaves', 'LeaveApprovedController@getYearMonthFinalLeaves');
    Route::get('dashboard/getCurrentMonthIncrementEmp', 'UserController@getCurrentMonthIncrementEmp');
    Route::post('dashboard/getLeavesSelfDashboard', 'LeaveApplicationController@getLeavesSelfDashboard');
    Route::get('dashboard/getHolidaysDashboard', 'HolidayController@getHolidaysDashboard');
    Route::get('dashboard/getPendingCount', 'CompanyController@getPendingCount');
    Route::get('dashboard/getPendingCountSelf', 'CompanyController@getPendingCountSelf');


    //Team
    Route::post('team/addTeam', 'TeamController@addTeam');
    Route::post('team/getTeamById', 'TeamController@getTeamById');
    Route::post('team/deleteTeam', 'TeamController@deleteTeam');




    //Project Sales
    Route::post('project_sales/saveProject', 'ProjectController@saveProject');
    Route::post('project_sales/getProjectById', 'ProjectController@getProjectById');
    Route::get('project_sales/getAllBASelfSales', 'ProjectController@getAllBASelfSales');

    // for approved
    Route::post('project_sales/saveFinalApproveProject', 'ApprovedProjectController@saveFinalApproveProject');
    Route::post('project_sales/getFinalApproveProjectById', 'ApprovedProjectController@getFinalApproveProjectById');
    Route::post('project_sales/updateFinalApproveProject', 'ApprovedProjectController@updateFinalApproveProject');
    Route::post('project_sales/startApprovedProject', 'ApprovedProjectController@startApprovedProject');


    //Project Ba
    Route::post('project_BA/saveBaProject', 'ProjectBaController@saveBaProject');
    Route::post('project_BA/getBaProjectById', 'ProjectBaController@getBaProjectById');
    Route::get('project_BA/getAllJBaSelfBA', 'ProjectBaController@getAllJBaSelfBA');
    Route::post('project_BA/getAllEmpTimingRecords', 'ProjectBaController@getAllEmpTimingRecords');



    //Project Jr Ba
    Route::post('project_Jr_BA/saveBaProject', 'ProjectJrBaController@saveBaProject');
    Route::post('project_Jr_BA/getBaProjectById', 'ProjectJrBaController@getBaProjectById');
    Route::post('project_Jr_BA/getJrBaDataofProject', 'ProjectJrBaController@getJrBaDataofProject');

    //Project Comments
    Route::post('project_comments/getProjectComments', 'ProjectCommentsController@getProjectComments');
    Route::post('project_comments/doCommentSr', 'ProjectCommentsController@doCommentSr');
    Route::post('project_comments/doCommentJr', 'ProjectCommentsController@doCommentJr');
    Route::post('project_comments/getSrProjectComments', 'ProjectCommentsController@getSrProjectComments');
    Route::post('project_comments/getJrProjectComments', 'ProjectCommentsController@getJrProjectComments');

    // for approved
    Route::post('project_comments/doApprovedCommentSr', 'ApprovedProjectCommentsController@doApprovedCommentSr');
    Route::post('project_comments/doApprovedCommentJr', 'ApprovedProjectCommentsController@doApprovedCommentJr');
    Route::post('project_comments/getApprovedProjectComments', 'ApprovedProjectCommentsController@getApprovedProjectComments');

    //candidate info
    Route::post('candidate/sendCandidateMail', 'CandidatesInfoController@sendCandidateMail');

    //datatable data
    Route::post('datatable/getAllLeaves', 'LeaveApplicationController@getAllLeaveApplication');
    Route::post('datatable/getAllApprovedLeaves', 'LeaveApprovedController@getAllApprovedLeaves');
    Route::post('datatable/getFirstApprovedApplications', 'LeaveApplicationController@getFirstApprovedApplications');
    Route::post('datatable/get_all_claims', 'ExpenseController@getAllClaims');
    Route::post('datatable/getSelfExpense', 'ExpenseController@getClaimsSelf');
    Route::post('datatable/getAllExpense', 'ExpenseController@getAllExpense');
    Route::post('datatable/getAllOpenings', 'JobOpeningController@getAllOpenings');
    Route::post('datatable/getAllJobApplications', 'JobApplicationsController@getAllJobApplications');
    Route::post('datatable/getAllCandidatesInfo', 'CandidatesInfoController@getAllCandidatesInfo');
    Route::post('datatable/getAllJobApplicationsInt', 'JobApplicationsController@getAllJobApplicationsInt');
    Route::post('datatable/getAllJobApplicationsTodayInt', 'JobApplicationsController@getAllJobApplicationsTodayInt');
    Route::post('datatable/getAllNews', 'NewsController@getAllNews');
    Route::post('datatable/getDatatableUsers', 'UserController@getDatatableUsers');
    Route::post('datatable/getAllUsersAttendance', 'UserController@getAllUsersAttendance');
    Route::post('datatable/getAllOpeningsSelf', 'JobOpeningController@getAllOpeningsSelf');
    Route::post('datatable/getApplicationByEmpId', 'JobApplicationsController@getApplicationByEmpId');
    Route::post('datatable/getUserAttendanceByDate', 'CheckInController@getUserAttendanceByDate');
    Route::post('datatable/getLeaveRequiredData', 'LeaveApplicationController@getLeaveRequiredData');
    Route::post('datatable/getEmpAllTakenLeaves', 'LeaveApprovedController@getEmpAllTakenLeaves');
    Route::post('datatable/getAllDeptDatatable', 'DepartmentController@getAllDeptDatatable');
    Route::post('datatable/getAllDesDatatable', 'DesignationController@getAllDesDatatable');
    Route::post('datatable/getAllLocationDatatable', 'LocationController@getAllLocationDatatable');
    Route::post('datatable/getAllCatDatatable', 'ExpenseCategoryController@getAllCatDatatable');
    Route::post('datatable/getAllAwardDatatable', 'AwardController@getAllAwardDatatable');
    Route::post('datatable/getHolidaysDatatable', 'HolidayController@getHolidaysDatatable');
    Route::post('datatable/getAllAppreciationDatatable', 'AppreciationController@getAllAppreciationDatatable');
    Route::post('datatable/getAllContactsDatatable', 'ContactController@getAllContactsDatatable');
    Route::post('datatable/getAllTeams', 'TeamController@getAllTeams');
    Route::post('datatable/getSelfTeamData', 'TeamController@getSelfTeamData');
    Route::post('datatable/getAllProjectsDataTable', 'ProjectController@getAllProjectsDataTable');
    Route::post('datatable/getAllProjectsBaDataTable', 'ProjectBaController@getAllProjectsBaDataTable');
    Route::post('datatable/getAllProjectsJrBaDataTable', 'ProjectJrBaController@getAllProjectsJrBaDataTable');
    Route::post('datatable/getAllRolesDatatable', 'RoleController@getAllRolesDatatable');
    Route::post('datatable/getAllApprovedProjectsDataTable', 'ApprovedProjectController@getAllApprovedProjectsDataTable');
    Route::post('datatable/getAllApprovedProjectsBaDataTable', 'ApprovedProjectController@getAllApprovedProjectsBaDataTable');
    Route::post('datatable/getAllApprovedProjectsJrBaDataTable', 'ApprovedProjectController@getAllApprovedProjectsJrBaDataTable');

    Route::post('datatable/getTeamAttendanceByDate', 'CheckInController@getTeamAttendanceByDate');
    Route::post('datatable/getAllEmpTimingRecords', 'ProjectBaController@getAllEmpTimingRecords');
    Route::post('datatable/getAllUserLeaves', 'UserLeaveController@getAllUserLeaves');
    Route::post('datatable/getAllProjectsDatatableBaTl', 'CompanyProjectController@getAllProjectsDatatableBaTl');
    Route::post('datatable/getOwnProjectsDatatableBaTl', 'CompanyProjectController@getOwnProjectsDatatableBaTl');
    Route::post('datatable/getAllProjectsDatatableBa', 'CompanyProjectBaController@getAllProjectsDatatableBa');
    Route::post('datatable/getAllClosedProjectsDatatableBaTl', 'CompanyProjectController@getAllClosedProjectsDatatableBaTl');
    Route::post('datatable/getAllClosedProjectsDatatableBa', 'CompanyProjectBaController@getAllClosedProjectsDatatableBa');
    Route::post('datatable/getAllCompanyPolicyDatatable', 'CompanyPolicyController@getAllCompanyPolicyDatatable');
    Route::post('datatable/getAllCompanyPolicySelfDatatable', 'CompanyPolicyController@getAllCompanyPolicySelfDatatable');
    Route::post('datatable/getAllEmailTemplateDataTable', 'EmailTemplatesController@getAllEmailTemplateDataTable');
    Route::post('datatable/getAllGeneratedEmailsDatatable', 'EmailGenerateController@getAllGeneratedEmailsDatatable');

    // email generate routes
    Route::post('generate/saveGeneratedEmail', 'EmailGenerateController@saveGeneratedEmail');
    Route::post('generate/getGeneratedEmailById', 'EmailGenerateController@getGeneratedEmailById');

    //company policy route
    Route::post('company_policy/savePolicy', 'CompanyPolicyController@savePolicy');
    Route::post('company_policy/companyPolicyById', 'CompanyPolicyController@companyPolicyById');
    Route::post('company_policy/deletePolicy', 'CompanyPolicyController@deletePolicy');


    // for notification

    Route::post('sendNotifications', 'NotificationController@sendNotifications');



    Route::post('/roles/{role_id}/grant/{permission_id}', 'RoleController@grantPermission');
    //    Route::post('saveEmailData', 'EmailController@saveemaildata');
    Route::post('/roles/{role_id}/revoke/{permission_id}', 'RoleController@revokePermission');
    //    Route::post('/createmodule', 'FileCreatedController@NewFileCreate');
    //    Route::post('testEmailSetting', 'EmailController@testEmailSetting');
    //    Route::get('/getEmailData{id}', 'EmailController@getEmailData');
    Route::get('/roles', 'RoleController@getAllRoles');
    Route::get('/roles/{role_id}', 'RoleController@getRolePermission');

    //    Route::get('getLogs', 'LogController@getLogs');
    //    Route::post('updateLogs', 'LogController@updateLogs');

    //company projects
    Route::post('company_project/save_project', 'CompanyProjectController@saveProject');
    Route::post('company_project/save_project_ba', 'CompanyProjectController@saveProjectBa');
    Route::post('company_project/getProjectById', 'CompanyProjectController@getProjectByID');
    Route::post('company_project/getBaProjectById', 'CompanyProjectController@getBaProjectById');
    Route::post('company_project/getAllEmpTimingRecords', 'CompanyProjectController@getAllEmpTimingRecords');
    Route::post('company_project/getProjectDataBaTl', 'CompanyProjectController@getProjectDataBaTl');
    Route::post('company_project/doComment', 'CompanyProjectCommentsController@doComment'); // startProject
    Route::post('company_project/projectAction', 'CompanyProjectController@projectAction');
    Route::post('company_project/checkProjectNameExist', 'CompanyProjectController@checkProjectNameExist');
    Route::post('company_project/getReportByEmp', 'CompanyProjectController@getReportByEmp');
    Route::get('company_project/getAllProjectsList', 'CompanyProjectController@getAllProjectsList');
    Route::post('company_project/getTotalHoursByProject', 'CompanyProjectController@getTotalHoursByProject');

    // activity box
    Route::get('activity_box/getEmpNotifications', 'ActivityBoxController@getEmpNotifications');
    Route::get('activity_box/readAllNotificationsEmp', 'ActivityBoxController@readAllNotificationsEmp');
    Route::post('activity_box/deleteNotification', 'ActivityBoxController@deleteNotification');
});


Route::get('backupnow', 'UserController@backupnow');
Route::get('downloadimage/{file}', 'UploadController@downloadimages');
Route::get('downloadfile/{file}', 'UploadController@downloadfile');
//upload a files
Route::post('upload/uploadImages', 'UploadController@uploadImages');
Route::post('upload/uploadFile', 'UploadController@uploadFile');
Route::post('upload/multipleFileUpload', 'UploadController@multipleFileUpload');


// send test email
Route::get('sendTestMail', 'EmailController@sendTestMail');
// end

<?php

namespace App\Http\Controllers;

use App\Repositories\ActivityBoxRepository;
use App\Repositories\AppreciationRepository;
use App\Repositories\ApprovedProjectCommentsRepository;
use App\Repositories\ApprovedProjectConvRepository;
use App\Repositories\ApprovedProjectEmpTimingsRepository;
use App\Repositories\ApprovedProjectFlagRepository;
use App\Repositories\ApprovedProjectRepository;
use App\Repositories\AwardRepository;
use App\Repositories\BreaksRepository;
use App\Repositories\CalenderRepository;
use App\Repositories\CandidatesInfoRepository;
use App\Repositories\CheckInRepository;
use App\Repositories\CommentsRepository;
use App\Repositories\CompanyPolicyRepository;
use App\Repositories\CompanyProjectBaRepository;
use App\Repositories\CompanyProjectEmpTiminigsRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\ContactRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\DesignationRepository;

//use App\Repositories\EmailSettingRepository;
//use App\Repositories\EmployeeRepository;
use App\Repositories\EmailGenerateRepository;
use App\Repositories\EmailSettingRepository;
use App\Repositories\EmailTemplateRepository;
use App\Repositories\EmployeeTimingsRepository;
use App\Repositories\ExpenseCategoryRepository;
use App\Repositories\ExpenseRepository;
use App\Repositories\HolidayRepository;
use App\Repositories\HolidayEmployeeRepository;
use App\Repositories\JobApplicationsRepository;
use App\Repositories\JobInterviewRepository;
use App\Repositories\JobStageRepository;
use App\Repositories\LeaveApplicationRepository;
use App\Repositories\LeaveApprovedRepository;
use App\Repositories\LeaveTypeRepository;
use App\Repositories\LocationRepository;

//use App\Repositories\LogActivityRepository;
//use App\Repositories\LogDetailRepository;
use App\Repositories\NewsEmployeeRepository;
use App\Repositories\NewsRepository;
use App\Repositories\OptionRepository;
//use App\Repositories\PayrollRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\ProjectBaRepository;
use App\Repositories\ProjectCommentsRepository;
use App\Repositories\ProjectConversationRepository;
use App\Repositories\ProjectJrBaRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\RoleRepository;
use App\Repositories\TeamEmployeeRepository;
use App\Repositories\TeamRepository;
use App\Repositories\UserAccountRepository;
use App\Repositories\UserLeaveRepository;
//use App\Repositories\UserPayrollRepository;
use App\Repositories\UserQualificationRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserworkexperienceRepository;
use App\Repositories\JobOpeningsRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Repositories\CompanyProjectRepository;
use App\Repositories\CompanyProjectConvRepository;
use App\Entities\CompanyProjectComments;
use App\Repositories\CompanyProjectCommentsRepository;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $userRepo;
    protected $roleRepo;
    protected $permissionRepo;
    protected $optionRepo;
    protected $companyRepo;
    protected $departmentRepo;
    protected $designationRepo;
    protected $locationRepo;
    protected $expense_categoryRepo;
    protected $awardRepo;
    protected $calenderRepo;
    protected $leavetypeRepo;
    protected $userworkRepo;
    protected $useraccountRepo;
    protected $userqualificationRepo;
    protected $leaveapplicationRepo;
    protected $contactRepo;
    protected $appreciationRepo;
    protected $newsRepo;
    protected $newsempRepo;
    protected $expenseRepo;
    protected $jobOpeningRepo;
    protected $jobApplicationsRepo;
    protected $jobStageRepo;
    protected $jobInterviewRepo;
    protected $holidayRepo;
    protected $holidayEmployeeRepo;
    protected $userLeaveRepo;
    protected $leaveApprovedRepo;
    protected $checkInRepo;
    protected $commentsRepo;
    protected $breaksRepo;
    protected $emailRepo;
    protected $emailTemplatesRepo;
    protected $teamRepo;
    protected $teamEmpRepo;
    protected $projectRepo;
    protected $projectBaRepo;
    protected $projectCommentsRepo;
    protected $projectConversationRepo;
    protected $projectJrBaRepo;
    protected $approvedProjectRepo;
    protected $approvedProjectFlagRepo;
    protected $approvedProjectConvRepo;
    protected $approvedProjectCommentsRepo;
    protected $approvedProjectEmpTimingRepo;
    protected $companyProjectRepo;
    protected $companyProjectBaRepo;
    protected $companyProjectEmpTimingsRepo;
    protected $companyProjectConvRepo;
    protected $companyProjectCommentRepo;
    protected $activityBoxRepo;
    protected $companyPolicyRepo;
    protected $emailGenerateRepo;
    protected $employeeTimingsRepo;
    protected $candidatesInfoRepo;

    public function __construct(
        UserRepository $userRepository,
        RoleRepository $roleRepository,
        PermissionRepository $permissionRepository,
        OptionRepository $optionRepository,
        CompanyRepository $companyRepository,
        DepartmentRepository $departmentRepository,
        DesignationRepository $designationRepository,
        LocationRepository $locationRepository,
        ExpenseCategoryRepository $expenseCategoryRepository,
        AwardRepository $awardRepository,
        CalenderRepository $calenderRepository,
        LeaveTypeRepository $leaveTypeRepository,
        UserAccountRepository $userAccountRepository,
        UserQualificationRepository $userQualificationRepository,
        UserworkexperienceRepository $userworkexperienceRepository,
        LeaveApplicationRepository $leaveApplicationRepository,
        ContactRepository $contactRepository,
        AppreciationRepository $appreciationRepository,
        NewsRepository $newsRepository,
        NewsEmployeeRepository $newsEmployeeRepository,
        ExpenseRepository $expenseRepository,
        JobOpeningsRepository $jobOpeningsRepository,
        JobApplicationsRepository $jobApplicationsRepository,
        JobStageRepository $jobStageRepository,
        JobInterviewRepository $jobInterviewRepository,
        HolidayRepository $holidayRepository,
        HolidayEmployeeRepository $holidayEmployeeRepository,
        UserLeaveRepository $userLeaveRepository,
        LeaveApprovedRepository $leaveApprovedRepository,
        CheckInRepository $checkInRepository,
        CommentsRepository $commentsRepository,
        BreaksRepository $breaksRepository,
        EmailSettingRepository $emailSettingRepository,
        EmailTemplateRepository $emailTemplatesRepository,
        TeamRepository $teamRepository,
        TeamEmployeeRepository $teamEmployeeRepository,
        ProjectRepository $projectRepository,
        ProjectBaRepository $projectBaRepository,
        ProjectCommentsRepository $projectCommentsRepository,
        ProjectConversationRepository $projectConversationRepository,
        ProjectJrBaRepository $projectJrBaRepository,
        ApprovedProjectRepository $approvedProjectRepository,
        ApprovedProjectFlagRepository $approvedProjectFlagRepository,
        ApprovedProjectConvRepository $approvedProjectConvRepository,
        ApprovedProjectCommentsRepository $approvedProjectCommentsRepository,
        ApprovedProjectEmpTimingsRepository $approvedProjectEmpTimingRepository,
        CompanyProjectRepository $companyProjectRepository,
        CompanyProjectBaRepository $companyProjectBaRepository,
        CompanyProjectEmpTiminigsRepository $companyProjectEmpTimingsRepository,
        CompanyProjectConvRepository $companyProjectConvRepo,
        CompanyProjectCommentsRepository $companyProjectCommentRepo,
        ActivityBoxRepository $activityBoxRepo,
        CompanyPolicyRepository $companyPolicyRepo,
        EmailGenerateRepository $emailGenerateRepo,
        EmployeeTimingsRepository $employeeTimingsRepo,
        CandidatesInfoRepository $candidatesInfoRepo
    ) {
        $this->userRepo = $userRepository;
        $this->roleRepo = $roleRepository;
        $this->permissionRepo = $permissionRepository;
        $this->optionRepo = $optionRepository;
        $this->companyRepo = $companyRepository;
        $this->departmentRepo = $departmentRepository;
        $this->designationRepo = $designationRepository;
        $this->locationRepo = $locationRepository;
        $this->expense_categoryRepo = $expenseCategoryRepository;
        $this->awardRepo = $awardRepository;
        $this->calenderRepo = $calenderRepository;
        $this->leavetypeRepo = $leaveTypeRepository;
        $this->userworkRepo = $userworkexperienceRepository;
        $this->useraccountRepo = $userAccountRepository;
        $this->userqualificationRepo = $userQualificationRepository;
        $this->leaveapplicationRepo = $leaveApplicationRepository;
        $this->contactRepo = $contactRepository;
        $this->appreciationRepo = $appreciationRepository;
        $this->newsRepo = $newsRepository;
        $this->newsempRepo = $newsEmployeeRepository;
        $this->expenseRepo = $expenseRepository;
        $this->jobOpeningRepo = $jobOpeningsRepository;
        $this->jobApplicationsRepo = $jobApplicationsRepository;
        $this->jobStageRepo = $jobStageRepository;
        $this->jobInterviewRepo = $jobInterviewRepository;
        $this->holidayRepo = $holidayRepository;
        $this->holidayEmployeeRepo = $holidayEmployeeRepository;
        $this->userLeaveRepo = $userLeaveRepository;
        $this->leaveApprovedRepo = $leaveApprovedRepository;
        $this->checkInRepo = $checkInRepository;
        $this->commentsRepo = $commentsRepository;
        $this->breaksRepo = $breaksRepository;
        $this->emailRepo = $emailSettingRepository;
        $this->emailTemplatesRepo = $emailTemplatesRepository;
        $this->teamRepo = $teamRepository;
        $this->teamEmpRepo = $teamEmployeeRepository;
        $this->projectRepo = $projectRepository;
        $this->projectBaRepo = $projectBaRepository;
        $this->projectCommentsRepo = $projectCommentsRepository;
        $this->projectConversationRepo = $projectConversationRepository;
        $this->projectJrBaRepo = $projectJrBaRepository;
        $this->approvedProjectRepo = $approvedProjectRepository;
        $this->approvedProjectFlagRepo = $approvedProjectFlagRepository;
        $this->approvedProjectConvRepo = $approvedProjectConvRepository;
        $this->approvedProjectCommentsRepo = $approvedProjectCommentsRepository;
        $this->approvedProjectEmpTimingRepo = $approvedProjectEmpTimingRepository;
        $this->companyProjectRepo = $companyProjectRepository;
        $this->companyProjectBaRepo = $companyProjectBaRepository;
        $this->companyProjectEmpTimingsRepo = $companyProjectEmpTimingsRepository;
        $this->companyProjectConvRepo = $companyProjectConvRepo;
        $this->companyProjectCommentRepo = $companyProjectCommentRepo;
        $this->activityBoxRepo = $activityBoxRepo;
        $this->companyPolicyRepo = $companyPolicyRepo;
        $this->emailGenerateRepo = $emailGenerateRepo;
        $this->employeeTimingsRepo = $employeeTimingsRepo;
        $this->candidatesInfoRepo = $candidatesInfoRepo;
    }

    function jsonResponse($data)
    {
        $response['response'] = 'success';
        $response['data'] = $data;
        return response()->json($response, 200);
    }

    function errorResponse($data)
    {
        $response['response'] = 'error';
        $response['data'] = $data;
        return response()->json($response, 200);
    }

    function notFoundResponse($data)
    {
        $response['response'] = 'not found';
        $response['data'] = $data;
        return response()->json($response, 404);
    }

    function accessDeniedResponse()
    {
        $response['response'] = 'access denied';
        $response['data'] = ['message' => 'you don\'t have permission to access this resource'];
        return response()->json($response, 200);
    }

    // function hasPermission(array){
    // if(Auth::user()->){
    //     abort('401')
    // }
    // }
}

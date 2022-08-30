<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use LaravelDoctrine\ACL\Contracts\HasRoles as HasRolesContracts;
use LaravelDoctrine\ACL\Mappings as ACL;
use LaravelDoctrine\ACL\Roles\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Gedmo\Mapping\Annotation as Gedmo;
use LaravelDoctrine\ACL\Contracts\HasPermissions;
use LaravelDoctrine\ACL\Permissions\HasPermissions as HasPermissionContracts;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class User implements Authenticatable, JWTSubject, HasRolesContracts, HasPermissions
{
    use \LaravelDoctrine\ORM\Auth\Authenticatable;
    use HasRoles;
    use HasPermissionContracts;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $firstname;

    /**
     * @ORM\Column(type="string")
     */
    protected $lastname;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $employee_id;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $about;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $company_name;

    /**
     * @ORM\Column(type="string")
     */
    protected $email;

    /**
     * @ORM\Column(type="string")
     */
    protected $company_email;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Option_master",inversedBy="User")
     * @ORM\JOinColumn(name="status",referencedColumnName="id")
     */
    protected $status;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Option_master",inversedBy="User")
     * @ORM\JOinColumn(name="gender",referencedColumnName="id")
     */
    protected $gender;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User",inversedBy="User")
     * @ORM\JOinColumn(name="report_to",referencedColumnName="id")
     */
    protected $report_to;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Department",inversedBy="User")
     * @ORM\JOinColumn(name="department",referencedColumnName="id")
     */
    protected $department;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Designation",inversedBy="User")
     * @ORM\JOinColumn(name="designation",referencedColumnName="id")
     */
    protected $designation;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Location",inversedBy="User")
     * @ORM\JOinColumn(name="location",referencedColumnName="id")
     */
    protected $location;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $address;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $per_address;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $allowed_login;

    /**
     * @ORM\Column(type="boolean",nullable=true)
     */
    protected $on_training;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $is_manager;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $marital_status;

    /**
     * @ORM\Column(type="date",nullable=true)
     */
    protected $probation_end_date;

    /**
     * @ORM\Column(type="date",nullable=true)
     */
    protected $joining_date;

    /**
     * @ORM\Column(type="date",nullable=true)
     */
    protected $increment_date;

    /**
     * @ORM\Column(type="date",nullable=true)
     */
    protected $exit_date;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $user_exit_status;

    /**
     * @ORM\Column(type="date",nullable=true)
     */
    protected $date_of_birth;

    /**
     * @ORM\Column(type="date",nullable=true)
     */
    protected $marriage_anniversary_date;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $contact_no;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $emergency_contact_no;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $emergency_contact_person;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $driving_license_number;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $pan_number;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $aadhar_number;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $voter_id_number;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $uan_number;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $pf_number;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $esic_number;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $driving_license_image;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $pan_id_image;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $aadhar_id_image;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $voter_id_image;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $lc;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $marksheet;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $offer_later_file;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $joining_letter_file;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $contract_file;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $resume_file;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $appointment_letter;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $increment_letter;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $promotion_letter;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $relieving_letter;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $exp_letter;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $appreciation_letter;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $document_returns_letter;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $no_due_certificate;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $acknowledgement_letter;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $warning_letter;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Option_master",inversedBy="User")
     * @ORM\JOinColumn(name="primary_account",referencedColumnName="id")
     */
    protected $primary_account;

    /**
     * @ORM\Column(type="string",nullable=true)
     *
     */
    protected $passport_number;

    /**
     * @ORM\Column(type="date",nullable=true)
     *
     */
    protected $passport_issue_date;

    /**
     * @ORM\Column(type="date",nullable=true)
     *
     */
    protected $passport_expiry_date;

    /**
     * @ORM\Column(type="text",nullable=true)
     *
     */
    protected $passport_image;

    /**
     * @ORM\Column(type="text",nullable=true)
     *
     */
    protected $visa_number;

    /**
     * @ORM\Column(type="date",nullable=true)
     *
     */
    protected $visa_issue_date;

    /**
     * @ORM\Column(type="date",nullable=true)
     *
     */
    protected $visa_expiry_date;

    /**
     * @ORM\Column(type="text",nullable=true)
     *
     */
    protected $visa_image;

    /**
     * @ORM\Column(type="string",nullable=true)
     *
     */
    protected $slack_username;

    /**
     * @ORM\Column(type="string",nullable=true)
     *
     */
    protected $linkdin_username;

    /**
     * @ORM\Column(type="string",nullable=true)
     *
     */
    protected $facebook_username;
    /**
     * @ORM\Column(type="string",nullable=true)
     *
     */
    protected $profile_image;

    /**
     * @ORM\Column(type="string",nullable=true)
     *
     */
    protected $twitter_username;

    /**
     * @ORM\Column(type="text",nullable=true)
     *
     */
    protected $certifications_courses;
    /**
     * @ORM\Column(type="text",nullable=true)
     *
     */
    protected $other_work_experirnce;

    /**
     * @ACL\HasRoles()
     * @var \Doctrine\Common\Collections\ArrayCollection|\LaravelDoctrine\ACL\Contracts\Role[]
     */
    protected $roles;

    /** @ORM\OneToMany(targetEntity="User_work_experiance", mappedBy="user") */
    protected $user_work;

    /** @ORM\OneToMany(targetEntity="User_qualification", mappedBy="user") */
    protected $user_qualification;

    /** @ORM\OneToMany(targetEntity="User_account", mappedBy="user") */
    protected $user_account;

    /**
     * @ORM\OneToMany(targetEntity="CheckIn", mappedBy="emp_id")
     */
    protected $user_check_in;

    /**
     * @ORM\OneToMany(targetEntity="UserLeave", mappedBy="emp_id")
     */
    protected $user_leaves;

    /**
     * @ORM\OneToMany(targetEntity="LeaveApproved", mappedBy="user_id")
     */
    protected $approved_leave;


    /**
     * @var \DateTime $created
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;

    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    protected $deletedAt;

    public function __construct($data)
    {
        if (!$data)
            return;

        $this->profile_image = isset($data['profile_image']) ? $data['profile_image'] : '';
        $this->firstname = isset($data['firstname']) ? $data['firstname'] : '';
        $this->lastname = isset($data['lastname']) ? $data['lastname'] : '';
        $this->status = isset($data['status']) ? $data['status'] : null;
        $this->email = isset($data['email']) ? $data['email'] : '';
        $this->company_email = isset($data['company_email']) ? $data['company_email'] : '';
        $this->about = isset($data['about']) ? $data['about'] : '';
        $this->company_name = isset($data['company_name']) ? $data['company_name'] : '';
        $this->password = isset($data['password']) ? Hash::make($data['password']) : '';
        $this->gender = isset($data['gender']) ? $data['gender'] : null;
        $this->department = isset($data['department']) ? $data['department'] : null;
        $this->designation = isset($data['designation']) ? $data['designation'] : null;
        $this->location = isset($data['location']) ? $data['location'] : null;
        $this->employee_id = isset($data['employee_id']) ? $data['employee_id'] : '';
        $this->address = isset($data['address']) ? $data['address'] : '';
        $this->per_address = isset($data['per_address']) ? $data['per_address'] : '';
        $this->allowed_login = isset($data['allowed_login']) ? $data['allowed_login'] : 0;
        $this->on_training = isset($data['on_training']) ? $data['on_training'] : null;
        $this->is_manager = isset($data['is_manager']) ? $data['is_manager'] : 0;
        $this->marital_status = isset($data['marital_status']) ? $data['marital_status'] : 0;
        $this->marriage_anniversary_date = isset($data['marriage_anniversary_date']) ? $data['marriage_anniversary_date'] : null;
        $this->probation_end_date = isset($data['probation_end_date']) ? $data['probation_end_date'] : null;
        $this->joining_date = isset($data['joining_date']) ? $data['joining_date'] : null;
        $this->increment_date = isset($data['increment_date']) ? $data['increment_date'] : null;
        $this->user_exit_status = isset($data['user_exit_status']) ? $data['user_exit_status'] : 1;
        $this->exit_date = isset($data['exit_date']) ? $data['exit_date'] : null;
        $this->date_of_birth = isset($data['date_of_birth']) ? $data['date_of_birth'] : null;
        $this->contact_no = isset($data['contact_no']) ? $data['contact_no'] : '';
        $this->emergency_contact_no = isset($data['emergency_contact_no']) ? $data['emergency_contact_no'] : '';
        $this->emergency_contact_person = isset($data['emergency_contact_person']) ? $data['emergency_contact_person'] : '';
        $this->driving_license_number = isset($data['driving_license_number']) ? $data['driving_license_number'] : '';
        $this->pan_number = isset($data['pan_number']) ? $data['pan_number'] : '';
        $this->aadhar_number = isset($data['aadhar_number']) ? $data['aadhar_number'] : '';
        $this->voter_id_number = isset($data['voter_id_number']) ? $data['voter_id_number'] : '';
        $this->uan_number = isset($data['uan_number']) ? $data['uan_number'] : '';
        $this->pf_number = isset($data['pf_number']) ? $data['pf_number'] : '';
        $this->esic_number = isset($data['esic_number']) ? $data['esic_number'] : '';
        $this->driving_license_image = isset($data['driving_license_image']) ? $data['driving_license_image'] : '';
        $this->pan_id_image = isset($data['pan_id_image']) ? $data['pan_id_image'] : '';
        $this->aadhar_id_image = isset($data['aadhar_id_image']) ? $data['aadhar_id_image'] : '';
        $this->voter_id_image = isset($data['voter_id_image']) ? $data['voter_id_image'] : '';
        $this->lc = isset($data['lc']) ? $data['lc'] : '';
        $this->marksheet = isset($data['marksheet']) ? $data['marksheet'] : '';


        $this->offer_later_file = isset($data['offer_later_file']) ? $data['offer_later_file'] : '';
        $this->joining_letter_file = isset($data['joining_letter_file']) ? $data['joining_letter_file'] : '';
        $this->contract_file = isset($data['contract_file']) ? $data['contract_file'] : '';
        $this->resume_file = isset($data['resume_file']) ? $data['resume_file'] : '';
        $this->appointment_letter = isset($data['appointment_letter']) ? $data['appointment_letter'] : '';
        $this->increment_letter = isset($data['increment_letter']) ? $data['increment_letter'] : '';
        $this->promotion_letter = isset($data['promotion_letter']) ? $data['promotion_letter'] : '';
        $this->relieving_letter = isset($data['relieving_letter']) ? $data['relieving_letter'] : '';
        $this->exp_letter = isset($data['exp_letter']) ? $data['exp_letter'] : '';
        $this->appreciation_letter = isset($data['appreciation_letter']) ? $data['appreciation_letter'] : '';
        $this->document_returns_letter = isset($data['document_returns_letter']) ? $data['document_returns_letter'] : '';
        $this->no_due_certificate = isset($data['no_due_certificate']) ? $data['no_due_certificate'] : '';
        $this->acknowledgement_letter = isset($data['acknowledgement_letter']) ? $data['acknowledgement_letter'] : '';
        $this->warning_letter = isset($data['warning_letter']) ? $data['warning_letter'] : '';



        $this->primary_account = isset($data['primary_account']) ? $data['primary_account'] : null;
        $this->passport_number = isset($data['passport_number']) ? $data['passport_number'] : '';
        $this->passport_issue_date = isset($data['passport_issue_date']) ? $data['passport_issue_date'] : null;
        $this->passport_expiry_date = isset($data['passport_expiry_date']) ? $data['passport_expiry_date'] : null;
        $this->passport_image = isset($data['passport_image']) ? $data['passport_image'] : '';
        $this->visa_number = isset($data['visa_number']) ? $data['visa_number'] : '';
        $this->visa_issue_date = isset($data['visa_issue_date']) ? $data['visa_issue_date'] : null;
        $this->visa_expiry_date = isset($data['visa_expiry_date']) ? $data['visa_expiry_date'] : null;
        $this->visa_image = isset($data['visa_image']) ? $data['visa_image'] : '';
        $this->slack_username = isset($data['slack_username']) ? $data['slack_username'] : '';
        $this->linkdin_username = isset($data['linkdin_username']) ? $data['linkdin_username'] : '';
        $this->facebook_username = isset($data['facebook_username']) ? $data['facebook_username'] : '';
        $this->twitter_username = isset($data['twitter_username']) ? $data['twitter_username'] : '';
        $this->certifications_courses = isset($data['certifications_courses']) ? $data['certifications_courses'] : '';
        $this->report_to = isset($data['report_to']) ? $data['report_to'] : null;
        $this->roles = isset($data['roles']) ? $data['roles'] : '';
    }


    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCompanyEmail()
    {
        return $this->company_email;
    }

    /**
     * @param mixed $company_email
     */
    public function setCompanyEmail($company_email): void
    {
        $this->company_email = $company_email;
    }

    /**
     * @return mixed
     */
    public function getPerAddress()
    {
        return $this->per_address;
    }

    /**
     * @param mixed $per_address
     */
    public function setPerAddress($per_address): void
    {
        $this->per_address = $per_address;
    }


    public function getLastname()
    {
        return $this->lastname;
    }


    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    public function getAbout()
    {
        return $this->about;
    }


    public function setAbout($about)
    {
        $this->about = $about;
    }


    public function getCompanyName()
    {
        return $this->company_name;
    }


    public function setCompanyName($company_name)
    {
        $this->company_name = $company_name;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }


    public function getGender()
    {
        return $this->gender;
    }


    public function setGender($gender)
    {
        $this->gender = $gender;
    }


    public function getReportTo()
    {
        return $this->report_to;
    }


    public function setReportTo($report_to)
    {
        $this->report_to = $report_to;
    }


    public function getDepartment()
    {
        return $this->department;
    }


    public function setDepartment($department)
    {
        $this->department = $department;
    }

    public function getDesignation()
    {
        return $this->designation;
    }

    public function setDesignation($designation)
    {
        $this->designation = $designation;
    }


    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation($location)
    {
        $this->location = $location;
    }


    public function getAddress()
    {
        return $this->address;
    }


    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getOnTraining()
    {
        return $this->on_training;
    }

    /**
     * @param mixed $on_training
     */
    public function setOnTraining($on_training): void
    {
        $this->on_training = $on_training;
    }


    public function getAllowedLogin()
    {
        return $this->allowed_login;
    }


    public function setAllowedLogin($allowed_login)
    {
        $this->allowed_login = $allowed_login;
    }


    public function getisManager()
    {
        return $this->is_manager;
    }

    public function setIsManager($is_manager)
    {
        $this->is_manager = $is_manager;
    }

    public function getMaritalStatus()
    {
        return $this->marital_status;
    }


    public function setMaritalStatus($marital_status)
    {
        $this->marital_status = $marital_status;
    }


    public function getProbationEndDate()
    {
        return $this->probation_end_date;
    }


    public function setProbationEndDate($probation_end_date)
    {
        $this->probation_end_date = $probation_end_date;
    }


    public function getJoiningDate()
    {
        return $this->joining_date;
    }


    public function setJoiningDate($joining_date)
    {
        $this->joining_date = $joining_date;
    }

    /**
     * @return mixed
     */
    public function getIncrementDate()
    {
        return $this->increment_date;
    }

    /**
     * @param mixed $increment_date
     */
    public function setIncrementDate($increment_date): void
    {
        $this->increment_date = $increment_date;
    }




    public function getExitDate()
    {
        return $this->exit_date;
    }


    public function setExitDate($exit_date)
    {
        $this->exit_date = $exit_date;
    }


    public function getUserExitStatus()
    {
        return $this->user_exit_status;
    }


    public function setUserExitStatus($user_exit_status)
    {
        $this->user_exit_status = $user_exit_status;
    }


    public function getDateOfBirth()
    {
        return $this->date_of_birth;
    }


    public function setDateOfBirth($date_of_birth)
    {
        $this->date_of_birth = $date_of_birth;
    }


    public function getMarriageAnniversaryDate()
    {
        return $this->marriage_anniversary_date;
    }


    public function setMarriageAnniversaryDate($marriage_anniversary_date)
    {
        $this->marriage_anniversary_date = $marriage_anniversary_date;
    }


    public function getContactNo()
    {
        return $this->contact_no;
    }


    public function setContactNo($contact_no)
    {
        $this->contact_no = $contact_no;
    }


    public function getEmergencyContactNo()
    {
        return $this->emergency_contact_no;
    }

    public function setEmergencyContactNo($emergency_contact_no)
    {
        $this->emergency_contact_no = $emergency_contact_no;
    }


    public function getEmergencyContactPerson()
    {
        return $this->emergency_contact_person;
    }

    public function setEmergencyContactPerson($emergency_contact_person)
    {
        $this->emergency_contact_person = $emergency_contact_person;
    }

    public function getDrivingLicenseNumber()
    {
        return $this->driving_license_number;
    }


    public function setDrivingLicenseNumber($driving_license_number)
    {
        $this->driving_license_number = $driving_license_number;
    }


    public function getPanNumber()
    {
        return $this->pan_number;
    }


    public function setPanNumber($pan_number)
    {
        $this->pan_number = $pan_number;
    }


    public function getAadharNumber()
    {
        return $this->aadhar_number;
    }


    public function setAadharNumber($aadhar_number)
    {
        $this->aadhar_number = $aadhar_number;
    }


    public function getVoterIdNumber()
    {
        return $this->voter_id_number;
    }


    public function setVoterIdNumber($voter_id_number)
    {
        $this->voter_id_number = $voter_id_number;
    }

    /**
     * @return mixed
     */
    public function getUanNumber()
    {
        return $this->uan_number;
    }

    /**
     * @param mixed $uan_number
     */
    public function setUanNumber($uan_number): void
    {
        $this->uan_number = $uan_number;
    }

    /**
     * @return mixed
     */
    public function getPfNumber()
    {
        return $this->pf_number;
    }

    /**
     * @param mixed $pf_number
     */
    public function setPfNumber($pf_number): void
    {
        $this->pf_number = $pf_number;
    }

    /**
     * @return mixed
     */
    public function getEsicNumber()
    {
        return $this->esic_number;
    }

    /**
     * @param mixed $esic_number
     */
    public function setEsicNumber($esic_number): void
    {
        $this->esic_number = $esic_number;
    }



    /**
     * @return mixed
     */
    public function getLc()
    {
        return $this->lc;
    }

    /**
     * @param mixed $lc
     */
    public function setLc($lc): void
    {
        $this->lc = $lc;
    }

    /**
     * @return mixed
     */
    public function getMarksheet()
    {
        return $this->marksheet;
    }

    /**
     * @param mixed $marksheet
     */
    public function setMarksheet($marksheet): void
    {
        $this->marksheet = $marksheet;
    }

    public function getDrivingLicenseImage()
    {
        return $this->driving_license_image;
    }


    public function setDrivingLicenseImage($driving_license_image)
    {
        $this->driving_license_image = $driving_license_image;
    }


    public function getPanIdImage()
    {
        return $this->pan_id_image;
    }


    public function setPanIdImage($pan_id_image)
    {
        $this->pan_id_image = $pan_id_image;
    }


    public function getAadharIdImage()
    {
        return $this->aadhar_id_image;
    }


    public function setAadharIdImage($aadhar_id_image)
    {
        $this->aadhar_id_image = $aadhar_id_image;
    }

    public function getVoterIdImage()
    {
        return $this->voter_id_image;
    }


    public function setVoterIdImage($voter_id_image)
    {
        $this->voter_id_image = $voter_id_image;
    }


    public function getOfferLaterFile()
    {
        return $this->offer_later_file;
    }


    public function setOfferLaterFile($offer_later_file)
    {
        $this->offer_later_file = $offer_later_file;
    }


    public function getJoiningLetterFile()
    {
        return $this->joining_letter_file;
    }


    public function setJoiningLetterFile($joining_letter_file)
    {
        $this->joining_letter_file = $joining_letter_file;
    }


    public function getContractFile()
    {
        return $this->contract_file;
    }


    public function setContractFile($contract_file)
    {
        $this->contract_file = $contract_file;
    }


    public function getResumeFile()
    {
        return $this->resume_file;
    }


    public function setResumeFile($resume_file)
    {
        $this->resume_file = $resume_file;
    }

    /**
     * @return mixed
     */
    public function getAppointmentLetter()
    {
        return $this->appointment_letter;
    }

    /**
     * @param mixed $appointment_letter
     */
    public function setAppointmentLetter($appointment_letter): void
    {
        $this->appointment_letter = $appointment_letter;
    }

    /**
     * @return mixed
     */
    public function getIncrementLetter()
    {
        return $this->increment_letter;
    }

    /**
     * @param mixed $increment_letter
     */
    public function setIncrementLetter($increment_letter): void
    {
        $this->increment_letter = $increment_letter;
    }

    /**
     * @return mixed
     */
    public function getPromotionLetter()
    {
        return $this->promotion_letter;
    }

    /**
     * @param mixed $promotion_letter
     */
    public function setPromotionLetter($promotion_letter): void
    {
        $this->promotion_letter = $promotion_letter;
    }

    /**
     * @return mixed
     */
    public function getRelievingLetter()
    {
        return $this->relieving_letter;
    }

    /**
     * @param mixed $relieving_letter
     */
    public function setRelievingLetter($relieving_letter): void
    {
        $this->relieving_letter = $relieving_letter;
    }

    /**
     * @return mixed
     */
    public function getExpLetter()
    {
        return $this->exp_letter;
    }

    /**
     * @param mixed $exp_letter
     */
    public function setExpLetter($exp_letter): void
    {
        $this->exp_letter = $exp_letter;
    }

    /**
     * @return mixed
     */
    public function getAppreciationLetter()
    {
        return $this->appreciation_letter;
    }

    /**
     * @param mixed $appreciation_letter
     */
    public function setAppreciationLetter($appreciation_letter): void
    {
        $this->appreciation_letter = $appreciation_letter;
    }

    /**
     * @return mixed
     */
    public function getDocumentReturnsLetter()
    {
        return $this->document_returns_letter;
    }

    /**
     * @param mixed $document_returns_letter
     */
    public function setDocumentReturnsLetter($document_returns_letter): void
    {
        $this->document_returns_letter = $document_returns_letter;
    }

    /**
     * @return mixed
     */
    public function getNoDueCertificate()
    {
        return $this->no_due_certificate;
    }

    /**
     * @param mixed $no_due_certificate
     */
    public function setNoDueCertificate($no_due_certificate): void
    {
        $this->no_due_certificate = $no_due_certificate;
    }

    /**
     * @return mixed
     */
    public function getAcknowledgementLetter()
    {
        return $this->acknowledgement_letter;
    }

    /**
     * @param mixed $acknowledgement_letter
     */
    public function setAcknowledgementLetter($acknowledgement_letter): void
    {
        $this->acknowledgement_letter = $acknowledgement_letter;
    }

    /**
     * @return mixed
     */
    public function getWarningLetter()
    {
        return $this->warning_letter;
    }

    /**
     * @param mixed $warning_letter
     */
    public function setWarningLetter($warning_letter): void
    {
        $this->warning_letter = $warning_letter;
    }

    public function getPrimaryAccount()
    {
        return $this->primary_account;
    }


    public function setPrimaryAccount($primary_account)
    {
        $this->primary_account = $primary_account;
    }


    public function getPassportNumber()
    {
        return $this->passport_number;
    }


    public function setPassportNumber($passport_number)
    {
        $this->passport_number = $passport_number;
    }


    public function getPassportIssueDate()
    {
        return $this->passport_issue_date;
    }


    public function setPassportIssueDate($passport_issue_date)
    {
        $this->passport_issue_date = $passport_issue_date;
    }


    public function getPassportExpiryDate()
    {
        return $this->passport_expiry_date;
    }


    public function setPassportExpiryDate($passport_expiry_date)
    {
        $this->passport_expiry_date = $passport_expiry_date;
    }


    public function getPassportImage()
    {
        return $this->passport_image;
    }


    public function setPassportImage($passport_image)
    {
        $this->passport_image = $passport_image;
    }


    public function getVisaNumber()
    {
        return $this->visa_number;
    }


    public function setVisaNumber($visa_number)
    {
        $this->visa_number = $visa_number;
    }


    public function getVisaIssueDate()
    {
        return $this->visa_issue_date;
    }


    public function setVisaIssueDate($visa_issue_date)
    {
        $this->visa_issue_date = $visa_issue_date;
    }


    public function getVisaExpiryDate()
    {
        return $this->visa_expiry_date;
    }


    public function setVisaExpiryDate($visa_expiry_date)
    {
        $this->visa_expiry_date = $visa_expiry_date;
    }


    public function getVisaImage()
    {
        return $this->visa_image;
    }


    public function setVisaImage($visa_image)
    {
        $this->visa_image = $visa_image;
    }


    public function getSlackUsername()
    {
        return $this->slack_username;
    }


    public function setSlackUsername($slack_username)
    {
        $this->slack_username = $slack_username;
    }


    public function getLinkdinUsername()
    {
        return $this->linkdin_username;
    }


    public function setLinkdinUsername($linkdin_username)
    {
        $this->linkdin_username = $linkdin_username;
    }


    public function getFacebookUsername()
    {
        return $this->facebook_username;
    }


    public function setFacebookUsername($facebook_username)
    {
        $this->facebook_username = $facebook_username;
    }


    public function getProfileImage()
    {
        return $this->profile_image;
    }


    public function setProfileImage($profile_image)
    {
        $this->profile_image = $profile_image;
    }


    public function getTwitterUsername()
    {
        return $this->twitter_username;
    }


    public function setTwitterUsername($twitter_username)
    {
        $this->twitter_username = $twitter_username;
    }


    public function getCertificationsCourses()
    {
        return $this->certifications_courses;
    }


    public function setCertificationsCourses($certifications_courses)
    {
        $this->certifications_courses = $certifications_courses;
    }


    public function getOtherWorkExperirnce()
    {
        return $this->other_work_experirnce;
    }

    public function setOtherWorkExperirnce($other_work_experirnce)
    {
        $this->other_work_experirnce = $other_work_experirnce;
    }


    public function getUserWork()
    {
        return $this->user_work;
    }


    public function setUserWork($user_work)
    {
        $this->user_work = $user_work;
    }


    public function getUserQualification()
    {
        return $this->user_qualification;
    }


    public function setUserQualification($user_qualification)
    {
        $this->user_qualification = $user_qualification;
    }


    public function getUserAccount()
    {
        return $this->user_account;
    }


    public function setUserAccount($user_account)
    {
        $this->user_account = $user_account;
    }

    public function setId($id)
    {
        $this->id = $id;
    }


    public function getFirstname()
    {
        return $this->firstname;
    }


    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }


    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getRoles()
    {
        return $this->roles->toArray();
    }

    public function setRoles($role)
    {
        $this->roles = $role;
    }


    public function getEmployeeId()
    {
        return $this->employee_id;
    }


    public function setEmployeeId($employee_id)
    {
        $this->employee_id = $employee_id;
    }


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getId();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /*
     * since Gate requires  LaravelDoctrine\ACL\Permissions\HasPermissions
     * we have implemented this methods
     */
    public function hasPermissionTo($name, $requireAll = false)
    {
        foreach ($this->roles as $role) {
            if ($role->hasPermissionTo($name, $requireAll)) {
                return true;
            }
        }
        return false;
    }

    public function getPermissions()
    {
        $permissions = [];
        foreach ($this->roles as $role) {
            foreach ($role->getPermissions() as $perm) {
                $permissions[] = $perm->getName();
            }
        }
        return $permissions;
    }
}

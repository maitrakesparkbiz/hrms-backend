<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="job_applications")
 */
class JobApplications
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="JobOpenings", inversedBy="JobApplications")
     * @ORM\JoinColumn(name="job_id",referencedColumnName="id")
     */
    protected $job_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $applicant_name;

    /**
     * @ORM\Column(type="string")
     */
    protected $location;

    /**
     * @ORM\Column(type="string")
     */
    protected $contact_email;

    /**
     * @ORM\Column(type="string")
     */
    protected $phone_number1;

    /**
     * @ORM\Column(type="string")
     */
    protected $phone_number2;

    /**
     * @ORM\Column(type="string")
     */
    protected $source;

    /**
     * @ORM\Column(type="string")
     */
    protected $current_company;

    /**
     * @ORM\Column(type="string")
     */
    protected $current_ctc;

    /**
     * @ORM\Column(type="string")
     */
    protected $expected_ctc;

    /**
     * @ORM\Column(type="string")
     */
    protected $degree;

    /**
     * @ORM\ManyToOne(targetEntity="JobStage", inversedBy="JobApplications")
     * @ORM\JoinColumn(name="stage",referencedColumnName="id")
     */
    protected $stage;

    /**
     * @ORM\Column(type="string")
     */
    protected $reject_reason;

    /**
     * @ORM\Column(type="string")
     */
    protected $resume;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="JobApplication")
     * @ORM\JoinColumn(name="assoc_emp_id",referencedColumnName="id")
     */
    protected $assoc_emp_id;

    /**
     * @ORM\OneToMany(targetEntity="JobInterview", mappedBy="applicant_id")
     */
    protected $job_int;

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
        $this->job_id = isset($data['job_id']) ? $data['job_id'] : NULL;
        $this->assoc_emp_id = isset($data['assoc_emp_id']) ? $data['assoc_emp_id'] : NULL;
        $this->applicant_name = isset($data['applicant_name']) ? $data['applicant_name'] : '';
        $this->location = isset($data['location']) ? $data['location'] : '';
        $this->contact_email = isset($data['contact_email']) ? $data['contact_email'] : '';
        $this->phone_number1 = isset($data['phone_number1']) ? $data['phone_number1'] : '';
        $this->phone_number2 = isset($data['phone_number2']) ? $data['phone_number2'] : '';
        $this->current_company = isset($data['current_company']) ? $data['current_company'] : '';
        $this->current_ctc = isset($data['current_ctc']) ? $data['current_ctc'] : '';
        $this->expected_ctc = isset($data['expected_ctc']) ? $data['expected_ctc'] : '';
        $this->degree = isset($data['degree']) ? $data['degree'] : '';
        $this->source = isset($data['source']) ? $data['source'] : '';
        $this->stage = isset($data['stage']) ? $data['stage'] : NULL;
        $this->resume = isset($data['resume']) ? $data['resume'] : '';
        $this->reject_reason = isset($data['reject_reason']) ? $data['reject_reason'] : '';
    }

    /**
     * @return mixed
     */
    public function getAssocEmpId()
    {
        return $this->assoc_emp_id;
    }

    /**
     * @param mixed $assoc_emp_id
     */
    public function setAssocEmpId($assoc_emp_id): void
    {
        $this->assoc_emp_id = $assoc_emp_id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $job_id
     */
    public function setJobId($job_id): void
    {
        $this->job_id = $job_id;
    }

    /**
     * @return mixed
     */
    public function getApplicantName()
    {
        return $this->applicant_name;
    }

    /**
     * @param mixed $applicant_name
     */
    public function setApplicantName($applicant_name): void
    {
        $this->applicant_name = $applicant_name;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location): void
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getContactEmail()
    {
        return $this->contact_email;
    }

    /**
     * @param mixed $contact_email
     */
    public function setContactEmail($contact_email): void
    {
        $this->contact_email = $contact_email;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber1()
    {
        return $this->phone_number1;
    }

    /**
     * @param mixed $phone_number1
     */
    public function setPhoneNumber1($phone_number1): void
    {
        $this->phone_number1 = $phone_number1;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber2()
    {
        return $this->phone_number2;
    }

    /**
     * @param mixed $phone_number2
     */
    public function setPhoneNumber2($phone_number2): void
    {
        $this->phone_number2 = $phone_number2;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     */
    public function setSource($source): void
    {
        $this->source = $source;
    }

    /**
     * @return mixed
     */
    public function getCurrentCompany()
    {
        return $this->current_company;
    }

    /**
     * @param mixed $current_company
     */
    public function setCurrentCompany($current_company): void
    {
        $this->current_company = $current_company;
    }

    /**
     * @return mixed
     */
    public function getCurrentCtc()
    {
        return $this->current_ctc;
    }

    /**
     * @param mixed $current_ctc
     */
    public function setCurrentCtc($current_ctc): void
    {
        $this->current_ctc = $current_ctc;
    }

    /**
     * @return mixed
     */
    public function getExpectedCtc()
    {
        return $this->expected_ctc;
    }

    /**
     * @param mixed $expected_ctc
     */
    public function setExpectedCtc($expected_ctc): void
    {
        $this->expected_ctc = $expected_ctc;
    }

    /**
     * @return mixed
     */
    public function getDegree()
    {
        return $this->degree;
    }

    /**
     * @param mixed $degree
     */
    public function setDegree($degree): void
    {
        $this->degree = $degree;
    }

    /**
     * @return mixed
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * @param mixed $stage
     */
    public function setStage($stage): void
    {
        $this->stage = $stage;
    }


    /**
     * @return mixed
     */
    public function getResume()
    {
        return $this->resume;
    }

    /**
     * @param mixed $resume
     */
    public function setResume($resume): void
    {
        $this->resume = $resume;
    }

    /**
     * @return mixed
     */
    public function getRejectReason()
    {
        return $this->reject_reason;
    }

    /**
     * @param mixed $reject_reason
     */
    public function setRejectReason($reject_reason): void
    {
        $this->reject_reason = $reject_reason;
    }



}
<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="job_interview")
 */

class JobInterview
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="JobApplications", inversedBy="JobInterview")
     * @ORM\JoinColumn(name="applicant_id",referencedColumnName="id")
     */
    protected $applicant_id;

    /**
     * @ORM\Column(type="date",nullable=true)
     */
    protected $interview_date;

    /**
     * @ORM\Column(type="time",nullable=true)
     */
    protected $interview_time;

    /**
     * @ORM\Column(type="string")
     */
    protected $status;

    /**
     * @ORM\Column(type="string")
     */
    protected $subject;

    /**
     * @var \DateTime $created
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @var \DateTime $updated
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
        $this->applicant_id = isset($data['applicant_id'])?$data['applicant_id']:NULL;
        $this->interview_date = isset($data['interview_date'])?$data['interview_date']:NULL;
        $this->interview_time = isset($data['interview_time'])?$data['interview_time']:NULL;
        $this->status = isset($data['status'])?$data['status']:'1';
        $this->subject = isset($data['subject'])?$data['subject']:'';
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $applicant_id
     */
    public function setApplicantId($applicant_id): void
    {
        $this->applicant_id = $applicant_id;
    }

    /**
     * @return mixed
     */
    public function getInterviewDate()
    {
        return $this->interview_date;
    }

    /**
     * @param mixed $interview_date
     */
    public function setInterviewDate($interview_date): void
    {
        $this->interview_date = $interview_date;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }


    /**
     * @return mixed
     */
    public function getInterviewTime()
    {
        return $this->interview_time;
    }

    /**
     * @param mixed $interview_time
     */
    public function setInterviewTime($interview_time): void
    {
        $this->interview_time = $interview_time;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject): void
    {
        $this->subject = $subject;
    }



}

?>
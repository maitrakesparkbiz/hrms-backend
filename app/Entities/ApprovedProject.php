<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="approved_project")
 */
class ApprovedProject
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="main_project_id", referencedColumnName="id")
     */
    protected $main_project_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $client_name;

    /**
     * @ORM\Column(type="string")
     */
    protected $project_name;

    /**
     * @ORM\Column(type="string")
     */
    protected $project_description;

    /**
     * @ORM\Column(type="string")
     */
    protected $client_email;

    /**
     * @ORM\Column(type="string")
     */
    protected $skype_contact;

    /**
     * @ORM\Column(type="string")
     */
    protected $project_doc;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    protected $created_by;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="assigned_ba", referencedColumnName="id")
     */
    protected $assigned_ba;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="assigned_jr_ba", referencedColumnName="id")
     */
    protected $assigned_jr_ba;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    protected $threshold_limit1;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    protected $threshold_limit2;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $deadline;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $est_time;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $approved_est_time;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $approved_extra_hours;

    /**
     * @ORM\Column(type="string")
     */
    protected $approved_extra_hours_reason;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $ba_project_hours;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $on_hold;

    /**
     * @ORM\Column(type="string")
     */
    protected $hold_comment;

    /**
     * @ORM\OneToMany(targetEntity="ApprovedProjectFlag", mappedBy="project_id")
     */
    protected $flags;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $is_started;

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
        $this->main_project_id = isset($data['main_project_id']) ? $data['main_project_id'] : null;
        $this->created_by = isset($data['created_by']) ? $data['created_by'] : null;
        $this->assigned_ba = isset($data['assigned_ba']) ? $data['assigned_ba'] : null;
        $this->assigned_jr_ba = isset($data['assigned_jr_ba']) ? $data['assigned_jr_ba'] : null;
        $this->client_name = isset($data['client_name']) ? $data['client_name'] : '';
        $this->project_name = isset($data['project_name']) ? $data['project_name'] : '';
        $this->project_description = isset($data['project_description']) ? $data['project_description'] : '';
        $this->project_doc = isset($data['project_doc']) ? $data['project_doc'] : '';
        $this->client_email = isset($data['client_email']) ? $data['client_email'] : '';
        $this->skype_contact = isset($data['skype_contact']) ? $data['skype_contact'] : '';
        $this->threshold_limit1 = isset($data['threshold_limit1']) ? $data['threshold_limit1'] : null;
        $this->threshold_limit2 = isset($data['threshold_limit2']) ? $data['threshold_limit2'] : null;
        $this->deadline = isset($data['deadline']) ? $data['deadline'] : null;
        $this->est_time = isset($data['est_time']) ? $data['est_time'] : null;
        $this->approved_est_time = isset($data['approved_est_time']) ? $data['approved_est_time'] : null;
        $this->approved_extra_hours = isset($data['approved_extra_hours']) ? $data['approved_extra_hours'] : null;
        $this->approved_extra_hours_reason = isset($data['approved_extra_hours_reason']) ? $data['approved_extra_hours_reason'] : '';
        $this->is_started = isset($data['is_started']) ? $data['is_started'] : 0;
        $this->ba_project_hours = isset($data['ba_project_hours']) ? $data['ba_project_hours'] : null;
        $this->on_hold = isset($data['on_hold']) ? $data['on_hold'] : 0;
        $this->hold_comment = isset($data['hold_comment']) ? $data['hold_comment'] : '';
    }


    /**
     * @return mixed
     */
    public function getMainProjectId()
    {
        return $this->main_project_id;
    }

    /**
     * @param mixed $main_project_id
     */
    public function setMainProjectId($main_project_id): void
    {
        $this->main_project_id = $main_project_id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getClientName()
    {
        return $this->client_name;
    }

    /**
     * @param mixed $client_name
     */
    public function setClientName($client_name): void
    {
        $this->client_name = $client_name;
    }

    /**
     * @return mixed
     */
    public function getProjectName()
    {
        return $this->project_name;
    }

    /**
     * @param mixed $project_name
     */
    public function setProjectName($project_name): void
    {
        $this->project_name = $project_name;
    }

    /**
     * @return mixed
     */
    public function getProjectDescription()
    {
        return $this->project_description;
    }

    /**
     * @param mixed $project_description
     */
    public function setProjectDescription($project_description): void
    {
        $this->project_description = $project_description;
    }

    /**
     * @return mixed
     */
    public function getClientEmail()
    {
        return $this->client_email;
    }

    /**
     * @param mixed $client_email
     */
    public function setClientEmail($client_email): void
    {
        $this->client_email = $client_email;
    }

    /**
     * @return mixed
     */
    public function getSkypeContact()
    {
        return $this->skype_contact;
    }

    /**
     * @param mixed $skype_contact
     */
    public function setSkypeContact($skype_contact): void
    {
        $this->skype_contact = $skype_contact;
    }

    /**
     * @return mixed
     */
    public function getProjectDoc()
    {
        return $this->project_doc;
    }

    /**
     * @param mixed $project_doc
     */
    public function setProjectDoc($project_doc): void
    {
        $this->project_doc = $project_doc;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * @param mixed $created_by
     */
    public function setCreatedBy($created_by): void
    {
        $this->created_by = $created_by;
    }

    /**
     * @return mixed
     */
    public function getAssignedBa()
    {
        return $this->assigned_ba;
    }

    /**
     * @param mixed $assigned_ba
     */
    public function setAssignedBa($assigned_ba): void
    {
        $this->assigned_ba = $assigned_ba;
    }

    /**
     * @return mixed
     */
    public function getAssignedJrBa()
    {
        return $this->assigned_jr_ba;
    }

    /**
     * @param mixed $assigned_jr_ba
     */
    public function setAssignedJrBa($assigned_jr_ba): void
    {
        $this->assigned_jr_ba = $assigned_jr_ba;
    }

    /**
     * @return mixed
     */
    public function getThresholdLimit1()
    {
        return $this->threshold_limit1;
    }

    /**
     * @param mixed $threshold_limit1
     */
    public function setThresholdLimit1($threshold_limit1): void
    {
        $this->threshold_limit1 = $threshold_limit1;
    }

    /**
     * @return mixed
     */
    public function getThresholdLimit2()
    {
        return $this->threshold_limit2;
    }

    /**
     * @param mixed $threshold_limit2
     */
    public function setThresholdLimit2($threshold_limit2): void
    {
        $this->threshold_limit2 = $threshold_limit2;
    }

    /**
     * @return mixed
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * @param mixed $deadline
     */
    public function setDeadline($deadline): void
    {
        $this->deadline = $deadline;
    }

    /**
     * @return mixed
     */
    public function getEstTime()
    {
        return $this->est_time;
    }

    /**
     * @param mixed $est_time
     */
    public function setEstTime($est_time): void
    {
        $this->est_time = $est_time;
    }

    /**
     * @return mixed
     */
    public function getApprovedEstTime()
    {
        return $this->approved_est_time;
    }

    /**
     * @param mixed $approved_est_time
     */
    public function setApprovedEstTime($approved_est_time): void
    {
        $this->approved_est_time = $approved_est_time;
    }


    /**
     * @return mixed
     */
    public function getisStarted()
    {
        return $this->is_started;
    }

    /**
     * @param mixed $is_started
     */
    public function setIsStarted($is_started): void
    {
        $this->is_started = $is_started;
    }

    /**
     * @return mixed
     */
    public function getApprovedExtraHours()
    {
        return $this->approved_extra_hours;
    }

    /**
     * @param mixed $approved_extra_hours
     */
    public function setApprovedExtraHours($approved_extra_hours): void
    {
        $this->approved_extra_hours = $approved_extra_hours;
    }

    /**
     * @return mixed
     */
    public function getApprovedExtraHoursReason()
    {
        return $this->approved_extra_hours_reason;
    }

    /**
     * @param mixed $approved_extra_hours_reason
     */
    public function setApprovedExtraHoursReason($approved_extra_hours_reason): void
    {
        $this->approved_extra_hours_reason = $approved_extra_hours_reason;
    }

    /**
     * @return mixed
     */
    public function getBaProjectHours()
    {
        return $this->ba_project_hours;
    }

    /**
     * @param mixed $ba_project_hours
     */
    public function setBaProjectHours($ba_project_hours): void
    {
        $this->ba_project_hours = $ba_project_hours;
    }

    /**
     * @return mixed
     */
    public function getOnHold()
    {
        return $this->on_hold;
    }

    /**
     * @param mixed $on_hold
     */
    public function setOnHold($on_hold): void
    {
        $this->on_hold = $on_hold;
    }

    /**
     * @return mixed
     */
    public function getHoldComment()
    {
        return $this->hold_comment;
    }

    /**
     * @param mixed $hold_comment
     */
    public function setHoldComment($hold_comment): void
    {
        $this->hold_comment = $hold_comment;
    }
}
 
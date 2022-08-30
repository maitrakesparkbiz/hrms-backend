<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="company_project")
 */

class CompanyProject
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

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
    protected $project_doc;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $est_time;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    protected $created_by;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     */
    protected $updated_by;

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
     * @ORM\Column(type="string")
     */
    protected $extra_hours_comment;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $on_hold;

    /**
     * @ORM\Column(type="string")
     */
    protected $hold_comment;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $is_started;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $is_closed;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $is_tl;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $is_own;

    /**
     * @ORM\ManyToOne(targetEntity="Option_master")
     * @ORM\JoinColumn(name="flag", referencedColumnName="id")
     */
    protected $flag;

    /**
     * @ORM\OneToMany(targetEntity="CompanyProjectBa", mappedBy="company_project_id")
     */
    protected $assigned_ba;

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

    /**
     * @ORM\OneToMany(targetEntity="CompanyProjectEmpTimings", mappedBy="company_project_id")
     */
    protected $company_projects;

    public function __construct($data)
    {
        $this->created_by = isset($data['created_by']) ? $data['created_by'] : null;
        $this->updated_by = isset($data['updated_by']) ? $data['updated_by'] : null;
        $this->flag = isset($data['flag']) ? $data['flag'] : null;
        $this->client_name = isset($data['client_name']) ? $data['client_name'] : '';
        $this->project_name = isset($data['project_name']) ? $data['project_name'] : '';
        $this->project_description = isset($data['project_description']) ? $data['project_description'] : '';
        $this->project_doc = isset($data['project_doc']) ? $data['project_doc'] : '';
        $this->threshold_limit1 = isset($data['threshold_limit1']) ? $data['threshold_limit1'] : 0;
        $this->threshold_limit2 = isset($data['threshold_limit2']) ? $data['threshold_limit2'] : 0;
        $this->deadline = isset($data['deadline']) ? $data['deadline'] : null;
        $this->est_time = isset($data['est_time']) ? $data['est_time'] : 0;
        $this->extra_hours_comment = isset($data['extra_hours_comment']) ? $data['extra_hours_comment'] : '';
        $this->is_started = isset($data['is_started']) ? $data['is_started'] : 0;
        $this->on_hold = isset($data['on_hold']) ? $data['on_hold'] : 0;
        $this->is_tl = isset($data['is_tl']) ? $data['is_tl'] : 0;
        $this->is_own = isset($data['is_own']) ? $data['is_own'] : 0;
        $this->is_closed = isset($data['is_closed']) ? $data['is_closed'] : 0;
        $this->hold_comment = isset($data['hold_comment']) ? $data['hold_comment'] : '';
    }

    /**
     * @return mixed
     */
    public function getIsTl()
    {
        return $this->is_tl;
    }

    /**
     * @param mixed $is_tl
     */
    public function setIsTl($is_tl): void
    {
        $this->is_tl = $is_tl;
    }

    /**
     * @return mixed
     */
    public function getUpdatedBy()
    {
        return $this->updated_by;
    }

    /**
     * @param mixed $updated_by
     */
    public function setUpdatedBy($updated_by): void
    {
        $this->updated_by = $updated_by;
    }

    /**
     * @return mixed
     */
    public function getIsOwn()
    {
        return $this->is_own;
    }

    /**
     * @param mixed $is_tl
     */
    public function setIsOwn($is_own): void
    {
        $this->is_own = $is_own;
    }

    /**
     * @return mixed
     */
    public function getIsClosed()
    {
        return $this->is_closed;
    }

    /**
     * @param mixed $is_tl
     */
    public function setIsClosed($is_closed): void
    {
        $this->is_closed = $is_closed;
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
    public function getExtraHoursComment()
    {
        return $this->extra_hours_comment;
    }

    /**
     * @param mixed $extra_hours_comment
     */
    public function setExtraHoursComment($extra_hours_comment): void
    {
        $this->extra_hours_comment = $extra_hours_comment;
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
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * @param mixed $flag
     */
    public function setFlag($flag): void
    {
        $this->flag = $flag;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}

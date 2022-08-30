<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;
/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="leave_application")
 */
class LeaveApplication
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User",inversedBy="LeaveApplication")
     * @ORM\JOinColumn(name="user",referencedColumnName="id")
     */
    protected $user_id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Leave_type",inversedBy="LeaveApplication")
     * @ORM\JOinColumn(name="leave_type",referencedColumnName="id")
     */
    protected $leave_type;

    /**
     *
     * @ORM\Column(type="date", nullable=true)
     */
    protected $leave_date;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    protected $half_day;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $leave_count;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $status;
    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $reason;


    /**
     * @ORM\Column(type="boolean")
     */
    protected $final_approve;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $reject_reason;

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
        $this->user_id = isset($data["user_id"]) ? $data["user_id"] : NULL;
        $this->leave_type = isset($data["leave_type"]) ? $data["leave_type"] : NULL;
        $this->leave_date = isset($data["leave_date"]) ? $data["leave_date"] : NULL;
        $this->half_day = isset($data["half_day"]) ? $data["half_day"] : 0;
        $this->reason = isset($data["reason"]) ? $data["reason"] : "";
        $this->status = isset($data["status"]) ? $data["status"] : "";
        $this->leave_count = isset($data["leave_count"]) ? $data["leave_count"] : "";
        $this->final_approve = isset($data["final_approve"])? $data["final_approve"] : 0;
        $this->reject_reason = isset($data["reject_reason"]) ? $data["reject_reason"] : NULL;
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

    function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFinalApprove()
    {
        return $this->final_approve;
    }

    /**
     * @param mixed $final_approve
     */
    public function setFinalApprove($final_approve): void
    {
        $this->final_approve = $final_approve;
    }

    function getLeave_type()
    {
        return $this->leave_type;
    }

    function getLeaveDate()
    {
        return $this->leave_date;
    }

    function getHalf_day()
    {
        return $this->half_day;
    }

    function getReason()
    {
        return $this->reason;
    }

    function setLeave_type($leave_type)
    {
        $this->leave_type = $leave_type;
    }

    function setLeaveDate($leave_date)
    {
        $this->leave_date = $leave_date;
    }

    function setHalf_day($half_day)
    {
        $this->half_day = $half_day;
    }

    function setReason($reason)
    {
        $this->reason = $reason;
    }
    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function getStatus()
    {
        return $this->status;
    }


    public function setStatus($status)
    {
        $this->status = $status;
    }


    function getLeave_count()
    {
        return $this->leave_count;
    }

    function setLeave_count($leave_count)
    {
        $this->leave_count = $leave_count;
    }


}
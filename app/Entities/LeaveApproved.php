<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="leave_approved")
 */
class  LeaveApproved
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User",inversedBy="LeaveApproved")
     * @ORM\JoinColumn(name="user_id",referencedColumnName="id")
     */
    protected $user_id;

    /**
     * @ORM\ManyToOne(targetEntity="LeaveApplication",inversedBy="LeaveApproved")
     * @ORM\JoinColumn(name="leave_id",referencedColumnName="id")
     */
    protected $leave_id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Leave_type",inversedBy="LeaveApproved")
     * @ORM\JOinColumn(name="leave_type",referencedColumnName="id")
     */
    protected $leave_type;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $leave_date;

    /**
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
     * @ORM\Column(type="text", nullable=true)
     */
    protected $reason;

    /**
     * @ORM\Column(type="string")
     */
    protected $status;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $reject_reason;

    /**
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    protected $is_deleted;

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
        $this->leave_id = isset($data['leave_id']) ? $data['leave_id'] : NULL;
        $this->half_day = isset($data["half_day"]) ? $data["half_day"] : 0;
        $this->reason = isset($data["reason"]) ? $data["reason"] : "";
        $this->status = isset($data["status"]) ? $data["status"] : "";
        $this->leave_count = isset($data["leave_count"]) ? $data["leave_count"] : "";
        $this->reject_reason = isset($data["reject_reason"]) ? $data["reject_reason"] : NULL;
        $this->is_deleted = isset($data["is_deleted"]) ? $data["is_deleted"] : 0;
    }

    /**
     * @param mixed $leave_id
     */
    public function setLeaveId($leave_id): void
    {
        $this->leave_id = $leave_id;
    }



    /**
     * @return mixed
     */
    public function getLeaveId()
    {
        return $this->leave_id;
    }



    /**
     * @return mixed
     */
    public function getisDeleted()
    {
        return $this->is_deleted;
    }

    /**
     * @param mixed $is_deleted
     */
    public function setIsDeleted($is_deleted): void
    {
        $this->is_deleted = $is_deleted;
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getLeaveType()
    {
        return $this->leave_type;
    }

    /**
     * @param mixed $leave_type
     */
    public function setLeaveType($leave_type): void
    {
        $this->leave_type = $leave_type;
    }

    /**
     * @return mixed
     */
    public function getLeaveDate()
    {
        return $this->leave_date;
    }

    /**
     * @param mixed $leave_date
     */
    public function setLeaveDate($leave_date): void
    {
        $this->leave_date = $leave_date;
    }

    /**
     * @return mixed
     */
    public function getHalfDay()
    {
        return $this->half_day;
    }

    /**
     * @param mixed $half_day
     */
    public function setHalfDay($half_day): void
    {
        $this->half_day = $half_day;
    }

    /**
     * @return mixed
     */
    public function getLeaveCount()
    {
        return $this->leave_count;
    }

    /**
     * @param mixed $leave_count
     */
    public function setLeaveCount($leave_count): void
    {
        $this->leave_count = $leave_count;
    }

    /**
     * @return mixed
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param mixed $reason
     */
    public function setReason($reason): void
    {
        $this->reason = $reason;
    }


}


?>
<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;
/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="leave_type")
 */
class Leave_type
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $leavetype;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Option_master",inversedBy="Leave_type")
     * @ORM\JOinColumn(name="gender",referencedColumnName="id")
     */
    protected $gender;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Option_master",inversedBy="Leave_type")
     * @ORM\JOinColumn(name="status",referencedColumnName="id")
     */
    protected $status;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $count_type;

    /**
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $count;

    /**
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $max_leave_month;

    /**
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $max_consecutive_leave_month;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $probation;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $half_day;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $intervening_holiday;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Option_master",inversedBy="Leave_type")
     * @ORM\JOinColumn(name="over_utilization",referencedColumnName="id")
     */
    protected $over_utilization;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $unused_leave;

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
        $this->leavetype = isset($data["leavetype"]) ? $data["leavetype"] : "";
        $this->gender = isset($data["gender"]) ? $data["gender"] : NULL;
        $this->status = isset($data["status"]) ? $data["status"] : NULL;
        $this->count_type = isset($data["count_type"]) ? $data["count_type"] : "";
        $this->count = isset($data["count"]) ? $data["count"] : "";
        $this->max_leave_month = isset($data["max_leave_month"]) ? $data["max_leave_month"] : "";
        $this->max_consecutive_leave_month = isset($data["max_consecutive_leave_month"]) ? $data["max_consecutive_leave_month"] : "";
        $this->probation = isset($data["probation"]) ? $data["probation"] : "";
        $this->half_day = isset($data["half_day"]) ? $data["half_day"] : "";
        $this->intervening_holiday = isset($data["intervening_holiday"]) ? $data["intervening_holiday"] : "";
        $this->over_utilization = isset($data["over_utilization"]) ? $data["over_utilization"] : NULL;
        $this->unused_leave = isset($data["unused_leave"]) ? $data["unused_leave"] : "";

    }

    function getId()
    {
        return $this->id;
    }

    function getLeavetype()
    {
        return $this->leavetype;
    }

    function getGender()
    {
        return $this->gender;
    }

    function getStatus()
    {
        return $this->status;
    }

    function getCount_type()
    {
        return $this->count_type;
    }

    function getCount()
    {
        return $this->count;
    }

    function getMax_leave_month()
    {
        return $this->max_leave_month;
    }

    function getMax_consecutive_leave_month()
    {
        return $this->max_consecutive_leave_month;
    }

    function getProbation()
    {
        return $this->probation;
    }

    function getHalf_day()
    {
        return $this->half_day;
    }

    function getIntervening_holiday()
    {
        return $this->intervening_holiday;
    }

    function getOver_utilization()
    {
        return $this->over_utilization;
    }

    function getUnused_leave()
    {
        return $this->unused_leave;
    }

    function setLeavetype($leavetype)
    {
        $this->leavetype = $leavetype;
    }

    function setGender($gender)
    {
        $this->gender = $gender;
    }

    function setStatus($status)
    {
        $this->status = $status;
    }

    function setCount_type($count_type)
    {
        $this->count_type = $count_type;
    }

    function setCount($count)
    {
        $this->count = $count;
    }

    function setMax_leave_month($max_leave_month)
    {
        $this->max_leave_month = $max_leave_month;
    }

    function setMax_consecutive_leave_month($max_consecutive_leave_month)
    {
        $this->max_consecutive_leave_month = $max_consecutive_leave_month;
    }

    function setProbation($probation)
    {
        $this->probation = $probation;
    }

    function setHalf_day($half_day)
    {
        $this->half_day = $half_day;
    }

    function setIntervening_holiday($intervening_holiday)
    {
        $this->intervening_holiday = $intervening_holiday;
    }

    function setOver_utilization($over_utilization)
    {
        $this->over_utilization = $over_utilization;
    }

    function setUnused_leave($unused_leave)
    {
        $this->unused_leave = $unused_leave;
    }


}
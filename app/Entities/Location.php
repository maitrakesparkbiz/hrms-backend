<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="location")
 */
class Location
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
    protected $name;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $address;

    /**
     *
     * @ORM\Column(type="time", nullable=true)
     */
    protected $office_start_time;

    /**
     *
     * @ORM\Column(type="time", nullable=true)
     */
    protected $office_end_time;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Option_master",inversedBy="Location")
     * @ORM\JOinColumn(name="leave_start_month",referencedColumnName="id")
     */
    protected $leave_start_month;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    protected $employee_self_checking;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    protected $overtime_pay;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    protected $half_day_allowed;

    /**
     *
     * @ORM\Column(type="time", nullable=true)
     */
    protected $half_day_hours;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    protected $clock_reminder;

    /**
     *
     * @ORM\Column(type="time",nullable=true)
     */
    protected $clock_reminder_time;

    /**
     *
     * @ORM\Column(type="string",nullable=true)
     */
    protected $late_mark_after_minute;

    /**
     *
     * @ORM\Column(type="text",nullable=true)
     */
    protected $allowed_ip;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $working_days;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $alt_sat;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="location")
     */
    protected $employees;


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
        $this->name = isset($data["name"]) ? $data["name"] : "";
        $this->address = isset($data["address"]) ? $data["address"] : "";
        $this->office_start_time = isset($data["office_start_time"]) ? $data["office_start_time"] : NULL;
        $this->office_end_time = isset($data["office_end_time"]) ? $data["office_end_time"] : NULL;
        $this->leave_start_month = isset($data["leave_start_month"]) ? $data["leave_start_month"] : NULL;
        $this->employee_self_checking = isset($data["employee_self_checking"]) ? $data["employee_self_checking"] : 0;
        $this->overtime_pay = isset($data["overtime_pay"]) ? $data["overtime_pay"] : 0;
        $this->half_day_allowed = isset($data["half_day_allowed"]) ? $data["half_day_allowed"] : 0;
        $this->half_day_hours = isset($data["half_day_hours"]) ? $data["half_day_hours"] : NULL;
        $this->clock_reminder = isset($data["clock_reminder"]) ? $data["clock_reminder"] : 0;
        $this->clock_reminder_time = isset($data["clock_reminder_time"]) ? $data["clock_reminder_time"] : NULL;
        $this->late_mark_after_minute = isset($data["late_mark_after_minute"]) ? $data["late_mark_after_minute"] : '';
        $this->allowed_ip = isset($data["allowed_ip"]) ? $data["allowed_ip"] : '';
        $this->working_days = isset($data["working_days"]) ? $data["working_days"] : '';
        $this->alt_sat = isset($data["alt_sat"]) ? $data["alt_sat"] : NULL;
    }


    function getId()
    {
        return $this->id;
    }

    function getName()
    {
        return $this->name;
    }

    function getAddress()
    {
        return $this->address;
    }

    function getOffice_start_time()
    {
        return $this->office_start_time;
    }

    function getOffice_end_time()
    {
        return $this->office_end_time;
    }

    function getLeave_start_month()
    {
        return $this->leave_start_month;
    }

    function getEmployee_self_checking()
    {
        return $this->employee_self_checking;
    }

    function getOvertime_pay()
    {
        return $this->overtime_pay;
    }

    function getClock_reminder()
    {
        return $this->clock_reminder;
    }

    function getClock_reminder_time()
    {
        return $this->clock_reminder_time;
    }

    function getLate_mark_after_minute()
    {
        return $this->late_mark_after_minute;
    }

    function getAllowed_ip()
    {
        return $this->allowed_ip;
    }

    function setName($name)
    {
        $this->name = $name;
    }

    function setAddress($address)
    {
        $this->address = $address;
    }

    function setOffice_start_time($office_start_time)
    {
        $this->office_start_time = $office_start_time;
    }

    function setOffice_end_time($office_end_time)
    {
        $this->office_end_time = $office_end_time;
    }

    function setLeave_start_month($leave_start_month)
    {
        $this->leave_start_month = $leave_start_month;
    }

    function setEmployee_self_checking($employee_self_checking)
    {
        $this->employee_self_checking = $employee_self_checking;
    }

    function setOvertime_pay($overtime_pay)
    {
        $this->overtime_pay = $overtime_pay;
    }

    function setClock_reminder($clock_reminder)
    {
        $this->clock_reminder = $clock_reminder;
    }

    function setClock_reminder_time($clock_reminder_time)
    {
        $this->clock_reminder_time = $clock_reminder_time;
    }

    function setLate_mark_after_minute($late_mark_after_minute)
    {
        $this->late_mark_after_minute = $late_mark_after_minute;
    }

    function setAllowed_ip($allowed_ip)
    {
        $this->allowed_ip = $allowed_ip;
    }

    /**
     * @return mixed
     */
    public function getWorkingDays()
    {
        return $this->working_days;
    }

    /**
     * @param mixed $working_days
     */
    public function setWorkingDays($working_days): void
    {
        $this->working_days = $working_days;
    }

    /**
     * @return mixed
     */
    public function getAltSat()
    {
        return $this->alt_sat;
    }

    /**
     * @param mixed $alt_sat
     */
    public function setAltSat($alt_sat): void
    {
        $this->alt_sat = $alt_sat;
    }

    /**
     * @return mixed
     */
    public function getHalfDayAllowed()
    {
        return $this->half_day_allowed;
    }

    /**
     * @param mixed $half_day_allowed
     */
    public function setHalfDayAllowed($half_day_allowed): void
    {
        $this->half_day_allowed = $half_day_allowed;
    }

    /**
     * @return mixed
     */
    public function getHalfDayHours()
    {
        return $this->half_day_hours;
    }

    /**
     * @param mixed $half_day_hours
     */
    public function setHalfDayHours($half_day_hours): void
    {
        $this->half_day_hours = $half_day_hours;
    }





}

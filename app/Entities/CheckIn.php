<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;


/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="checkin")
 */
class CheckIn
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User",inversedBy="Checkin")
     * @ORM\JoinColumn(name="emp_id",referencedColumnName="id")
     */
    protected $emp_id;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    protected $check_in_time;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    protected $check_out_time;

    /**
     * @ORM\ManyToOne(targetEntity="User",inversedBy="Checkin")
     * @ORM\JOinColumn(name="check_out_emp_id",referencedColumnName="id")
     */
    protected $check_out_emp_id;

    /**
     * @ORM\OneToMany(targetEntity="Breaks", mappedBy="check_in_id")
     */
    protected $breaks;

    /**
     * @ORM\OneToMany(targetEntity="Comments", mappedBy="check_in_id")
     */
    protected $comments;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $check_in_ip;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $check_out_ip;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $is_late;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $late_minutes;

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
        $this->emp_id = isset($data['emp_id']) ? $data['emp_id'] : NULL;
        $this->check_in_time = isset($data['check_in_time']) ? $data['check_in_time'] : NULL;
        $this->check_out_time = isset($data['check_out_time']) ? $data['check_out_time'] : NULL;
        $this->check_out_emp_id = isset($data['check_out_emp_id']) ? $data['check_out_emp_id'] : NULL;
        $this->check_in_ip = isset($data['check_in_ip']) ? $data['check_in_ip'] : NULL;
        $this->check_out_ip = isset($data['check_out_ip']) ? $data['check_out_ip'] : NULL;
        $this->check_out_time = isset($data['check_out_time']) ? $data['check_out_time'] : NULL;
        $this->is_late = isset($data['is_late']) ? $data['is_late'] : NULL;
        $this->late_minutes = isset($data['late_minutes']) ? $data['late_minutes'] : NULL;
    }

    /**
     * @param mixed $breaks
     */


    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getisLate()
    {
        return $this->is_late;
    }

    /**
     * @param mixed $is_late
     */
    public function setIsLate($is_late): void
    {
        $this->is_late = $is_late;
    }

    /**
     * @return mixed
     */
    public function getLateMinutes()
    {
        return $this->late_minutes;
    }

    /**
     * @param mixed $late_minutes
     */
    public function setLateMinutes($late_minutes): void
    {
        $this->late_minutes = $late_minutes;
    }

    /**
     * @return mixed
     */
    public function getCheckInIp()
    {
        return $this->check_in_ip;
    }

    /**
     * @param mixed $check_in_ip
     */
    public function setCheckInIp($check_in_ip): void
    {
        $this->check_in_ip = $check_in_ip;
    }

    /**
     * @return mixed
     */
    public function getCheckOutIp()
    {
        return $this->check_out_ip;
    }

    /**
     * @param mixed $check_out_ip
     */
    public function setCheckOutIp($check_out_ip): void
    {
        $this->check_out_ip = $check_out_ip;
    }



    /**
     * @return mixed
     */
    public function getEmpId()
    {
        return $this->emp_id;
    }

    /**
     * @param mixed $emp_id
     */
    public function setEmpId($emp_id): void
    {
        $this->emp_id = $emp_id;
    }

    /**
     * @return mixed
     */
    public function getCheckOutId()
    {
        return $this->check_out_emp_id;
    }

    /**
     * @param mixed $check_out_emp_id
     */
    public function setCheckOutId($check_out_emp_id): void
    {
        $this->check_out_emp_id = $check_out_emp_id;
    }

    /**
     * @return mixed
     */
    public function getCheckInTime()
    {
        return $this->check_in_time;
    }

    /**
     * @param mixed $check_in_time
     */
    public function setCheckInTime($check_in_time): void
    {
        $this->check_in_time = $check_in_time;
    }

    /**
     * @return mixed
     */
    public function getCheckOutTime()
    {
        return $this->check_out_time;
    }

    /**
     * @param mixed $check_out_time
     */
    public function setCheckOutTime($check_out_time): void
    {
        $this->check_out_time = $check_out_time;
    }


}
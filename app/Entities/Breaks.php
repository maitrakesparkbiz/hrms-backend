<?php


namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;
/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="breaks")
 */

class Breaks
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User",inversedBy="Breaks")
     * @ORM\JoinColumn(name="emp_id",referencedColumnName="id")
     */
    protected $emp_id;

    /**
     * @ORM\ManyToOne(targetEntity="CheckIn", inversedBy="Breaks")
     * @ORM\JoinColumn(name="check_in_id", referencedColumnName="id")
     */
    protected $check_in_id;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    protected $break_in_time;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    protected $break_out_time;

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
        $this->check_in_id = isset($data['check_in_id']) ? $data['check_in_id'] : NULL;
        $this->break_in_time = isset($data['break_in_time']) ? $data['break_in_time'] : NULL;
        $this->break_out_time = isset($data['break_out_time']) ? $data['break_out_time'] : NULL;
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
    public function getCheckInId()
    {
        return $this->check_in_id;
    }

    /**
     * @param mixed $check_in_id
     */
    public function setCheckInId($check_in_id): void
    {
        $this->check_in_id = $check_in_id;
    }

    /**
     * @return mixed
     */
    public function getBreakInTime()
    {
        return $this->break_in_time;
    }

    /**
     * @param mixed $break_in_time
     */
    public function setBreakInTime($break_in_time): void
    {
        $this->break_in_time = $break_in_time;
    }

    /**
     * @return mixed
     */
    public function getBreakOutTime()
    {
        return $this->break_out_time;
    }

    /**
     * @param mixed $break_out_time
     */
    public function setBreakOutTime($break_out_time): void
    {
        $this->break_out_time = $break_out_time;
    }





}

?>
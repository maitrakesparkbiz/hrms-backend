<?php

namespace App\Entities;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="holiday_employee")
 */

class Holiday_employee{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Holiday")
     * @ORM\JoinColumn(name="holiday_id", referencedColumnName="id")
     */
    protected $holiday_id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="emp_id", referencedColumnName="id")
     */
    protected $emp_id;


    public function __construct($data)
    {
        $this->holiday_id = isset($data['holiday_id']) ? $data['holiday_id'] : NULL;
        $this->emp_id = isset($data['emp_id']) ? $data['emp_id'] : NULL;
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
    public function getHolidayId()
    {
        return $this->holiday_id;
    }

    /**
     * @param mixed $holiday_id
     */
    public function setHolidayId($holiday_id): void
    {
        $this->holiday_id = $holiday_id;
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


}


?>
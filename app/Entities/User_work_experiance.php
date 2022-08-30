<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="user_work_experiance")
 */
class User_work_experiance
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
    protected $company_name;

    /**
     * 
     * @ORM\Column(type="string", nullable=true)
     */
    protected $designation;

    /**
     * 
     * @ORM\Column(type="date",nullable=true)
     */
    protected $from_date;

    /**
     * 
     * @ORM\Column(type="date",nullable=true)
     */
    protected $to_date;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $details;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $relieving_letter;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $exp_letter;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $salary_slip;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User",inversedBy="User_work_experiance")
     * @ORM\JOinColumn(name="user",referencedColumnName="id")
     */
    protected $user;

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
        $this->company_name = isset($data["company_name"]) ? $data["company_name"] : '';
        $this->designation = isset($data["designation"]) ? $data["designation"] : "";
        $this->from_date = isset($data["from_date"]) ? $data["from_date"] : NULL;
        $this->to_date = isset($data["to_date"]) ? $data["to_date"] : NULL;
        $this->details = isset($data["details"]) ? $data["details"] : "";
        $this->user = isset($data["user"]) ? $data["user"] : NULL;
        $this->relieving_letter = isset($data["relieving_letter"]) ? $data["relieving_letter"] : '';
        $this->exp_letter = isset($data["exp_letter"]) ? $data["exp_letter"] : '';
        $this->salary_slip = isset($data["exp_letter"]) ? $data["exp_letter"] : '';
    }

    function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getRelievingLetter()
    {
        return $this->relieving_letter;
    }

    /**
     * @param mixed $relieving_letter
     */
    public function setRelievingLetter($relieving_letter): void
    {
        $this->relieving_letter = $relieving_letter;
    }

    /**
     * @return mixed
     */
    public function getExpLetter()
    {
        return $this->exp_letter;
    }

    /**
     * @param mixed $exp_letter
     */
    public function setExpLetter($exp_letter): void
    {
        $this->exp_letter = $exp_letter;
    }

    /**
     * @return mixed
     */
    public function getSalarySlip()
    {
        return $this->salary_slip;
    }

    /**
     * @param mixed $salary_slip
     */
    public function setSalarySlip($salary_slip): void
    {
        $this->salary_slip = $salary_slip;
    }

    function getDesignation()
    {
        return $this->designation;
    }

    function getFrom_date()
    {
        return $this->from_date;
    }

    function getTo_date()
    {
        return $this->to_date;
    }

    function getDetails()
    {
        return $this->details;
    }

    function getUser()
    {
        return $this->user;
    }

    public function getCompanyName()
    {
        return $this->company_name;
    }

    public function setCompanyName($company_name)
    {
        $this->company_name = $company_name;
    }



    function setDesignation($designation)
    {
        $this->designation = $designation;
    }

    function setFrom_date($from_date)
    {
        $this->from_date = $from_date;
    }

    function setTo_date($to_date)
    {
        $this->to_date = $to_date;
    }

    function setDetails($details)
    {
        $this->details = $details;
    }

    function setUser($user)
    {
        $this->user = $user;
    }



}

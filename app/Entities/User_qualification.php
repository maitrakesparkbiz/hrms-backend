<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="user_qualification")
 */
class User_qualification
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Option_master",inversedBy="User_account")
     * @ORM\JOinColumn(name="education_type",referencedColumnName="id")
     */
    protected $education_type;


    /**
     * 
     * @ORM\Column(type="string", nullable=true)
     */
    protected $university_name;

    /**
     * 
     * @ORM\Column(type="date",nullable=true)
     */
    protected $start_date;

    /**
     * 
     * @ORM\Column(type="date",nullable=true)
     */
    protected $end_date;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $doc_copy;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $degree;

    /**
     * 
     * @ORM\Column(type="text", nullable=true)
     */
    protected $details;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User",inversedBy="User_qualification")
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
        $this->education_type = isset($data["education_type"]) ? $data["education_type"] : NULL;
        $this->university_name = isset($data["university_name"]) ? $data["university_name"] : "";
        $this->start_date = isset($data["start_date"]) ? $data["start_date"] : NULL;
        $this->end_date = isset($data["end_date"]) ? $data["end_date"] : NULL;
        $this->details = isset($data["details"]) ? $data["details"] : "";
        $this->doc_copy = isset($data["doc_copy"]) ? $data["doc_copy"] : '';
        $this->degree = isset($data["degree"]) ? $data["degree"]:'';
        $this->user = isset($data["user"]) ? $data["user"] : NULL;
    }

    /**
     * @return mixed
     */
    public function getDegree()
    {
        return $this->degree;
    }

    /**
     * @param mixed $degree
     */
    public function setDegree($degree): void
    {
        $this->degree = $degree;
    }


    function getId()
    {
        return $this->id;
    }

    function getEducation_type()
    {
        return $this->education_type;
    }

    function getAccount_holder_name()
    {
        return $this->account_holder_name;
    }

    function getUniversity_name()
    {
        return $this->university_name;
    }

    /**
     * @return mixed
     */
    public function getDocCopy()
    {
        return $this->doc_copy;
    }

    /**
     * @param mixed $doc_copy
     */
    public function setDocCopy($doc_copy): void
    {
        $this->doc_copy = $doc_copy;
    }

    function getStart_date()
    {
        return $this->start_date;
    }

    function getEnd_date()
    {
        return $this->end_date;
    }

    function getDetails()
    {
        return $this->details;
    }

    function getUser()
    {
        return $this->user;
    }


    function setEducation_type($education_type)
    {
        $this->education_type = $education_type;
    }

    function setAccount_holder_name($account_holder_name)
    {
        $this->account_holder_name = $account_holder_name;
    }

    function setUniversity_name($university_name)
    {
        $this->university_name = $university_name;
    }

    function setStart_date($start_date)
    {
        $this->start_date = $start_date;
    }

    function setEnd_date($end_date)
    {
        $this->end_date = $end_date;
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

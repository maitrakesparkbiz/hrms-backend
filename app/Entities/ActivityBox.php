<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;
/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="activity_box")
 */
class ActivityBox
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User",inversedBy="ActivityBox")
     * @ORM\JOinColumn(name="emp_id",referencedColumnName="id")
     */
    protected $emp_id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User",inversedBy="ActivityBox")
     * @ORM\JOinColumn(name="from_emp",referencedColumnName="id")
     */
    protected $from_emp;

    /**
     *
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     *
     * @ORM\Column(type="string")
     */
    protected $details;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $is_read;

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
        $this->emp_id = isset($data["emp_id"]) ? $data["emp_id"] : NULL;
        $this->from_emp = isset($data["from_emp"]) ? $data["from_emp"] : NULL;
        $this->title = isset($data["title"]) ? $data["title"] : "";
        $this->details = isset($data["details"]) ? $data["details"] : "";
        $this->is_read = isset($data["is_read"]) ? $data["is_read"] : 0;

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
    public function getFromEmp()
    {
        return $this->from_emp;
    }

    /**
     * @param mixed $from_emp
     */
    public function setFromEmp($from_emp): void
    {
        $this->from_emp = $from_emp;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param mixed $details
     */
    public function setDetails($details): void
    {
        $this->details = $details;
    }

    /**
     * @return mixed
     */
    public function getisRead()
    {
        return $this->is_read;
    }

    /**
     * @param mixed $is_read
     */
    public function setIsRead($is_read): void
    {
        $this->is_read = $is_read;
    }





}
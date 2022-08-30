<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="employee_timings")
 */
class EmployeeTimings
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="CompanyProject")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    protected $project_id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="emp_id",referencedColumnName="id")
     */
    protected $emp_id;

    /**
     * @ORM\Column(type="decimal",precision=10, scale=2)
     */
    protected $record_hours;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $record_date;


    /**
     * @ORM\Column(type="string")
     */
    protected $comment;

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

    public function __construct($data = null)
    {
        $this->project_id = isset($data['project_id']) ? $data['project_id'] : null;
        $this->emp_id = isset($data['emp_id']) ? $data['emp_id'] : null;
        $this->record_hours = isset($data['record_hours']) ? $data['record_hours'] : 0;
        $this->record_date = isset($data['record_date']) ? $data['record_date'] : null;
        $this->comment = isset($data['comment']) ? $data['comment'] : '';
    }

    /**
     * @return mixed
     */
    public function getProjectId()
    {
        return $this->project_id;
    }

    /**
     * @param mixed $project_id
     */
    public function setProjectId($project_id): void
    {
        $this->project_id = $project_id;
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
    public function getRecordHours()
    {
        return $this->record_hours;
    }

    /**
     * @param mixed $record_hours
     */
    public function setRecordHours($record_hours): void
    {
        $this->record_hours = $record_hours;
    }

    /**
     * @return mixed
     */
    public function getRecordDate()
    {
        return $this->record_date;
    }

    /**
     * @param mixed $record_date
     */
    public function setRecordDate($record_date): void
    {
        $this->record_date = $record_date;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }




}

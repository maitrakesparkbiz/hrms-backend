<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="company_project_emp_timings")
 */
class CompanyProjectEmpTimings
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="CompanyProject")
     * @ORM\JoinColumn(name="company_project_id", referencedColumnName="id")
     */
    protected $company_project_id;

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
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JOinColumn(name="created_by",referencedColumnName="id")
     */
    protected $created_by;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $record_date;


    /**
     * @ORM\Column(type="string")
     */
    protected $redmark_comment;

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
        $this->company_project_id = isset($data['company_project_id']) ? $data['company_project_id'] : null;
        $this->emp_id = isset($data['emp_id']) ? $data['emp_id'] : null;
        $this->created_by = isset($data['created_by']) ? $data['created_by'] : null;
        $this->record_hours = isset($data['record_hours']) ? $data['record_hours'] : 0;
        $this->record_date = isset($data['record_date']) ? $data['record_date'] : null;
        $this->redmark_comment = isset($data['redmark_comment']) ? $data['redmark_comment'] : '';
    }

    /**
     * @return mixed
     */
    public function getCompanyProjectId()
    {
        return $this->company_project_id;
    }

    /**
     * @param mixed $company_project_id
     */
    public function setCompanyProjectId($company_project_id): void
    {
        $this->company_project_id = $company_project_id;
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
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * @param mixed $created_by
     */
    public function setCreatedBy($created_by): void
    {
        $this->created_by = $created_by;
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
    public function getRedmarkComment()
    {
        return $this->redmark_comment;
    }

    /**
     * @param mixed $redmark_comment
     */
    public function setRedmarkComment($redmark_comment): void
    {
        $this->redmark_comment = $redmark_comment;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


}
 
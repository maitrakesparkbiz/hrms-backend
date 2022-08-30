<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="company_project_ba")
 */
class CompanyProjectBa
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
     * @ORM\JoinColumn(name="emp_id", referencedColumnName="id")
     */
    protected $emp_id;

    /**
     * @ORM\ManyToOne(targetEntity="Option_master")
     * @ORM\JoinColumn(name="flag", referencedColumnName="id")
     */
    protected $flag;

    /**
     * @ORM\ManyToOne(targetEntity="Option_master")
     * @ORM\JoinColumn(name="ba_tl_flag", referencedColumnName="id")
     */
    protected $ba_tl_flag;

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
        $this->flag = isset($data['flag']) ? $data['flag'] : null;
        $this->ba_tl_flag = isset($data['ba_tl_flag']) ? $data['ba_tl_flag'] : null;
        $this->emp_id = isset($data['emp_id']) ? $data['emp_id'] : null;
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
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * @param mixed $flag
     */
    public function setFlag($flag): void
    {
        $this->flag = $flag;
    }

    /**
     * @return mixed
     */
    public function getBaTlFlag()
    {
        return $this->ba_tl_flag;
    }

    /**
     * @param mixed $ba_tl_flag
     */
    public function setBaTlFlag($ba_tl_flag): void
    {
        $this->ba_tl_flag = $ba_tl_flag;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    
}
 
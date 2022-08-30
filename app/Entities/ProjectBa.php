<?php


namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="project_ba")
 */
class ProjectBa
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    protected $project_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $est_time;

    /**
     * @ORM\ManyToOne(targetEntity="Option_master")
     * @ORM\JoinColumn(name="flag", referencedColumnName="id")
     */
    protected $flag;

    /**
     * @ORM\ManyToOne(targetEntity="Option_master")
     * @ORM\JoinColumn(name="jr_ba_flag", referencedColumnName="id")
     */
    protected $jr_ba_flag;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="emp_id", referencedColumnName="id")
     */
    protected $emp_id;

    /**
     * @ORM\OneToMany(targetEntity="ProjectJrBa", mappedBy="ba_project_id")
     */
    protected $project_jr_ba;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="assigned_jr_ba", referencedColumnName="id")
     */
    protected $assigned_jr_ba;

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
        $this->project_id = isset($data['project_id']) ? $data['project_id'] : null;
        $this->est_time = isset($data['est_time']) ? $data['est_time'] : 0;
        $this->flag = isset($data['flag']) ? $data['flag'] : null;
        $this->jr_ba_flag = isset($data['jr_ba_flag']) ? $data['jr_ba_flag'] : null;
        $this->emp_id = isset($data['emp_id']) ? $data['emp_id'] : null;
        $this->assigned_jr_ba = isset($data['assigned_jr_ba']) ? $data['assigned_jr_ba'] : null;
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
    public function getEstTime()
    {
        return $this->est_time;
    }

    /**
     * @param mixed $est_time
     */
    public function setEstTime($est_time): void
    {
        $this->est_time = $est_time;
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
    public function getAssignedJrBa()
    {
        return $this->assigned_jr_ba;
    }

    /**
     * @param mixed $assigned_jr_ba
     */
    public function setAssignedJrBa($assigned_jr_ba): void
    {
        $this->assigned_jr_ba = $assigned_jr_ba;
    }

    /**
     * @return mixed
     */
    public function getJrBaFlag()
    {
        return $this->jr_ba_flag;
    }

    /**
     * @param mixed $jr_ba_flag
     */
    public function setJrBaFlag($jr_ba_flag): void
    {
        $this->jr_ba_flag = $jr_ba_flag;
    }
}
 
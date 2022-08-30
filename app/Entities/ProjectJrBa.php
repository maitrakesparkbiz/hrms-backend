<?php


namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="project_jr_ba")
 */
class ProjectJrBa
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
     * @ORM\ManyToOne(targetEntity="ProjectBa")
     * @ORM\JoinColumn(name="ba_project_id", referencedColumnName="id")
     */
    protected $ba_project_id;

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
     * @ORM\JoinColumn(name="sr_to_jr_flag", referencedColumnName="id")
     */
    protected $sr_to_jr_flag;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="emp_id", referencedColumnName="id")
     */
    protected $emp_id;


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
        $this->project_id = isset($data['project_id']) ? $data['project_id'] : NULL;
        $this->ba_project_id = isset($data['ba_project_id']) ? $data['ba_project_id'] : NULL;
        $this->est_time = isset($data['est_time']) ? $data['est_time'] : 0;
        $this->flag = isset($data['flag']) ? $data['flag'] : NULL;
        $this->sr_to_jr_flag = isset($data['sr_to_jr_flag']) ? $data['sr_to_jr_flag'] : NULL;
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
    public function getBaProjectId()
    {
        return $this->ba_project_id;
    }

    /**
     * @param mixed $ba_project_id
     */
    public function setBaProjectId($ba_project_id): void
    {
        $this->ba_project_id = $ba_project_id;
    }

    /**
     * @return mixed
     */
    public function getSrToJrFlag()
    {
        return $this->sr_to_jr_flag;
    }

    /**
     * @param mixed $sr_to_jr_flag
     */
    public function setSrToJrFlag($sr_to_jr_flag): void
    {
        $this->sr_to_jr_flag = $sr_to_jr_flag;
    }
}

?>
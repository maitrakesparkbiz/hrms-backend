<?php

namespace App\Entities;

use Doctrine\ORM\Mapping AS ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="project_conversation")
 */
class ProjectConversation
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
     * @ORM\JoinColumn(name="project_ba_id", referencedColumnName="id")
     */
    protected $project_ba_id;

    /**
     * @ORM\ManyToOne(targetEntity="ProjectJrBa")
     * @ORM\JoinColumn(name="project_jr_ba_id", referencedColumnName="id")
     */
    protected $project_jr_ba_id;

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
        $this->project_ba_id = isset($data['project_ba_id']) ? $data['project_ba_id'] : NULL;
        $this->project_jr_ba_id = isset($data['project_jr_ba_id']) ? $data['project_jr_ba_id'] : NULL;
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getProjectBaId()
    {
        return $this->project_ba_id;
    }

    /**
     * @param mixed $project_ba_id
     */
    public function setProjectBaId($project_ba_id): void
    {
        $this->project_ba_id = $project_ba_id;
    }

    /**
     * @return mixed
     */
    public function getProjectJrBaId()
    {
        return $this->project_jr_ba_id;
    }

    /**
     * @param mixed $project_jr_ba_id
     */
    public function setProjectJrBaId($project_jr_ba_id): void
    {
        $this->project_jr_ba_id = $project_jr_ba_id;
    }

}
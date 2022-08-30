<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="project")
 */
class Project
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $client_name;

    /**
     * @ORM\Column(type="string")
     */
    protected $project_name;

    /**
     * @ORM\Column(type="string")
     */
    protected $project_description;

    /**
     * @ORM\Column(type="string")
     */
    protected $client_email;

    /**
     * @ORM\Column(type="string")
     */
    protected $skype_contact;

    /**
     * @ORM\Column(type="string")
     */
    protected $project_doc;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    protected $created_by;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="assigned_to", referencedColumnName="id")
     */
    protected $assigned_to;

    /**
     * @ORM\ManyToOne(targetEntity="Option_master")
     * @ORM\JoinColumn(name="status_flag", referencedColumnName="id")
     */
    protected $status_flag;

    /**
     * @ORM\OneToMany(targetEntity="ProjectBa", mappedBy="project_id")
     */
    protected $ba;

    /**
     * @ORM\OneToMany(targetEntity="ProjectJrBa", mappedBy="project_id")
     */
    protected $jr_ba;

    /**
     * @ORM\OneToMany(targetEntity="ProjectConversation", mappedBy="project_id")
     */
    protected $conv_id;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $final_approved;

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
        $this->client_name = isset($data['client_name']) ? $data['client_name'] : '';
        $this->project_name = isset($data['project_name']) ? $data['project_name'] : '';
        $this->project_description = isset($data['project_description']) ? $data['project_description'] : '';
        $this->client_email = isset($data['client_email']) ? $data['client_email'] : '';
        $this->skype_contact = isset($data['skype_contact']) ? $data['skype_contact'] : '';
        $this->project_doc = isset($data['project_doc']) ? $data['project_doc'] : '';
        $this->created_by = isset($data['created_by']) ? $data['created_by'] : NULL;
        $this->assigned_to = isset($data['assigned_to']) ? $data['assigned_to'] : NULL;
        $this->status_flag = isset($data['status_flag']) ? $data['status_flag'] : '';
        $this->final_approved = isset($data['final_approved']) ? $data['final_approved'] : 0;
    }

    /**
     * @return mixed
     */
    public function getFinalApproved()
    {
        return $this->final_approved;
    }

    /**
     * @param mixed $final_approved
     */
    public function setFinalApproved($final_approved): void
    {
        $this->final_approved = $final_approved;
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
    public function getClientName()
    {
        return $this->client_name;
    }

    /**
     * @param mixed $client_name
     */
    public function setClientName($client_name): void
    {
        $this->client_name = $client_name;
    }

    /**
     * @return mixed
     */
    public function getProjectName()
    {
        return $this->project_name;
    }

    /**
     * @param mixed $project_name
     */
    public function setProjectName($project_name): void
    {
        $this->project_name = $project_name;
    }

    /**
     * @return mixed
     */
    public function getProjectDescription()
    {
        return $this->project_description;
    }

    /**
     * @param mixed $project_description
     */
    public function setProjectDescription($project_description): void
    {
        $this->project_description = $project_description;
    }

    /**
     * @return mixed
     */
    public function getProjectDoc()
    {
        return $this->project_doc;
    }

    /**
     * @param mixed $project_doc
     */
    public function setProjectDoc($project_doc): void
    {
        $this->project_doc = $project_doc;
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
    public function getAssignedTo()
    {
        return $this->assigned_to;
    }

    /**
     * @param mixed $assigned_to
     */
    public function setAssignedTo($assigned_to): void
    {
        $this->assigned_to = $assigned_to;
    }

    /**
     * @return mixed
     */
    public function getStatusFlag()
    {
        return $this->status_flag;
    }

    /**
     * @param mixed $status_flag
     */
    public function setStatusFlag($status_flag): void
    {
        $this->status_flag = $status_flag;
    }

    /**
     * @return mixed
     */
    public function getClientEmail()
    {
        return $this->client_email;
    }

    /**
     * @param mixed $client_email
     */
    public function setClientEmail($client_email): void
    {
        $this->client_email = $client_email;
    }

    /**
     * @return mixed
     */
    public function getSkypeContact()
    {
        return $this->skype_contact;
    }

    /**
     * @param mixed $skype_contact
     */
    public function setSkypeContact($skype_contact): void
    {
        $this->skype_contact = $skype_contact;
    }
}

?>
<?php


namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="email_generate")
 */

class EmailGenerate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="emp_id", referencedColumnName="id")
     */
    protected $emp_id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    protected $created_by;

    /**
     * @ORM\ManyToOne(targetEntity="EmailTemplates")
     * @ORM\JoinColumn(name="email_template_id", referencedColumnName="id")
     */
    protected $email_template_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $spacing;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

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
        $this->emp_id = isset($data['emp_id']) ? $data['emp_id'] : null;
        $this->created_by = isset($data['created_by']) ? $data['created_by'] : null;
        $this->email_template_id = isset($data['email_template_id']) ? $data['email_template_id'] : null;
        $this->spacing = isset($data['spacing']) ? $data['spacing'] : '';
        $this->description = isset($data['description']) ? $data['description'] : '';
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
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * @param mixed $emp_id
     */
    public function setCreatedBy($created_by): void
    {
        $this->created_by = $created_by;
    }

    /**
     * @return mixed
     */
    public function getEmailTemplateId()
    {
        return $this->email_template_id;
    }

    /**
     * @param mixed $email_template_id
     */
    public function setEmailTemplateId($email_template_id): void
    {
        $this->email_template_id = $email_template_id;
    }

    /**
     * @return mixed
     */
    public function getSpacing()
    {
        return $this->spacing;
    }

    /**
     * @param mixed $spacing
     */
    public function setSpacing($spacing): void
    {
        $this->spacing = $spacing;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }
}

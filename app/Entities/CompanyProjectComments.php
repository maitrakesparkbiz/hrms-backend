<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="company_project_comment")
 */
class CompanyProjectComments
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="CompanyProjectConv")
     * @ORM\JoinColumn(name="conv_id",referencedColumnName="id")
     */
    protected $conv_id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="emp_id",referencedColumnName="id")
     */
    protected $emp_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $msg_text;

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
        $this->conv_id = isset($data['conv_id']) ? $data['conv_id'] : null;
        $this->emp_id = isset($data['emp_id']) ? $data['emp_id'] : null;
        $this->msg_text = isset($data['msg_text']) ? $data['msg_text'] : '';
    }

    /**
     * @return mixed
     */
    public function getConvId()
    {
        return $this->conv_id;
    }

    /**
     * @param mixed $conv_id
     */
    public function setConvId($conv_id): void
    {
        $this->conv_id = $conv_id;
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
    public function getMsgText()
    {
        return $this->msg_text;
    }

    /**
     * @param mixed $msg_text
     */
    public function setMsgText($msg_text): void
    {
        $this->msg_text = $msg_text;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


}
 
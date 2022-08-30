<?php

namespace App\Entities;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="approved_project_comments")
 */
class ApprovedProjectComments
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="ApprovedProjectConv")
     * @ORM\JoinColumn(name="conv_id",referencedColumnName="id")
     */
    protected $conv_id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="u_id",referencedColumnName="id")
     */
    protected $u_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $msg_text;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    public function __construct($data)
    {
        $this->conv_id = isset($data['conv_id']) ? $data['conv_id'] : NULL;
        $this->u_id = isset($data['u_id']) ? $data['u_id'] : NULL;
        $this->msg_text = isset($data['msg_text']) ? $data['msg_text'] : '';
        $this->created_at = new \DateTime();
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
    public function getConvId()
    {
        return $this->conv_id;
    }

    /**
     * @param mixed $project_id
     */
    public function setConvId($conv_id): void
    {
        $this->conv_id = $conv_id;
    }

    /**
     * @return mixed
     */
    public function getUId()
    {
        return $this->u_id;
    }

    /**
     * @param mixed $u_id
     */
    public function setUId($u_id): void
    {
        $this->u_id = $u_id;
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


}

?>
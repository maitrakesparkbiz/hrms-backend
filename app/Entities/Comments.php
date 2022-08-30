<?php


namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="comments")
 */

class Comments{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User",inversedBy="Comments")
     * @ORM\JoinColumn(name="emp_id",referencedColumnName="id")
     */
    protected $emp_id;

    /**
     * @ORM\ManyToOne(targetEntity="CheckIn", inversedBy="Comments")
     * @ORM\JoinColumn(name="check_in_id", referencedColumnName="id")
     */
    protected $check_in_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $comment_text;

    /**
     * @ORM\Column(type="string")
     */
    protected $response_text;

    /**
     * @var \DateTime $created
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @var \DateTime $updated
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
        $this->emp_id = isset($data['emp_id']) ? $data['emp_id'] : NULL;
        $this->check_in_id = isset($data['check_in_id']) ? $data['check_in_id'] : NULL;
        $this->comment_text = isset($data['comment_text']) ? $data['comment_text'] : '';
        $this->response_text = isset($data['response_text']) ? $data['response_text'] : '';
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
    public function getCheckInId()
    {
        return $this->check_in_id;
    }

    /**
     * @param mixed $check_in_id
     */
    public function setCheckInId($check_in_id): void
    {
        $this->check_in_id = $check_in_id;
    }

    /**
     * @return mixed
     */
    public function getCommentText()
    {
        return $this->comment_text;
    }

    /**
     * @param mixed $comment_text
     */
    public function setCommentText($comment_text): void
    {
        $this->comment_text = $comment_text;
    }

    /**
     * @return mixed
     */
    public function getResponseText()
    {
        return $this->response_text;
    }

    /**
     * @param mixed $response_text
     */
    public function setResponseText($response_text): void
    {
        $this->response_text = $response_text;
    }



}


?>
<?php

namespace App\Entities;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="news_employee")
 */
class News_employee
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="News")
     * @ORM\JoinColumn(name="news_id", referencedColumnName="id")
     */
    protected $news_id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="emp_id", referencedColumnName="id")
     */
    protected $emp_id;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    protected $is_read;


    public function __construct($data)
    {
        $this->news_id = isset($data['news_id']) ? $data['news_id'] : NULL;
        $this->emp_id = isset($data['emp_id']) ? $data['emp_id'] : NULL;
        $this->is_read = isset($data["is_read"]) ? $data["is_read"] : 0;

    }

    function getId()
    {
        return $this->id;

    }

    public function getNewsId()
    {
        return $this->news_id;
    }

    public function setNewsId($news_id)
    {
        $this->news_id = $news_id;
    }


    public function getEmpId()
    {
        return $this->emp_id;
    }


    public function setEmpId($emp_id)
    {
        $this->emp_id = $emp_id;
    }

    /**
     * @return mixed
     */
    public function getisRead()
    {
        return $this->is_read;
    }

    /**
     * @param mixed $is_read
     */
    public function setIsRead($is_read): void
    {
        $this->is_read = $is_read;
    }

}
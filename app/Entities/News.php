<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;
/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="news")
 */
class News
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;


    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $status;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $publish_date;


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

    /**
     * @ORM\OneToMany(targetEntity="News_employee",mappedBy="news_id")
     */
    protected $news_employee;

    public function __construct($data)
    {
        $this->title = isset($data["title"]) ? $data["title"] : "";
        $this->status = isset($data["status"]) ? $data["status"] : "";
        $this->description = isset($data["description"]) ? $data["description"] : "";
        $this->publish_date = isset($data["publish_date"]) ? $data["publish_date"] : null;

    }

    function getId()
    {
        return $this->id;
    }

    function getStatus()
    {
        return $this->status;
    }

    public function getPublishDate()
    {
        return $this->publish_date;
    }


    public function setPublishDate($publish_date)
    {
        $this->publish_date = $publish_date;
    }



    function getDescription()
    {
        return $this->description;
    }


    public function getTitle()
    {
        return $this->title;
    }


    public function setTitle($title)
    {
        $this->title = $title;
    }



    function setStatus($status)
    {
        $this->status = $status;
    }

    function setDescription($description)
    {
        $this->description = $description;
    }


}
<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;
/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="appreciation")
 */
class Appreciation
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Award",inversedBy="Appreciation")
     * @ORM\JOinColumn(name="award_id",referencedColumnName="id")
     */
    protected $award_id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User",inversedBy="Appreciation")
     * @ORM\JOinColumn(name="emp_id",referencedColumnName="id")
     */
    protected $emp_id;


    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $image;

    /**
     * @ORM\Column(type="date",nullable=true)
     */
    protected $date;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $prize;



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
        $this->award_id = isset($data["award_id"]) ? $data["award_id"] : NULL;
        $this->emp_id = isset($data["emp_id"]) ? $data["emp_id"] : NULL;
        $this->image = isset($data["image"]) ? $data["image"] : "";
        $this->prize = isset($data["prize"]) ? $data["prize"] : "";
        $this->date = isset($data["date"]) ? $data["date"] : "";

    }

    public function getId()
    {
        return $this->id;
    }


    public function getAwardId()
    {
        return $this->award_id;
    }

    public function setAwardId($award_id)
    {
        $this->award_id = $award_id;
    }

    public function getEmpId()
    {
        return $this->emp_id;
    }


    public function setEmpId($emp_id)
    {
        $this->emp_id = $emp_id;
    }


    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }


    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }


    public function getPrize()
    {
        return $this->prize;
    }

    public function setPrize($prize)
    {
        $this->prize = $prize;
    }


}
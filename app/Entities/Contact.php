<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;
/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="contact")
 */
class Contact
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
    protected $name;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $email;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $number;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $service;


    /**
     *
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
        $this->name = isset($data["name"]) ? $data["name"] : "";
        $this->email = isset($data["email"]) ? $data["email"] : "";
        $this->number = isset($data["number"]) ? $data["number"] : "";
        $this->service = isset($data["service"]) ? $data["service"] : "";
        $this->description = isset($data["description"]) ? $data["description"] : "";

    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }


    public function setName($name)
    {
        $this->name = $name;
    }


    public function getEmail()
    {
        return $this->email;
    }


    public function setEmail($email)
    {
        $this->email = $email;
    }


    public function getNumber()
    {
        return $this->number;
    }

    public function setNumber($number)
    {
        $this->number = $number;
    }

    public function getService()
    {
        return $this->service;
    }


    public function setService($service)
    {
        $this->service = $service;
    }


    public function getDescription()
    {
        return $this->description;
    }


    public function setDescription($description)
    {
        $this->description = $description;
    }




}
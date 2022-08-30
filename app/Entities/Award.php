<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="award")
 */
class Award
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User",inversedBy="Award")
     * @ORM\JOinColumn(name="user",referencedColumnName="id")
     */
    protected $user;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Option_master",inversedBy="Award")
     * @ORM\JOinColumn(name="status",referencedColumnName="id")
     */
    protected $status;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\OneToMany(targetEntity="Appreciation", mappedBy="award_id")
     */
    protected $assigned_awards;

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
        $this->user = isset($data["user"]) ? $data["user"] : NULL;
        $this->name = isset($data["name"]) ? $data["name"] : "";
        $this->status = isset($data["status"]) ? $data["status"] : NULL;
        $this->description = isset($data["description"]) ? $data["description"] : "";
    }

    function getId()
    {
        return $this->id;
    }

    function getUser()
    {
        return $this->user;
    }

    function getName()
    {
        return $this->name;
    }

    function getStatus()
    {
        return $this->status;
    }

    function getDescription()
    {
        return $this->description;
    }


    function setUser($user)
    {
        $this->user = $user;
    }

    function setName($name)
    {
        $this->name = $name;
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
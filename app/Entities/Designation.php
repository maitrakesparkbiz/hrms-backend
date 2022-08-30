<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="designation")
 */
class Designation
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Department")
     * @ORM\JoinColumn(name="dep_id", referencedColumnName="id")
     */
    protected $dep_id;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="designation")
     */
    protected $employees;

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
        $this->dep_id = isset($data["dep_id"]) ? $data["dep_id"] : null;
    }

    /**
     * @return mixed
     */
    public function getDepId()
    {
        return $this->dep_id;
    }

    /**
     * @param mixed $dep_id
     */
    public function setDepId($dep_id): void
    {
        $this->dep_id = $dep_id;
    }

    function getId()
    {
        return $this->id;
    }

    function getName()
    {
        return $this->name;
    }


    function setName($name)
    {
        $this->name = $name;
    }


}
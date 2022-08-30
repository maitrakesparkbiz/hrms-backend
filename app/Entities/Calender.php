<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="calender")
 */
class Calender
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;


    /**
     *
     * @ORM\ManyToOne(targetEntity="Option_master",inversedBy="Calender")
     * @ORM\JOinColumn(name="calendermonth",referencedColumnName="id")
     */
    protected $calendermonth;

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
        $this->calendermonth = isset($data["calendermonth"]) ? $data["calendermonth"] : NULL;


    }

    function getId()
    {
        return $this->id;
    }


    public function getCalendermonth()
    {
        return $this->calendermonth;
    }

    public function setCalendermonth($calendermonth)
    {
        $this->calendermonth = $calendermonth;
    }


}
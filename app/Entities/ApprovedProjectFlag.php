<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="approved_project_flag")
 */
class ApprovedProjectFlag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="ApprovedProject")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    protected $project_id;

    /**
     * @ORM\ManyToOne(targetEntity="Option_master")
     * @ORM\JoinColumn(name="flag_sales", referencedColumnName="id")
     */
    protected $flag_sales;

    /**
     * @ORM\ManyToOne(targetEntity="Option_master")
     * @ORM\JoinColumn(name="flag_ba", referencedColumnName="id")
     */
    protected $flag_ba;

    /**
     * @ORM\ManyToOne(targetEntity="Option_master")
     * @ORM\JoinColumn(name="flag_jr_ba", referencedColumnName="id")
     */
    protected $flag_jr_ba;

    /**
     * @ORM\ManyToOne(targetEntity="Option_master")
     * @ORM\JoinColumn(name="flag_ba_to_jr", referencedColumnName="id")
     */
    protected $flag_ba_to_jr;


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
        $this->project_id = isset($data['project_id']) ? $data['project_id'] : NULL;
        $this->flag_sales = isset($data['flag_sales']) ? $data['flag_sales'] : NULL;
        $this->flag_ba = isset($data['flag_ba']) ? $data['flag_ba'] : NULL;
        $this->flag_jr_ba = isset($data['flag_jr_ba']) ? $data['flag_jr_ba'] : NULL;
        $this->flag_ba_to_jr = isset($data['flag_ba_to_jr']) ? $data['flag_ba_to_jr'] : NULL;
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
    public function getProjectId()
    {
        return $this->project_id;
    }

    /**
     * @param mixed $project_id
     */
    public function setProjectId($project_id): void
    {
        $this->project_id = $project_id;
    }

    /**
     * @return mixed
     */
    public function getFlagSales()
    {
        return $this->flag_sales;
    }

    /**
     * @param mixed $flag_sales
     */
    public function setFlagSales($flag_sales): void
    {
        $this->flag_sales = $flag_sales;
    }

    /**
     * @return mixed
     */
    public function getFlagBa()
    {
        return $this->flag_ba;
    }

    /**
     * @param mixed $flag_ba
     */
    public function setFlagBa($flag_ba): void
    {
        $this->flag_ba = $flag_ba;
    }

    /**
     * @return mixed
     */
    public function getFlagJrBa()
    {
        return $this->flag_jr_ba;
    }

    /**
     * @param mixed $flag_jr_ba
     */
    public function setFlagJrBa($flag_jr_ba): void
    {
        $this->flag_jr_ba = $flag_jr_ba;
    }

    /**
     * @return mixed
     */
    public function getFlagBaToJr()
    {
        return $this->flag_ba_to_jr;
    }

    /**
     * @param mixed $flag_ba_to_jr
     */
    public function setFlagBaToJr($flag_ba_to_jr): void
    {
        $this->flag_ba_to_jr = $flag_ba_to_jr;
    }


}

?>
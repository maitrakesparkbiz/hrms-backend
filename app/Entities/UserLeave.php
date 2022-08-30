<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="user_leave")
 */
class UserLeave
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="user")
     * @ORM\JoinColumn(name="emp_id", referencedColumnName="id")
     */
    protected $emp_id;

    /**
     * @ORM\Column(type="decimal",precision=10, scale=2)
     */
    protected $cl;

    /**
     * @ORM\Column(type="decimal",precision=10, scale=2)
     */
    protected $pl;

    /**
     * @ORM\Column(type="decimal",precision=10, scale=2)
     */
    protected $sl;

    /**
     * @ORM\Column(type="decimal",precision=10, scale=2)
     */
    protected $used_upl;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $employment_started;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $one_year_completed;

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

    function __construct($data)
    {
        $this->emp_id = isset($data['emp_id'])?$data['emp_id']:NULL;
        $this->cl = isset($data['cl'])?$data['cl']:0;
        $this->pl= isset($data['pl'])?$data['pl']:0;
        $this->sl = isset($data['sl'])?$data['sl']:0;
        $this->used_upl = isset($data['used_upl'])?$data['used_upl']:0;
        $this->employment_started = isset($data['employment_started'])?$data['employment_started']:0;
        $this->one_year_completed = isset($data['one_year_completed'])?$data['one_year_completed']:0;
    }

    /**
     * @return mixed
     */
    public function getUsedUpl()
    {
        return $this->used_upl;
    }

    /**
     * @param mixed $used_upl
     */
    public function setUsedUpl($used_upl): void
    {
        $this->used_upl = $used_upl;
    }


    /**
     * @return mixed
     */
    public function getEmploymentStarted()
    {
        return $this->employment_started;
    }

    /**
     * @param mixed $employment_started
     */
    public function setEmploymentStarted($employment_started): void
    {
        $this->employment_started = $employment_started;
    }

    /**
     * @return mixed
     */
    public function getOneYearCompleted()
    {
        return $this->one_year_completed;
    }

    /**
     * @param mixed $one_year_completed
     */
    public function setOneYearCompleted($one_year_completed): void
    {
        $this->one_year_completed = $one_year_completed;
    }




    /**
     * @return mixed
     */
    public function getEmpId()
    {
        return $this->emp_id;
    }

    /**
     * @return mixed
     */
    public function getCl()
    {
        return $this->cl;
    }

    /**
     * @param mixed $cl
     */
    public function setCl($cl): void
    {
        $this->cl = $cl;
    }

    /**
     * @return mixed
     */
    public function getPl()
    {
        return $this->pl;
    }

    /**
     * @param mixed $pl
     */
    public function setPl($pl): void
    {
        $this->pl = $pl;
    }

    /**
     * @return mixed
     */
    public function getSl()
    {
        return $this->sl;
    }

    /**
     * @param mixed $sl
     */
    public function setSl($sl): void
    {
        $this->sl = $sl;
    }


}


?>
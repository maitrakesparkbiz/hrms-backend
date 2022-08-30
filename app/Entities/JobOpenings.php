<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="job_openings")
 */
class JobOpenings
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $role;

    /**
     * @ORM\Column(type="string")
     */
    protected $exp_required;

    /**
     * @ORM\Column(type="string")
     */
    protected $ctc;

    /**
     * @ORM\Column(type="string")
     */
    protected $vacancies;

    /**
     * @ORM\Column(type="text")
     */
    protected $introduction;

    /**
     * @ORM\Column(type="text")
     */
    protected $responsibilities;

    /**
     * @ORM\Column(type="text")
     */
    protected $skill_set;

    /**
     * @ORM\Column(type="date")
     */
    protected $last_date;

    /**
     * @ORM\Column(type="date")
     */
    protected $posted_date;

    /**
     * @ORM\Column(type="string")
     */
    protected $posted_as;

    /**
     * @ORM\Column(type="string")
     */
    protected $status;

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
        $this->role = isset($data["role"]) ? $data["role"] : "";
        $this->exp_required = isset($data["exp_required"]) ? $data["exp_required"] : "";
        $this->ctc = isset($data["ctc"]) ? $data["ctc"] : "";
        $this->vacancies = isset($data["vacancies"]) ? $data["vacancies"] : "";
        $this->introduction = isset($data["introduction"]) ? $data["introduction"] : "";
        $this->responsibilities = isset($data["responsibilities"]) ? $data["responsibilities"] : "";
        $this->skill_set = isset($data["skill_set"]) ? $data["skill_set"] : "";
        $this->last_date = isset($data["last_date"]) ? $data["last_date"] : "";
        $this->posted_date = isset($data["posted_date"]) ? $data["posted_date"] : "";
        $this->posted_as = isset($data["posted_as"]) ? $data["posted_as"] : "Both";
        $this->status = isset($data["status"]) ? $data["status"] : "Open";
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
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role): void
    {
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getExpRequired()
    {
        return $this->exp_required;
    }

    /**
     * @param mixed $exp_required
     */
    public function setExpRequired($exp_required): void
    {
        $this->exp_required = $exp_required;
    }

    /**
     * @return mixed
     */
    public function getCtc()
    {
        return $this->ctc;
    }

    /**
     * @param mixed $ctc
     */
    public function setCtc($ctc): void
    {
        $this->ctc = $ctc;
    }

    /**
     * @return mixed
     */
    public function getVacancies()
    {
        return $this->vacancies;
    }

    /**
     * @param mixed $vacancies
     */
    public function setVacancies($vacancies): void
    {
        $this->vacancies = $vacancies;
    }

    /**
     * @return mixed
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }

    /**
     * @param mixed $introduction
     */
    public function setIntroduction($introduction): void
    {
        $this->introduction = $introduction;
    }

    /**
     * @return mixed
     */
    public function getResponsibilities()
    {
        return $this->responsibilities;
    }

    /**
     * @param mixed $responsibilities
     */
    public function setResponsibilities($responsibilities): void
    {
        $this->responsibilities = $responsibilities;
    }

    /**
     * @return mixed
     */
    public function getSkillSet()
    {
        return $this->skill_set;
    }

    /**
     * @param mixed $skill_set
     */
    public function setSkillSet($skill_set): void
    {
        $this->skill_set = $skill_set;
    }

    /**
     * @return mixed
     */
    public function getLastDate()
    {
        return $this->last_date;
    }

    /**
     * @param mixed $last_date
     */
    public function setLastDate($last_date): void
    {
        $this->last_date = $last_date;
    }

    /**
     * @return mixed
     */
    public function getPostedDate()
    {
        return $this->posted_date;
    }

    /**
     * @param mixed $posted_date
     */
    public function setPostedDate($posted_date): void
    {
        $this->posted_date = $posted_date;
    }

    /**
     * @return mixed
     */
    public function getPostedAs()
    {
        return $this->posted_as;
    }

    /**
     * @param mixed $posted_as
     */
    public function setPostedAs($posted_as): void
    {
        $this->posted_as = $posted_as;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }
}
 
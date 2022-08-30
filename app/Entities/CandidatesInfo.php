<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="candidates_info")
 */
class CandidatesInfo
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
    protected $candidate_name;

    /**
     * @ORM\ManyToOne(targetEntity="JobOpenings", inversedBy="JobApplications")
     * @ORM\JoinColumn(name="category",referencedColumnName="id")
     */
    protected $category;

    /**
     * @ORM\Column(type="string")
     */
    protected $contact_email;

    /**
     * @ORM\Column(type="string")
     */
    protected $phone_number;

    /**
     * @ORM\Column(type="string")
     */
    protected $experiance;

    /**
     * @ORM\Column(type="string")
     */
    protected $current_company;

    /**
     * @ORM\Column(type="string")
     */
    protected $current_ctc;

    /**
     * @ORM\Column(type="string")
     */
    protected $expected_ctc;

    /**
     * @ORM\Column(type="string")
     */
    protected $resume;


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
        $this->candidate_name = isset($data['candidate_name']) ? $data['candidate_name'] : '';
        $this->category = isset($data['category']) ? $data['category'] : NULL;
        $this->contact_email = isset($data['contact_email']) ? $data['contact_email'] : '';
        $this->phone_number = isset($data['phone_number']) ? $data['phone_number'] : '';
        $this->current_company = isset($data['current_company']) ? $data['current_company'] : '';
        $this->current_ctc = isset($data['current_ctc']) ? $data['current_ctc'] : '';
        $this->expected_ctc = isset($data['expected_ctc']) ? $data['expected_ctc'] : '';
        $this->experiance = isset($data['experiance']) ? $data['experiance'] : '';
        $this->resume = isset($data['resume']) ? $data['resume'] : '';
    }

    /**
     * @return mixed
     */
    public function getCandidateName()
    {
        return $this->candidate_name;
    }

    /**
     * @param mixed $candidate_name
     */
    public function setCandidateName($candidate_name): void
    {
        $this->candidate_name = $candidate_name;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category): void
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getContactEmail()
    {
        return $this->contact_email;
    }

    /**
     * @param mixed $contact_email
     */
    public function setContactEmail($contact_email): void
    {
        $this->contact_email = $contact_email;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phone_number;
    }

    /**
     * @param mixed $phone_number
     */
    public function setPhoneNumber($phone_number): void
    {
        $this->phone_number = $phone_number;
    }

    /**
     * @return mixed
     */
    public function getExperiance()
    {
        return $this->experiance;
    }

    /**
     * @param mixed $experiance
     */
    public function setExperiance($experiance): void
    {
        $this->experiance = $experiance;
    }

    /**
     * @return mixed
     */
    public function getCurrentCompany()
    {
        return $this->current_company;
    }

    /**
     * @param mixed $current_company
     */
    public function setCurrentCompany($current_company): void
    {
        $this->current_company = $current_company;
    }

    /**
     * @return mixed
     */
    public function getCurrentCtc()
    {
        return $this->current_ctc;
    }

    /**
     * @param mixed $current_ctc
     */
    public function setCurrentCtc($current_ctc): void
    {
        $this->current_ctc = $current_ctc;
    }

    /**
     * @return mixed
     */
    public function getExpectedCtc()
    {
        return $this->expected_ctc;
    }

    /**
     * @param mixed $expected_ctc
     */
    public function setExpectedCtc($expected_ctc): void
    {
        $this->expected_ctc = $expected_ctc;
    }

    /**
     * @return mixed
     */
    public function getResume()
    {
        return $this->resume;
    }

    /**
     * @param mixed $resume
     */
    public function setResume($resume): void
    {
        $this->resume = $resume;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }




}
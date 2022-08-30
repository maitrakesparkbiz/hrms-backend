<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="company_project_conv")
 */
class CompanyProjectConv
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="CompanyProject")
     * @ORM\JoinColumn(name="company_project_id", referencedColumnName="id")
     */
    protected $company_project_id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user1", referencedColumnName="id")
     */
    protected $user1;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user2", referencedColumnName="id")
     */
    protected $user2;

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
        $this->company_project_id = isset($data['company_project_id']) ? $data['company_project_id'] : null;
        $this->user1 = isset($data['user1']) ? $data['user1'] : null;
        $this->user2 = isset($data['user2']) ? $data['user2'] : NULL;
    }

    /**
     * @return mixed
     */
    public function getCompanyProjectId()
    {
        return $this->company_project_id;
    }

    /**
     * @param mixed $company_project_id
     */
    public function setCompanyProjectId($company_project_id): void
    {
        $this->company_project_id = $company_project_id;
    }

    /**
     * @return mixed
     */
    public function getUser1()
    {
        return $this->user1;
    }

    /**
     * @param mixed $user1
     */
    public function setUser1($user1): void
    {
        $this->user1 = $user1;
    }

    /**
     * @return mixed
     */
    public function getUser2()
    {
        return $this->user2;
    }

    /**
     * @param mixed $user2
     */
    public function setUser2($user2): void
    {
        $this->user2 = $user2;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    
}
 
<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="company_policy")
 */

class CompanyPolicy
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
    protected $title;

    /**
     * @ORM\Column(type="text")
     */
    protected $short_description;

    /**
     * @ORM\Column(type="text")
     */
    protected $long_description;

    /**
     * @ORM\Column(type="string")
     */
    protected $policy_doc;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $is_published;

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

        $this->title = isset($data['title']) ? $data['title'] : '';
        $this->short_description = isset($data['short_description']) ? $data['short_description'] : '';
        $this->long_description = isset($data['long_description']) ? $data['long_description'] : '';
        $this->policy_doc = isset($data['policy_doc']) ? $data['policy_doc'] : '';
        $this->is_published = isset($data['is_published']) ? $data['is_published'] : 0;

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
    public function getisPublished()
    {
        return $this->is_published;
    }

    /**
     * @param mixed $is_published
     */
    public function setIsPublished($is_published): void
    {
        $this->is_published = $is_published;
    }



    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getShortDescription()
    {
        return $this->short_description;
    }

    /**
     * @param mixed $short_description
     */
    public function setShortDescription($short_description): void
    {
        $this->short_description = $short_description;
    }

    /**
     * @return mixed
     */
    public function getLongDescription()
    {
        return $this->long_description;
    }

    /**
     * @param mixed $long_description
     */
    public function setLongDescription($long_description): void
    {
        $this->long_description = $long_description;
    }

    /**
     * @return mixed
     */
    public function getPolicyDoc()
    {
        return $this->policy_doc;
    }

    /**
     * @param mixed $policy_doc
     */
    public function setPolicyDoc($policy_doc): void
    {
        $this->policy_doc = $policy_doc;
    }


}

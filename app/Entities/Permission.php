<?php


namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use LaravelDoctrine\ACL\Contracts\Permission as PermissionContract;

/**
 * @ORM\Entity()
 * @ORM\Table(name="permission")
 */
class Permission implements PermissionContract
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="integer")
     */
    protected $cat_status;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Option_master",inversedBy="Permission")
     * @ORM\JOinColumn(name="category",referencedColumnName="id",nullable=true)
     */
    protected $category;

    public function __construct($data)
    {
        $this->name = isset($data['name']) ? $data['name'] : null;
        $this->category = isset($data['category']) ? $data['category'] : null;
        $this->cat_status = isset($data['cat_status']) ? $data['cat_status'] : null;
    }


    public function getName()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getCatStatus()
    {
        return $this->cat_status;
    }

    /**
     * @param mixed $cat_status
     */
    public function setCatStatus($cat_status): void
    {
        $this->cat_status = $cat_status;
    }


}
<?php

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use LaravelDoctrine\ACL\Contracts\Permission;
use LaravelDoctrine\ACL\Contracts\Role as RoleContract;
use LaravelDoctrine\ACL\Permissions\HasPermissions;
use LaravelDoctrine\ACL\Mappings as ACL;

/**
 * @ORM\Entity()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */

/**
 * @ORM\Entity()
 * @ORM\Table(name="role")
 */
class Role implements RoleContract
{

    use HasPermissions;
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
     * @ORM\Column(type="text")
     */
    protected $description;


    /**
     * @ACL\HasPermissions()
     */
    public $permissions;

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

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="roles")
     */
    protected $employees;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ArrayCollection|Permission[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function addPermission($permission)
    {
        if ($this->permissions->contains($permission)) {
            return;
        }else if($permission){
            return $this->permissions->add($permission);
        }else{
            return;
        }
    }

    public function removePermission($permission)
    {
        return $this->permissions->removeElement($permission);
    }

    public function grantPermission($permission)
    {
        return $this->permissions->add($permission);
    }

    public function revokePermission($permission)
    {
        return $this->permissions->removeElement($permission);
    }

    function getDescription()
    {
        return $this->description;
    }

    function setName($name)
    {
        $this->name = $name;
    }

    function setDescription($description)
    {
        $this->description = $description;
    }

    public function __construct($data = null)
    {
        if (!$data)
            return;
        $this->name = $data['name'];
        $this->description = $data['description'];
        $this->permissions = isset($data['permissions']) ? $data['permissions'] : null;
    }

}
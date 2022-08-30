<?php

namespace App\Repositories;


use App\Entities\Permission;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class PermissionRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\Permission'));
    }

    public function PermissionOfId($id)
    {
        return $this->_em->getRepository('App\Entities\Permission')->findOneBy([
            "id" => $id
        ]);
    }

    function getPermission($id)
    {
        return $this->find($id);
    }

    function getAllPermissions()
    {
        $query = $this->createQueryBuilder('p')
            ->select('p.id,p.name,p.title,p.cat_status,c.id as cat_id, c.key_text as cat_name')
            ->leftJoin('p.category', 'c')
            ->getQuery();
        return $query->getArrayResult();
    }

    function addNewPermission(Permission $permission)
    {
        $this->_em->persist($permission);
        $this->_em->flush($permission);
        return $permission;

    }

}
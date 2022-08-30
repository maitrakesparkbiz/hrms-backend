<?php

namespace App\Repositories;

use App\Entities\Role;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class RoleRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\Role'));
    }

    function grantPermission($role_id, $permission_id)
    {
        $role = $this->find($role_id);
        $role->grantPermission($this->_em->find('App\Entities\Permission', $permission_id));
        $this->_em->persist($role);
        $this->_em->flush($role);
    }

    function revokePermission($role_id, $permission_id)
    {
        $role = $this->find($role_id);
        $role->revokePermission($this->_em->find('App\Entities\Permission', $permission_id));
        $this->_em->persist($role);
        $this->_em->flush($role);
    }

    public function RoleById($id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('r,p')
            ->from('App\Entities\Role', 'r')
            ->leftJoin('r.permissions', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('r.id', ':id')
                )
            )
            ->setParameter('id', $id);
        $qb = $query->getQuery();
        return $qb->getArrayResult([0]);
    }

    public function RolePermissionById($id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('r.name as role_name, r.description,p.id,p.name,p.title,p.cat_status,c.id as cat_id, c.key_text as cat_name')
            ->from('App\Entities\Role', 'r')
            ->leftJoin('r.permissions', 'p')
            ->leftJoin('p.category', 'c')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('r.id', ':id')
                )
            )
            ->setParameter('id', $id);
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    function getRole($role_id)
    {
        return $this->find($role_id);
    }

    public function RoleOfId($id)
    {
        return $this->_em->getRepository('App\Entities\Role')->findOneBy([
            'id' => $id
        ]);
    }

    public function update(Role $role, $data)
    {
        if (isset($data["name"])) {
            $role->setName($data["name"]);
        }
        if (isset($data["description"])) {
            $role->setDescription($data["description"]);
        }

        $this->_em->persist($role);
        $this->_em->flush();
    }

    function getRoles()
    {
        $query = $this->createQueryBuilder('r')
            ->select('r,p')
            ->leftJoin('r.permissions', 'p')
            ->where('r.id != 1') // leaving super admin role in listing
            ->getQuery();
        return $query->getArrayResult();
    }
    function getFilterRecords($search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(r.id) as count')
            ->from("App\Entities\Role", "r")
            ->where('r.id != 1')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('r.name', ':filter'),
                    $query->expr()->like('r.description', ':filter')
                )
            )
            ->setParameters(
                ['filter' => '%' . $search . '%']
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    function countAllRole()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(r.id) as count')
            ->from("App\Entities\Role", "r")
            ->where('r.id != 1');
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    function getAllRolesDatatable($order, $col, $search, $start, $length)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('r.id', 'r.name', 'r.description', 'count(ru.id) as employees')
            ->from("App\Entities\Role", "r")
            ->leftJoin('r.employees', 'ru', 'with', 'ru.user_exit_status=:status')
            ->having(
                $query->expr()->orX(
                    $query->expr()->like('r.name', ':filter'),
                    $query->expr()->like('r.description', ':filter'),
                    $query->expr()->like('employees', ':filter')
                )
            )
            // ->where('r.id != 1')
            ->orderBy(($col == 'employees' ? 'employees' : 'r.' . $col), strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                ['filter' => '%' . $search . '%', 'status' => 1]
            )
            ->groupBy('r.id')
            ->getQuery();

        $qb = $query->getQuery();

        $data = $qb->getResult(Query::HYDRATE_ARRAY);
        return $data;
    }

    //    function tempAllRoles()
    //    {
    //        $query = $this->_em->createQueryBuilder()
    //            ->select('r.id,r.name,count(ru) as emps')
    //            ->from("App\Entities\Role", "r")
    //            ->leftJoin('r.employees', 'ru')
    //            ->groupBy('r.id')
    //            ->getQuery();
    //
    //        return $query->getArrayResult();
    //    }

    function getRolesWithoutEmployee()
    {
        $query = $this->createQueryBuilder('r')
            ->select('r,p')
            ->leftJoin('r.permissions', 'p')
            ->where('r.id! = 51') // leaving Employee role in listing
            ->getQuery();
        return $query->getArrayResult();
    }

    function getAllPermissionByRoleId($role_id)
    {
        $query = $this->createQueryBuilder('r')
            ->select('r,p')
            ->leftJoin('r.permissions', 'p')
            ->where('r.id=:id')
            ->setParameter('id', $role_id)
            ->getQuery();
        return $query->getArrayResult()[0]['permissions'];
    }


    public function prepareData($data)
    {
        return new Role($data);
    }


    public function create(Role $role)
    {

        $this->_em->persist($role);
        $this->_em->flush();
        return $role->getId();
    }

    function save($role)
    {
        $this->_em->persist($role);
        $this->_em->flush($role);
    }

    public function delete(Role $role)
    {
        $this->_em->remove($role);
        $this->_em->flush();
    }

    public function getEmployeeRoleId($emp)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('r.id')
            ->from('App\Entities\Role', 'r')
            ->where('LOWER(r.name) LIKE :emp')
            ->setParameter('emp', $emp)
            ->getQuery();

        return $query->getArrayResult();
    }
}

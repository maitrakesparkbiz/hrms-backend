<?php

namespace App\Repositories;

use App\Entities\Department;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class DepartmentRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\Department'));
    }

    public function create(Department $department)
    {
        $this->_em->persist($department);
        $this->_em->flush();

        return $department;
    }

    public function prepareData($data)
    {
        return new Department($data);
    }

    public function DepartmentOfId($id)
    {
        return $this->_em->getRepository('App\Entities\Department')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(Department $department, $data)
    {
        if (isset($data["name"])) {
            $department->setName($data["name"]);
        }

        $this->_em->persist($department);
        $this->_em->flush();

        return $department;
    }

    public function getAllDepartment()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("d", 'u')
            ->from("App\Entities\Department", "d")
            ->leftJoin('d.employees', 'u', 'with', 'u.user_exit_status=:status')
            ->setParameter('status', 1)
            ->getQuery();

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }
    public function getFilterRecored($search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(d.id) as count')
            ->from("App\Entities\Department", "d")
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('d.name', ':filter')
                )
            )
            ->setParameters(
                ['filter' => '%' . $search . '%']
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();


    }

    public function countAllDept()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(d.id) as count')
            ->from("App\Entities\Department", "d");
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function getAllDeptDatatable($order, $col, $search, $start, $length)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("d.id", 'd.name', 'count(u.id) as employees')
            ->from("App\Entities\Department", "d")
            ->leftJoin('d.employees', 'u', 'with', 'u.user_exit_status=:status')
            ->having(
                $query->expr()->orX(
                    $query->expr()->like('d.name', ':filter'),
                    $query->expr()->like('employees', ':filter')
                )
            )
            ->orderBy(($col == 'employees' ? 'employees' : 'd.name'), strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                ['filter' => '%' . $search . '%', 'status' => 1]
            )
            ->groupBy('d.id')
            ->getQuery();

        $qb = $query->getQuery();

        $data = $qb->getResult(Query::HYDRATE_ARRAY);
        return $data;
    }

    public function delete(Department $department)
    {
        $this->_em->remove($department);
        $this->_em->flush();
    }

    public function checkDept($name)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("d")
            ->from("App\Entities\Department", "d")
            ->where('d.name =: name')
            ->setParameter('name', $name);

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }
}

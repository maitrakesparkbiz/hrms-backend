<?php

namespace App\Repositories;

use App\Entities\Designation;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class DesignationRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\Designation'));
    }

    public function create(Designation $designation)
    {
        $this->_em->persist($designation);
        $this->_em->flush();

        return $designation;
    }

    public function prepareData($data)
    {
        return new Designation($data);
    }

    public function DesignationOfId($id)
    {
        return $this->_em->getRepository('App\Entities\Designation')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(Designation $designation, $data)
    {
        if (isset($data["name"])) {
            $designation->setName($data["name"]);
        }

        if (isset($data['dep_id'])) {
            $designation->setDepId($data["dep_id"]);
        }

        $this->_em->persist($designation);
        $this->_em->flush();

        return $designation;
    }

    public function getAllDesignation()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("d", 'dep.id as dep_id', 'dep.name as dep_name', 'u')
            ->from("App\Entities\Designation", "d")
            ->leftJoin('d.dep_id', 'dep')
            ->leftJoin('d.employees', 'u', 'with', 'u.user_exit_status=:status')
            ->setParameter('status', 1)
            ->getQuery();

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function getFilterRecords($search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(d.id) as count')
            ->from("App\Entities\Designation", "d")
            ->leftJoin('d.dep_id', 'dep')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('d.name', ':filter')
                    , $query->expr()->like('dep.name', ':filter')
                )
            )
            ->setParameters(
                ['filter' => '%' . $search . '%']
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countAllDes()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(d.id) as count')
            ->from("App\Entities\Designation", "d");
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function getAllDesDatatable($col, $order, $search, $start, $length)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("d.id", 'd.name', 'dep.id as dep_id', 'dep.name as dep_name', 'count(u.id) as employees')
            ->from("App\Entities\Designation", "d")
            ->leftJoin('d.dep_id', 'dep')
            ->leftJoin('d.employees', 'u', 'with', 'u.user_exit_status=:status')
            ->having(
                $query->expr()->orX(
                    $query->expr()->like('d.name', ':filter'),
                    $query->expr()->like('dep.name', ':filter'),
                    $query->expr()->like('employees', ':filter')
                )
            )
            ->setFirstResult($start)
            ->setMaxResults($length);
        $query->orderBy(($col == "name" ? 'd.' . $col : ($col == 'dep_name' ? 'dep.name' : ($col == 'employees' ? 'employees' : 'd.' . $col))), strtoupper($order));
        $query->setParameters(
            ['filter' => '%' . $search . '%', 'status' => 1]
        )

            ->groupBy('d.id')
            ->getQuery();

        $qb = $query->getQuery();

        $data = $qb->getResult(Query::HYDRATE_ARRAY);
        return $data;
    }

    public function delete(Designation $designation)
    {
        $this->_em->remove($designation);
        $this->_em->flush();
    }

    public function checkDept($name)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("d")
            ->from("App\Entities\Designation", "d")
            ->where('d.name =: name')
            ->setParameter('name', $name);

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }
}

<?php

namespace App\Repositories;

use App\Entities\Award;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class AwardRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\Award'));
    }

    public function prepareData($data)
    {
        return new Award($data);
    }

    public function create(Award $award)
    {
        $this->_em->persist($award);
        $this->_em->flush();

        return $award;
    }

    public function AwardOfId($id)
    {
        return $this->_em->getRepository('App\Entities\Award')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(Award $award, $data)
    {
        if (isset($data["user"])) {
            $award->setUser($data["user"]);
        }
        if (isset($data["name"])) {
            $award->setName($data["name"]);
        }
        if (isset($data["description"])) {
            $award->setDescription($data["description"]);
        }
        if (isset($data["status"])) {
            $award->setStatus($data["status"]);
        }

        $this->_em->persist($award);
        $this->_em->flush();

        return $award;
    }

    public function getAllAward()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("a,s")
            ->from("App\Entities\Award", "a")
            ->leftJoin("a.status", "s");

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }
    public function getFilterRecords($search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(a.id) as count')
            ->from('App\Entities\Award', 'a')
            ->leftJoin("a.status", "s")
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('a.name', ':filter')
                    , $query->expr()->like('s.value_text', ':filter')
                )
            )
            ->setParameters(
                ['filter' => '%' . $search . '%']
            );
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }


    public function countAllAwards()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(a.id) as count')
            ->from('App\Entities\Award', 'a');
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function getAllAwardDatatable($col, $order, $search, $start, $length)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("a.id,a.name,s.value_text,count(ap) as assigned_awards")
            ->from("App\Entities\Award", "a")
            ->leftJoin("a.status", "s")
            ->leftJoin('a.assigned_awards', 'ap')
            ->having(
                $query->expr()->orX(
                    $query->expr()->like('a.name', ':filter'),
                    $query->expr()->like('s.value_text', ':filter'),
                    $query->expr()->like('assigned_awards', ':filter')
                )
            );
        $query->orderBy(($col == "status" ? 's.value_text' : ($col == "assigned_awards" ? 'assigned_awards' : 'a.' . $col)), strtoupper($order));

        $query->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                ['filter' => '%' . $search . '%']
            )
            ->groupBy('a.id');

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function delete(Award $award)
    {
        $this->_em->remove($award);
        $this->_em->flush();
    }

    public function getAwardById($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select("a,s")
            ->from("App\Entities\Award", "a")
            ->leftJoin("a.status", "s")
            ->where("a.id = :id")
            ->setParameter("id", $id)
            ->getQuery();
        return $query->getArrayResult([0]);
    }
}

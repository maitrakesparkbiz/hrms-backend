<?php

namespace App\Repositories;

use App\Entities\Calender;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class CalenderRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\Calender'));
    }

    public function prepareData($data)
    {
        return new Calender($data);
    }

    public function create(Calender $award)
    {
        $this->_em->persist($award);
        $this->_em->flush();

        return $award;
    }

    public function AwardOfId($id)
    {
        return $this->_em->getRepository('App\Entities\Calender')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(Calender $award, $data)
    {
        if (isset($data["calendermonth"])) {
            $award->setCalendermonth($data["calendermonth"]);
        }
        $this->_em->persist($award);
        $this->_em->flush();

        return $award;
    }

    public function getCalenderByID($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select("c,ti")
            ->from("App\Entities\Calender", "c")
            ->leftJoin("c.calendermonth", "ti")
            ->where("c.id = :id")
            ->setParameter("id", $id)
            ->getQuery();
        return $query->getArrayResult([0]);
    }

    public function CalenderOfID($id)
    {
        return $this->_em->getRepository('App\Entities\Calender')->findOneBy([
            "id" => $id
        ]);
    }
}
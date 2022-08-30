<?php

namespace App\Repositories;

use App\Entities\Leave_type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class LeaveTypeRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\Leave_type'));
    }

    public function prepareData($data)
    {
        return new Leave_type($data);
    }

    public function create(Leave_type $award)
    {
        $this->_em->persist($award);
        $this->_em->flush();

        return $award;
    }

    public function LeaveTypeOfId($id)
    {
        return $this->_em->getRepository('App\Entities\Leave_type')->findOneBy([
            "id" => $id
        ]);
    }

    public function getLeaveTypeById($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select("l,g,s,ou")
            ->from("App\Entities\Leave_type", "l")
            ->leftJoin("l.gender", "g")
            ->leftJoin("l.status", "s")
            ->leftJoin("l.over_utilization", "ou")
            ->where("l.id = :id")
            ->setParameter("id", $id)
            ->getQuery();
        return $query->getArrayResult([0]);
    }

    public function getAllLeaveType()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("l,g,s,o")
            ->from("App\Entities\Leave_type", "l")
            ->leftJoin("l.gender", "g")
            ->leftJoin("l.status", "s")
            ->leftJoin("l.over_utilization", "o");

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function update(Leave_type $leave_type, $data)
    {
        if (isset($data["leavetype"])) {
            $leave_type->setLeavetype($data["leavetype"]);
        }
        if (isset($data["gender"])) {
            $leave_type->setGender($data["gender"]);
        }
        if (isset($data["status"])) {
            $leave_type->setStatus($data["status"]);
        }
        if (isset($data["count_type"])) {
            $leave_type->setCount_type($data["count_type"]);
        }
        if (isset($data["count"])) {
            $leave_type->setCount($data["count"]);
        }
        if (isset($data["max_leave_month"])) {
            $leave_type->setMax_leave_month($data["max_leave_month"]);
        }
        if (isset($data["max_consecutive_leave_month"])) {
            $leave_type->setMax_consecutive_leave_month($data["max_consecutive_leave_month"]);
        }
        if (isset($data["probation"])) {
            $leave_type->setProbation($data["probation"]);
        }
        if (isset($data["half_day"])) {
            $leave_type->setHalf_day($data["half_day"]);
        }
        if (isset($data["intervening_holiday"])) {
            $leave_type->setIntervening_holiday($data["intervening_holiday"]);
        }
        if (isset($data["over_utilization"])) {
            $leave_type->setOver_utilization($data["over_utilization"]);
        }
        if (isset($data["unused_leave"])) {
            $leave_type->setUnused_leave($data["unused_leave"]);
        }

        $this->_em->persist($leave_type);
        $this->_em->flush();

        return $leave_type;
    }

    public function delete(Leave_type $leave_type)
    {
        $this->_em->remove($leave_type);
        $this->_em->flush();
    }
}
<?php

namespace App\Repositories;

use App\Entities\Breaks;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;

class BreaksRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\Breaks'));
    }

    public function prepareData($data)
    {
        return new Breaks($data);
    }

    public function create(Breaks $breaks)
    {
        $this->_em->persist($breaks);
        $this->_em->flush();

        return $breaks;
    }

    public function BreaksOfId($id)
    {
        return $this->_em->getRepository('App\Entities\Breaks')->findOneBy([
            'id' => $id
        ]);
    }

    public function getBreakInData($emp_id, $startTime, $endTime)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('b')
            ->from('App\Entities\Breaks', 'b')
            ->leftJoin('b.emp_id', 'u')
            ->where('u.id=:emp_id')
            ->andWhere('b.break_in_time>=:startTime')
            ->andWhere('b.break_in_time<=:endTime')
            ->andWhere('b.break_out_time IS NULL')
            ->setParameters(['emp_id' => $emp_id, 'startTime' => $startTime, 'endTime' => $endTime])
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getBreakInDataInitial($emp_id, $startTime, $endTime)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('b')
            ->from('App\Entities\Breaks', 'b')
            ->leftJoin('b.emp_id', 'u')
            ->where('u.id=:emp_id')
            ->andWhere('b.break_in_time>=:startTime')
            ->andWhere('b.break_in_time<=:endTime')
            ->setParameters(['emp_id' => $emp_id, 'startTime' => $startTime, 'endTime' => $endTime])
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getBreaksToday($check_in_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('b')
            ->from('App\Entities\Breaks','b')
            ->leftJoin('b.check_in_id','c')
            ->where('c.id=:id')
            ->setParameter('id',$check_in_id)
            ->getQuery();

        return $query->getArrayResult();
    }

    public function update(Breaks $breaks, $data)
    {
        if (isset($data['emp_id'])) {
            $breaks->setEmpId($data['emp_id']);
        }
        if (isset($data['check_in_id'])) {
            $breaks->setCheckInId($data['check_in_id']);
        }
        if (isset($data['break_in_time'])) {
            $breaks->setBreakInTime($data['break_in_time']);
        }
        if (isset($data['break_out_time'])) {
            $breaks->setBreakOutTime($data['break_out_time']);
        }

        $this->_em->persist($breaks);
        $this->_em->flush();
        return $breaks;
    }

    public function delete(Breaks $breaks){
        $this->_em->remove($breaks);
        $this->_em->flush();
    }
}

?>
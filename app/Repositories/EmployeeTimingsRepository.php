<?php

namespace App\Repositories;

use App\Entities\EmployeeTimings;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class EmployeeTimingsRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\EmployeeTimings'));
    }

    public function create(EmployeeTimings $employeeTimings)
    {
        $this->_em->persist($employeeTimings);
        $this->_em->flush();

        return $employeeTimings;
    }

    public function prepareData($data)
    {
        return new EmployeeTimings($data);
    }

    public function delete(EmployeeTimings $employeeTimings)
    {
        $this->_em->remove($employeeTimings);
        $this->_em->flush();
    }

    public function EmpTimingsOfId($id)
    {
        return $this->_em->getRepository('App\Entities\EmployeeTimings')->findOneBy([
            "id" => $id
        ]);
    }

//    public function update(EmployeeTimings $employeeTimings, $data)
//    {
//        if (isset($data["project_id"])) {
//            $employeeTimings->setProjectId($data["project_id"]);
//        }
//        if (isset($data["emp_id"])) {
//            $employeeTimings->setProjectId($data["emp_id"]);
//        }
//
//
//        $this->_em->persist($employeeTimings);
//        $this->_em->flush();
//
//        return $employeeTimings;
//    }

    public function getEmpTimingRecordsByEmp($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('apt,p,cp')
            ->from('App\Entities\EmployeeTimings', 'apt')
            ->leftJoin('apt.emp_id', 'p')
            ->leftJoin('apt.project_id','cp')
            ->where('apt.emp_id=:id')
            ->setParameter('id', $emp_id)
            ->orderBy('apt.created_at', 'DESC')
            ->getQuery();
        return $query->getArrayResult();
    }
    public function getEmpTimingRecordsWithTotal($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('SUM(apt.record_hours) as total,MAX(apt.record_date) as max_date, MIN(apt.record_date) as min_date')
            ->from('App\Entities\EmployeeTimings', 'apt')
            ->where('apt.emp_id=:id')
            ->setParameter('id',$emp_id)
            ->getQuery();
        return $query->getArrayResult();
    }


}

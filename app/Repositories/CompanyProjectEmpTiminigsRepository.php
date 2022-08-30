<?php

namespace App\Repositories;

use App\Entities\CompanyProjectEmpTimings;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class CompanyProjectEmpTiminigsRepository extends EntityRepository
{

    private $class = 'App\Entities\CompanyProjectEmpTimings';

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\CompanyProjectEmpTimings'));
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');
    }

    public function create(CompanyProjectEmpTimings $approvedProjectEmpTimings)
    {
        $this->_em->persist($approvedProjectEmpTimings);
        $this->_em->flush();

        return $approvedProjectEmpTimings;
    }

    public function prepareData($data)
    {
        return new CompanyProjectEmpTimings($data);
    }

    public function ApprovedProjectEmpTimingOfId($id)
    {
        return $this->_em->getRepository('App\Entities\CompanyProjectEmpTimings')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(CompanyProjectEmpTimings $approvedProjectEmpTimings, $data)
    {
        if (isset($data["main_project_id"])) {
            $approvedProjectEmpTimings->setMainProjectId($data["main_project_id"]);
        }


        $this->_em->persist($approvedProjectEmpTimings);
        $this->_em->flush();

        return $approvedProjectEmpTimings;
    }

    public function selectRecordByProjectID($project_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('SUM(cp.record_hours) as sum','MAX(cp.record_date) as max_date',' MIN(cp.record_date) as min_date')
            ->from('App\Entities\CompanyProjectEmpTimings', 'cp')
            ->where('cp.company_project_id=:id')
            ->setParameter('id', $project_id)
            ->getQuery();

        return $query->getArrayResult()[0];
    }

    public function getAllEmpTimingRecords($project_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('apt,p,cb')
            ->from($this->class, 'apt')
            ->leftJoin('apt.emp_id', 'p')
            ->leftJoin('apt.created_by', 'cb')
            ->where('apt.company_project_id=:id')
            ->setParameter('id', $project_id)
            ->orderBy('apt.record_date', 'DESC')
            ->getQuery();
        return $query->getArrayResult();
    }
    public function getAllEmpTimingRecordsWithTotal($project_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('SUM(apt.record_hours) as total,MAX(apt.record_date) as max_date, MIN(apt.record_date) as min_date')
            ->from($this->class, 'apt')
            ->where('apt.company_project_id=:id')
            ->setParameter('id', $project_id)
            ->getQuery();
        return $query->getArrayResult();
    }
    public function getAllEmpTimingRecordsWithTotalEmpId($start,$end_date,$emp_id,$project_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('SUM(apt.record_hours) as total,MAX(apt.record_date) as max_date, MIN(apt.record_date) as min_date')
            ->from($this->class, 'apt')
            ->where('apt.emp_id=:id')
            ->andWhere('apt.record_date>=:start')
            ->andWhere('apt.record_date<=:end');
           if ($project_id) {
               $query->andWhere('apt.company_project_id=:project_id')
                   ->setParameters(['id' => $emp_id,'project_id'=>$project_id,'start'=>$start,'end'=>$end_date]);
           } else {
               $query ->setParameters(['id' => $emp_id,'start'=>$start,'end'=>$end_date]);
           }
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }
}

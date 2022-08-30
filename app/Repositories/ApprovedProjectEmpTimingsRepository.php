<?php

namespace App\Repositories;

use App\Entities\ApprovedProjectEmpTimings;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ApprovedProjectEmpTimingsRepository extends EntityRepository
{

    private $class = 'App\Entities\ApprovedProjectEmpTimings';

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\ApprovedProjectEmpTimings'));
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');
    }

    public function create(ApprovedProjectEmpTimings $approvedProjectEmpTimings)
    {
        $this->_em->persist($approvedProjectEmpTimings);
        $this->_em->flush();

        return $approvedProjectEmpTimings;
    }

    public function prepareData($data)
    {
        return new ApprovedProjectEmpTimings($data);
    }

    public function ApprovedProjectEmpTimingOfId($id)
    {
        return $this->_em->getRepository('App\Entities\ApprovedProjectEmpTimings')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(ApprovedProjectEmpTimings $approvedProjectEmpTimings, $data)
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
            ->select('SUM(ap.record_hours) as sum','MAX(ap.record_date) as max_date',' MIN(ap.record_date) as min_date')
            ->from('App\Entities\ApprovedProjectEmpTimings', 'ap')
            ->where('ap.project_id=:id')
            ->setParameter('id', $project_id)
            ->getQuery();

        return $query->getArrayResult()[0];
    }
}

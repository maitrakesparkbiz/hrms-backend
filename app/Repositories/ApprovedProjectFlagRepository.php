<?php

namespace App\Repositories;

use App\Entities\ApprovedProjectFlag;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ApprovedProjectFlagRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\ApprovedProjectFlag'));
    }

    public function create(ApprovedProjectFlag $approvedProjectFlag)
    {
        $this->_em->persist($approvedProjectFlag);
        $this->_em->flush();

        return $approvedProjectFlag->getId();
    }

    public function prepareData($data)
    {
        return new ApprovedProjectFlag($data);
    }

    public function ApprovedProjectFlagOfId($id)
    {
        return $this->_em->getRepository('App\Entities\ApprovedProjectFlag')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(ApprovedProjectFlag $approvedProjectFlag, $data)
    {
        if (isset($data["project_id"])) {
            $approvedProjectFlag->setProjectId($data["project_id"]);
        }
        if (isset($data["flag_sales"])) {
            $approvedProjectFlag->setFlagSales($data["flag_sales"]);
        }
        if (isset($data["flag_ba"])) {
            $approvedProjectFlag->setFlagBa($data["flag_ba"]);
        }
        if (isset($data["flag_jr_ba"])) {
            $approvedProjectFlag->setFlagJrBa($data["flag_jr_ba"]);
        }
        if (isset($data["flag_ba_to_jr"])) {
            $approvedProjectFlag->setFlagBaToJr($data["flag_ba_to_jr"]);
        }
        $this->_em->persist($approvedProjectFlag);
        $this->_em->flush();

        return $approvedProjectFlag;
    }

    public function getFlagId($project_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('af.id as flag_id')
            ->from('App\Entities\ApprovedProjectFlag', 'af')
            ->where('af.project_id=:proj_id')
            ->setParameter('proj_id', $project_id)
            ->getQuery();

        return $query->getArrayResult();
    }
}

?>
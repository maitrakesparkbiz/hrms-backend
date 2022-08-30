<?php

namespace App\Repositories;

use App\Entities\ProjectConversation;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ProjectConversationRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\ProjectConversation'));
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');
    }

    public function create(ProjectConversation $projectConversation)
    {
        $this->_em->persist($projectConversation);
        $this->_em->flush();

        return $projectConversation->getId();
    }

    public function prepareData($data)
    {
        return new ProjectConversation($data);
    }

    public function ConvOfId($id)
    {
        return $this->_em->getRepository('App\Entities\ProjectConversation')->findOneBy([
            "id" => $id
        ]);
    }

    public function delete(ProjectConversation $projectConversation)
    {
        $this->_em->remove($projectConversation);
        $this->_em->flush();
    }

    public function getSrConvId($proj_id, $proj_ba_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('pc.id')
            ->from('App\Entities\ProjectConversation', 'pc')
            ->where('pc.project_id=:proj_id')
            ->andWhere('pc.project_ba_id=:proj_ba_id')
            ->andWhere('pc.project_jr_ba_id is NULL')
            ->setParameters(['proj_id' => $proj_id, 'proj_ba_id' => $proj_ba_id])
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getJrConvId($proj_id, $proj_ba_id, $proj_jr_ba_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('pc.id')
            ->from('App\Entities\ProjectConversation', 'pc')
            ->where('pc.project_id=:proj_id')
            ->andWhere('pc.project_ba_id=:proj_ba_id')
            ->andWhere('pc.project_jr_ba_id=:proj_jr_ba_id')
            ->setParameters(['proj_id' => $proj_id, 'proj_ba_id' => $proj_ba_id, 'proj_jr_ba_id' => $proj_jr_ba_id])
            ->getQuery();

        return $query->getArrayResult();
    }

    public function update(ProjectConversation $projectConversation, $data)
    {
        if (isset($data["project_id"])) {
            $projectConversation->setProjectId($data["project_id"]);
        }
        if (isset($data["project_ba_id"])) {
            $projectConversation->setProjectBaId($data["project_ba_id"]);
        }
        if (isset($data["project_jr_ba_id"])) {
            $projectConversation->setProjectJrBaId($data["project_jr_ba_id"]);
        }

        $this->_em->persist($projectConversation);
        $this->_em->flush();

        return $projectConversation;
    }
}

?>
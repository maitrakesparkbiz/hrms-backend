<?php

namespace App\Repositories;

use App\Entities\JobStage;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class JobStageRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\JobStage'));
    }

    public function stageOfId($id)
    {
        return $this->_em->getRepository('App\Entities\JobStage')->findOneBy([
                'id' => $id
            ]
        );
    }
}
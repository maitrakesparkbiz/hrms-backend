<?php

namespace App\Repositories;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class OptionRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\Option_master'));
    }

    public function getAllOptionsBySelectId($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('o.id,s.id as select_id,o.key_text,o.value_text')
            ->from('App\Entities\Option_master', 'o')
            ->leftJoin('o.select_id', 's')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getArrayResult();
    }

    public function OptionOfId($id)
    {
        return $this->_em->getRepository('App\Entities\Option_master')->find($id);
    }

    public function get_all_permission_select_id($id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('o,pec')
            ->from('App\Entities\Option_master', 'o')
            ->leftJoin('o.select_id', 's')
            ->leftJoin('o.permission', 'pec')
            ->where('s.id=:id')
            ->setParameter('id', $id);
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }


}
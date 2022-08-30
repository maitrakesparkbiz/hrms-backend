<?php

namespace App\Repositories;

use App\Entities\CompanyProjectComments;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class CompanyProjectCommentsRepository extends EntityRepository
{
    public $class = 'App\Entities\CompanyProjectComments';
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata($this->class));
    }

    public function create(CompanyProjectComments $companyProjectComments)
    {
        $this->_em->persist($companyProjectComments);
        $this->_em->flush();

        return $companyProjectComments;
    }

    public function prepareData($data)
    {
        return new CompanyProjectComments($data);
    }

    public function CompanyProjectCommentsOfId($id)
    {
        return $this->_em->getRepository($this->class)->findOneBy([
            "id" => $id
        ]);
    }

    public function update(CompanyProjectComments $companyProjectComments, $data)
    {
        if (isset($data["conv_id"])) {
            $companyProjectComments->setConvId($data["conv_id"]);
        }
        if (isset($data["u_id"])) {
            $companyProjectComments->setEmpId($data["u_id"]);
        }
        if (isset($data["msg_text"])) {
            $companyProjectComments->setMsgText($data["msg_text"]);
        }
        $this->_em->persist($companyProjectComments);
        $this->_em->flush();

        return $companyProjectComments;
    }

    public function getProjectComments($conv_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('c.id,c.msg_text,u.id as emp_id')
            ->from('App\Entities\CompanyProjectComments', 'c')
            ->leftJoin('c.emp_id', 'u')
            ->where('c.conv_id=:c_id')
            ->setParameter('c_id', $conv_id)
            ->getQuery();

        return $query->getArrayResult();
    }
}
 
<?php

namespace App\Repositories;

use App\Entities\ApprovedProjectComments;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ApprovedProjectCommentsRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\ApprovedProjectComments'));
    }

    public function create(ApprovedProjectComments $approvedProjectComments)
    {
        $this->_em->persist($approvedProjectComments);
        $this->_em->flush();

        return $approvedProjectComments;
    }

    public function prepareData($data)
    {
        return new ApprovedProjectComments($data);
    }

    public function ApprovedProjectCommentsOfId($id)
    {
        return $this->_em->getRepository('App\Entities\ApprovedProjectComments')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(ApprovedProjectComments $approvedProjectComments, $data)
    {
        if (isset($data["conv_id"])) {
            $approvedProjectComments->setConvId($data["conv_id"]);
        }
        if (isset($data["u_id"])) {
            $approvedProjectComments->setUId($data["u_id"]);
        }
        if (isset($data["msg_text"])) {
            $approvedProjectComments->setMsgText($data["msg_text"]);
        }
        $this->_em->persist($approvedProjectComments);
        $this->_em->flush();

        return $approvedProjectComments;
    }

    public function getProjectComments($conv_id){
        $query = $this->_em->createQueryBuilder()
            ->select('c.id,c.msg_text,u.id as u_id')
            ->from('App\Entities\ApprovedProjectComments','c')
            ->leftJoin('c.u_id','u')
            ->where('c.conv_id=:c_id')
            ->setParameter('c_id',$conv_id)
            ->getQuery();

        return $query->getArrayResult();
    }
}

?>
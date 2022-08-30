<?php

namespace App\Repositories;

use App\Entities\ApprovedProjectConv;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ApprovedProjectConvRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\ApprovedProjectConv'));
    }

    public function create(ApprovedProjectConv $approvedProjectConv)
    {
        $this->_em->persist($approvedProjectConv);
        $this->_em->flush();

        return $approvedProjectConv->getId();
    }

    public function prepareData($data)
    {
        return new ApprovedProjectConv($data);
    }

    public function ApprovedProjectConvOfId($id)
    {
        return $this->_em->getRepository('App\Entities\ApprovedProjectConv')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(ApprovedProjectConv $approvedProjectConv, $data)
    {
        if (isset($data["project_id"])) {
            $approvedProjectConv->setProjectId($data["project_id"]);
        }
        if (isset($data["user1"])) {
            $approvedProjectConv->setUser1($data["user1"]);
        }
        if (isset($data["user2"])) {
            $approvedProjectConv->setUser2($data["user2"]);
        }
        $this->_em->persist($approvedProjectConv);
        $this->_em->flush();

        return $approvedProjectConv;
    }

    public function delete(ApprovedProjectConv $approvedProjectConv){
        $this->_em->remove($approvedProjectConv);
        $this->_em->flush();
    }

    public function getSrConvId($project_id, $user1, $user2)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('pc.id')
            ->from('App\Entities\ApprovedProjectConv','pc')
            ->where('pc.project_id=:proj_id')
            ->andWhere('pc.user1=:user1')
            ->andWhere('pc.user2=:user2')
            ->setParameters(['proj_id'=>$project_id,'user1'=>$user1,'user2'=>$user2])
            ->getQuery();

        return $query->getArrayResult();
    }
}

?>
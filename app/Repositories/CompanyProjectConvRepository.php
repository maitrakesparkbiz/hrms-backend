<?php

namespace App\Repositories;

use App\Entities\CompanyProjectConv;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class CompanyProjectConvRepository extends EntityRepository
{
    private $class = 'App\Entities\CompanyProjectConv';
    
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata( 'App\Entities\CompanyProjectConv'));
    }

    public function create(CompanyProjectConv $companyProjectConv)
    {
        $this->_em->persist($companyProjectConv);
        $this->_em->flush();

        return $companyProjectConv->getId();
    }

    public function prepareData($data)
    {
        return new CompanyProjectConv($data);
    }

    public function CompanyProjectConvOfId($id)
    {
        return $this->_em->getRepository($this->class)->findOneBy([
            "id" => $id
        ]);
    }

    public function update( CompanyProjectConv $companyProjectConv, $data)
    {
        if (isset($data["project_id"])) {
            $companyProjectConv->setCompanyProjectId($data["project_id"]);
        }
        if (isset($data["user1"])) {
            $companyProjectConv->setUser1($data["user1"]);
        }
        if (isset($data["user2"])) {
            $companyProjectConv->setUser2($data["user2"]);
        }
        $this->_em->persist( $companyProjectConv);
        $this->_em->flush();

        return $companyProjectConv;
    }

    public function delete(CompanyProjectConv $companyProjectConv)
    {
        $this->_em->remove($companyProjectConv);
        $this->_em->flush();
    }

    public function getConvId($proj_id,$user2,$isBa = false){
        $query = $this->_em->createQueryBuilder();
        $query->select('cv.id,u.id as emp_id,u.firstname,u.lastname')
            ->from($this->class,'cv');
            if($isBa){
                $query->leftJoin('cv.user1','u');
            }else{
                $query->leftJoin('cv.user2','u');
            }            
        $query->where('cv.company_project_id=:proj_id')
            ->andWhere('cv.user2=:user2')
            ->setParameters(['proj_id' => $proj_id, 'user2' => $user2]);
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }
}
 
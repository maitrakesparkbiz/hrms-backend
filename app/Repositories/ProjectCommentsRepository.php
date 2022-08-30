<?php

namespace App\Repositories;

use App\Entities\ProjectComments;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ProjectCommentsRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\ProjectComments'));
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');
    }

    public function create(ProjectComments $projectComments)
    {
        $this->_em->persist($projectComments);
        $this->_em->flush();

        return $projectComments;
    }

    public function prepareData($data)
    {
        return new ProjectComments($data);
    }

    public function ProjectOfId($id)
    {
        return $this->_em->getRepository('App\Entities\ProjectComments')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(ProjectComments $projectComments, $data)
    {
        if (isset($data["project_id"])) {
            $projectComments->setProjectId($data["project_id"]);
        }
        if (isset($data["u_id"])) {
            $projectComments->setUId($data["u_id"]);
        }
        if (isset($data["msg_text"])) {
            $projectComments->setMsgText($data["msg_text"]);
        }

        $this->_em->persist($projectComments);
        $this->_em->flush();

        return $projectComments;
    }

    public function getProjectComments($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('c.id,c.msg_text,u.id as u_id')
            ->from('App\Entities\ProjectComments','c')
            ->leftJoin('c.u_id','u')
            ->where('c.conv_id=:c_id')
            ->setParameter('c_id',$id)
            ->getQuery();

        return $query->getArrayResult();
    }
}

?>
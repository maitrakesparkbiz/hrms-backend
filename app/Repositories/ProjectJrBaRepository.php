<?php

namespace App\Repositories;

use App\Entities\ProjectJrBa;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ProjectJrBaRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\ProjectJrBa'));
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');
    }

    public function create(ProjectJrBa $projectJrBa)
    {
        $this->_em->persist($projectJrBa);
        $this->_em->flush();

        return $projectJrBa;
    }

    public function prepareData($data)
    {
        return new ProjectJrBa($data);
    }

    public function ProjectJrBaOfId($id)
    {
        return $this->_em->getRepository('App\Entities\ProjectJrBa')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(ProjectJrBa $projectJrBa, $data)
    {
        if (isset($data["project_id"])) {
            $projectJrBa->setProjectId($data["project_id"]);
        }
        if (isset($data["est_time"])) {
            $projectJrBa->setEstTime($data["est_time"]);
        }
        if (isset($data["flag"])) {
            $projectJrBa->setFlag($data["flag"]);
        }
        if (isset($data["emp_id"])) {
            $projectJrBa->setEmpId($data["emp_id"]);
        }
        if (isset($data["sr_to_jr_flag"])) {
            $projectJrBa->setSrToJrFlag($data["sr_to_jr_flag"]);
        }

        $this->_em->persist($projectJrBa);
        $this->_em->flush();

        return $projectJrBa;
    }
    //
    //    public function delete($project_id)
    //    {
    //        $this->_em->createQueryBuilder()
    //            ->delete()
    //            ->from('App\Entities\ProjectBa', 'pb')
    //            ->where('pb.project_id=:project_id')
    //            ->setParameter('project_id', $project_id)
    //            ->getQuery();
    //    }
    //
    //    public function checkUserProjectExists($project_id)
    //    {
    //        $query = $this->_em->createQueryBuilder()
    //            ->select('pb.id')
    //            ->from('App\Entities\ProjectBa', 'pb')
    //            ->where('pb.project_id=:p_id')
    //            ->setParameters(['p_id' => $project_id])
    //            ->getQuery();
    //
    //        return $query->getArrayResult();
    //    }
    //
public function getFilterRecords($search, $emp_id)
{
    $query = $this->_em->createQueryBuilder();

    $query->select('count(p.id) as count')
        ->from('App\Entities\Project', 'p')
        ->leftJoin('p.jr_ba', 'pjb')
        ->where(
            $query->expr()->orX(
                $query->expr()->like('p.project_name', ':filter')
                , $query->expr()->like('DATE(p.created_at)', ':filter')
                , $query->expr()->like('pjb.est_time', ':filter')
            ),
            $query->expr()->andX(
                $query->expr()->eq('pjb.emp_id', ':emp_id')
            )
        )
        ->setParameters(['filter' => '%' . $search . '%', 'emp_id' => $emp_id]);
    $qb = $query->getQuery();
    return $qb->getArrayResult();
}

    public function countAllProjectsBa($emp_id)
    {
        $query = $this->_em->createQueryBuilder();

        $query->select('count(p.id) as count')
            ->from('App\Entities\Project', 'p')
            ->leftJoin('p.jr_ba', 'pjb')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('pjb.emp_id', ':emp_id')
                )
            )
            ->setParameters(['emp_id' => $emp_id]);
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function getAllProjectsBaDataTable($order, $col, $search, $start, $length, $emp_id)
    {
        $query = $this->_em->createQueryBuilder();

        $query->select('p.id,
                        p.project_name,
                        p.project_doc,
                        p.project_description,
                        p.created_at,
                        pjb.id as project_jr_ba_id,
                        pjb.est_time,
                        pb.id as project_ba_id,
                        o.value_text,
                        o.key_text,
                        sr.id as sr_ba_id,
                        sr.firstname as sr_ba_fname,
                        sr.lastname as sr_ba_lname, 
                        sr.profile_image as sr_ba_profile')
            ->from('App\Entities\Project', 'p')
            ->leftJoin('p.ba', 'pb')
            ->leftJoin('p.jr_ba', 'pjb')
            ->leftJoin('p.assigned_to', 'sr')
            ->leftJoin('pjb.flag', 'o')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('p.project_name', ':filter'),
                    $query->expr()->like('DATE(p.created_at)', ':filter'),
                    $query->expr()->like('pjb.est_time', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->eq('pjb.emp_id', ':emp_id')
                )
            )
            ->orderBy(($col == 'est_time' ? 'pjb.' : 'p.') . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(['filter' => '%' . $search . '%', 'emp_id' => $emp_id]);
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }


    public function getJrBaProjectById($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('pb.id as id,pb.est_time,pba.id as ba_project_id,p.id as project_id,p.project_name,p.project_doc,p.project_description,p.created_at')
            ->from('App\Entities\ProjectJrBa', 'pb')
            ->leftJoin('pb.ba_project_id', 'pba')
            ->leftJoin('pb.project_id', 'p')
            ->where('pb.id=:id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getArrayResult()[0];
    }

    public function checkUserProjectExists($proj_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('pb.id', 'u.id as emp_id')
            ->from('App\Entities\ProjectJrBa', 'pb')
            ->leftJoin('pb.emp_id', 'u')
            ->where('pb.project_id=:proj_id')
            ->setParameter('proj_id', $proj_id)
            ->getQuery();

        return $query->getArrayResult();
    }

    public function checkJrBaExists($ba_proj_id, $user_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('pjb.id')
            ->from('App\Entities\ProjectJrBa', 'pjb')
            ->where('pjb.ba_project_id=:ba_proj_id')
            ->andWhere('pjb.emp_id=:user_id')
            ->setParameters(['ba_proj_id' => $ba_proj_id, 'user_id' => $user_id])
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getAllJrBaOfBaProject($ba_proj_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('pjb.id,u.id as emp_id')
            ->from('App\Entities\ProjectJrBa', 'pjb')
            ->leftJoin('pjb.emp_id', 'u')
            ->where('pjb.ba_project_id=:ba_proj_id')
            ->setParameter('ba_proj_id', $ba_proj_id)
            ->getQuery();

        return $query->getArrayResult();
    }

    public function delete(ProjectJrBa $projectJrBa)
    {
        $this->_em->remove($projectJrBa);
        $this->_em->flush();
    }

    public function getJrBaDataofProject($ba_project_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('u.firstname,u.lastname,u.profile_image,pjb.est_time,f.key_text,f.value_text')
            ->from('App\Entities\ProjectJrBa', 'pjb')
            ->leftJoin('pjb.emp_id', 'u')
            ->leftJoin('pjb.sr_to_jr_flag', 'f')
            ->where('pjb.ba_project_id=:id')
            ->setParameter('id', $ba_project_id)
            ->getQuery();

        return $query->getArrayResult();
    }
}

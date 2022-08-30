<?php

namespace App\Repositories;

use App\Entities\ProjectBa;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ProjectBaRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\ProjectBa'));
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');
    }

    public function create(ProjectBa $projectBa)
    {
        $this->_em->persist($projectBa);
        $this->_em->flush();

        return $projectBa->getId();
    }

    public function prepareData($data)
    {
        return new ProjectBa($data);
    }

    public function ProjectOfId($id)
    {
        return $this->_em->getRepository('App\Entities\ProjectBa')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(ProjectBa $projectBa, $data)
    {
        if (isset($data["project_id"])) {
            $projectBa->setProjectId($data["project_id"]);
        }
        if (isset($data["est_time"])) {
            $projectBa->setEstTime($data["est_time"]);
        }
        if (isset($data["flag"])) {
            $projectBa->setFlag($data["flag"]);
        }
        if (isset($data["jr_ba_flag"])) {
            $projectBa->setJrBaFlag($data["jr_ba_flag"]);
        }
        if (isset($data["emp_id"])) {
            $projectBa->setEmpId($data["emp_id"]);
        }
        if (array_key_exists("assigned_jr_ba", $data)) {
            $projectBa->setAssignedJrBa($data["assigned_jr_ba"]);
        }

        $this->_em->persist($projectBa);
        $this->_em->flush();

        return $projectBa;
    }
    public function getFilterRecords($search, $emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(p.id) as count')
            ->from("App\Entities\Project", "p")
            ->leftJoin('p.assigned_to', 'u')
            ->leftJoin('p.ba', 'pb')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('p.project_name', ':filter'),
                    $query->expr()->like('DATE(p.created_at)', ':filter'),
                    $query->expr()->like('pb.est_time', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->eq('u.id', ':emp_id')
                )
            )
            ->setParameters(
                ['filter' => '%' . $search . '%', 'emp_id' => $emp_id]
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countAllProjectsBa($emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(p.id) as count')
            ->from("App\Entities\Project", "p")
            ->leftJoin('p.assigned_to', 'u')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('u.id', ':emp_id')
                )
            )
            ->setParameters(
                ['emp_id' => $emp_id]
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function getAllProjectsBaDataTable($order, $col, $search, $start, $length, $emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('p.id,
                            p.project_name,
                            p.client_name,
                            p.project_doc,
                            p.project_description,
                            p.created_at,
                            p.client_email,
                            p.skype_contact,
                            pb.id as project_ba_id,
                            pb.est_time,
                            o.value_text as sr_flag_value,o.key_text as sr_flag_key')
            ->from("App\Entities\Project", "p")
            ->leftJoin('p.assigned_to', 'u')
            ->leftJoin('p.ba', 'pb')
            ->leftJoin('pb.flag', 'o')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('p.project_name', ':filter'),
                    $query->expr()->like('DATE(p.created_at)', ':filter'),
                    $query->expr()->like('pb.est_time', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->eq('u.id', ':emp_id')
                )
            )
            ->orderBy(($col == 'est_time' ? 'pb.' : 'p.') . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(['filter' => '%' . $search . '%', 'emp_id' => $emp_id]);
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function delete($project_id)
    {
        $this->_em->createQueryBuilder()
            ->delete()
            ->from('App\Entities\ProjectBa', 'pb')
            ->where('pb.project_id=:project_id')
            ->setParameter('project_id', $project_id)
            ->getQuery();
    }

    public function checkUserProjectExists($project_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('pb,pb.id,u.id as emp_id,jr_ba')
            ->from('App\Entities\ProjectBa', 'pb')
            ->leftJoin('pb.emp_id', 'u')
            ->leftJoin('pb.project_jr_ba', 'jr_ba')
            ->where('pb.project_id=:p_id')
            ->setParameters(['p_id' => $project_id])
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getBaProjectById($id)
    {
        $query = $this->_em->createQueryBuilder()
            //            ->select('pb.id as id,
            //                            pb.est_time,
            //                            p.id as project_id,
            //                            p.project_name,
            //                            p.client_name,
            //                            p.project_doc,
            //                            p.project_description,
            //                            p.client_email,
            //                            p.skype_contact,
            //                            p.created_at,
            //                            pjb.id as project_jr_ba_id')

            ->select('pb,p,pjb,jr')
            ->from('App\Entities\ProjectBa', 'pb')
            ->leftJoin('pb.project_id', 'p')
            ->leftJoin('pb.project_jr_ba', 'pjb')
            ->leftJoin('pjb.emp_id', 'jr')
            ->where('pb.id=:id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getArrayResult()[0];
    }
    public function getAllJBaSelfBA()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('u.id', 'u.firstname', 'u.lastname', 'u.profile_image')
            ->from('App\Entities\User', 'u')
            ->where('u.user_exit_status=:status')
            ->andWhere('u.designation=:id')
            ->setParameters(['status' => 1, 'id' => 1])
            ->orderBy('u.created_at', 'DESC')
            ->getQuery();
        return $query->getArrayResult();
    }
    public function getAllEmpTimingRecords($project_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('apt,p,cb')
            ->from('App\Entities\ApprovedProjectEmpTimings', 'apt')
            ->leftJoin('apt.emp_id', 'p')
            ->leftJoin('apt.created_by', 'cb')
            ->where('apt.project_id=:id')
            ->setParameter('id', $project_id)
            ->orderBy('apt.created_at', 'DESC')
            ->getQuery();
        return $query->getArrayResult();
    }
    public function getAllEmpTimingRecordsWithTotal($project_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('SUM(apt.record_hours) as total,MAX(apt.record_date) as max_date, MIN(apt.record_date) as min_date')
            ->from('App\Entities\ApprovedProjectEmpTimings', 'apt')
            ->where('apt.project_id=:id')
            ->setParameter('id', $project_id)
            ->getQuery();
        return $query->getArrayResult();
    }
}

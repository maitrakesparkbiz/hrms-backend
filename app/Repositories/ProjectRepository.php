<?php

namespace App\Repositories;

use App\Entities\Project;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ProjectRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\Project'));
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');
    }

    public function create(Project $project)
    {
        $this->_em->persist($project);
        $this->_em->flush();

        return $project;
    }

    public function prepareData($data)
    {
        return new Project($data);
    }

    public function ProjectOfId($id)
    {
        return $this->_em->getRepository('App\Entities\Project')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(Project $project, $data)
    {
        if (isset($data["client_name"])) {
            $project->setClientName($data["client_name"]);
        }
        if (isset($data["project_name"])) {
            $project->setProjectName($data["project_name"]);
        }
        if (isset($data["client_email"])) {
            $project->setClientEmail($data["client_email"]);
        }
        if (isset($data["skype_contact"])) {
            $project->setSkypeContact($data["skype_contact"]);
        }
        if (isset($data["project_description"])) {
            $project->setProjectDescription($data["project_description"]);
        }
        if (array_key_exists('project_doc', $data)) {
            $project->setProjectDoc($data["project_doc"]);
        }
        if (isset($data["created_by"])) {
            $project->setCreatedBy($data["created_by"]);
        }
        if (isset($data["assigned_to"])) {
            $project->setAssignedTo($data["assigned_to"]);
        }
        if (isset($data["status_flag"])) {
            $project->setStatusFlag($data["status_flag"]);
        }
        if (isset($data["final_approved"])) {
            $project->setFinalApproved($data["final_approved"]);
        }

        $this->_em->persist($project);
        $this->_em->flush();

        return $project;
    }
    public function getFilterRecords($search, $emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(p.id) as count')
            ->from("App\Entities\Project", "p")
            ->leftJoin('p.assigned_to', 'u')
            ->leftJoin('p.status_flag', 'o')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('p.client_name', ':filter'),
                    $query->expr()->like('p.project_name', ':filter'),
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                    $query->expr()->like('DATE(p.created_at)', ':filter')
                ),
                $query->expr()->eq('p.created_by', ':emp_id')
            )
            ->setParameters(
                ['filter' => '%' . $search . '%', 'emp_id' => $emp_id]
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countAllProjects($emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(p.id) as count')
            ->from("App\Entities\Project", "p")
            ->where(
                $query->expr()->eq('p.created_by', ':emp_id')
            )
            ->setParameters(
                ['emp_id' => $emp_id]
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function getAllProjectsDataTable($order, $col, $search, $start, $length, $emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('p.id,
                            p.client_name,
                            p.project_name,
                            p.project_description, 
                            p.project_doc,
                            p.created_at,
                            p.client_email,
                            p.final_approved,
                            p.skype_contact,
                            pb.id as project_ba_id,
                            pb.est_time,
                            u.id as emp_id,
                            u.firstname,
                            u.lastname,
                            u.profile_image,
                            o.value_text as status,
                            o.key_text')
            ->from("App\Entities\Project", "p")
            ->leftJoin('p.assigned_to', 'u')
            ->leftJoin('p.status_flag', 'o')
            ->leftJoin('p.ba', 'pb')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('p.client_name', ':filter'),
                    $query->expr()->like('p.project_name', ':filter'),
                    $query->expr()->like('p.client_name', ':filter'),
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                    $query->expr()->like('DATE(p.created_at)', ':filter')
                ),
                $query->expr()->eq('p.created_by', ':emp_id')
            )
            ->orderBy(($col == 'firstname' ? 'u.' : 'p.') . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(['filter' => '%' . $search . '%', 'emp_id' => $emp_id]);
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function getProjectById($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('p.id,
                            p.client_name,
                            p.project_name,
                            p.project_description,
                            p.project_doc,
                            p.client_email,
                            p.skype_contact,
                            pb.est_time,
                            u.id as assigned_to,
                            u.firstname as ba_fname,
                            u.lastname as ba_lname')
            ->from('App\Entities\Project', 'p')
            ->leftJoin('p.assigned_to', 'u')
            ->leftJoin('p.ba', 'pb')
            ->where('p.id=:id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getArrayResult()[0];
    }

    public function getNotifyData($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('p.project_name,
                            u.id as created_by')
            ->from('App\Entities\Project', 'p')
            ->where('p.id=:id')
            ->leftJoin('p.created_by', 'u')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getArrayResult()[0];
    }

    public function getAllBASelfSales()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('u.id', 'u.firstname', 'u.lastname', 'u.profile_image')
            ->from('App\Entities\User', 'u')
            ->where('u.user_exit_status=:status')
            ->andWhere('u.designation=:id')
            ->setParameters(['status' => 1, 'id' => 4])
            ->orderBy('u.created_at', 'DESC')
            ->getQuery();
        return $query->getArrayResult();
    }
}

<?php

namespace App\Repositories;

use App\Entities\ApprovedProject;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ApprovedProjectRepository extends EntityRepository
{

    private $class = 'App\Entities\ApprovedProject';

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\ApprovedProject'));
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');
    }

    public function create(ApprovedProject $approvedProject)
    {
        $this->_em->persist($approvedProject);
        $this->_em->flush();

        return $approvedProject;
    }

    public function prepareData($data)
    {
        return new ApprovedProject($data);
    }

    public function ApprovedProjectOfId($id)
    {
        return $this->_em->getRepository('App\Entities\ApprovedProject')->findOneBy([
            "id" => $id
        ]);
    }

    public function getFinalApproveProjectById($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('ap,
                            u.id as created_by,
                            ba.firstname as ba_fname,
                            ba.lastname as ba_lname,
                            ba.id as assigned_ba,
                            jba.id as assigned_jr_ba,
                            f.id as flag_id')
            ->from('App\Entities\ApprovedProject', 'ap')
            ->leftJoin('ap.flags', 'f')
            ->leftJoin('ap.created_by', 'u')
            ->leftJoin('ap.assigned_ba', 'ba')
            ->leftJoin('ap.assigned_jr_ba', 'jba')
            ->where('ap.id=:id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getArrayResult()[0];
    }
    public function getSalesFilterRecords($search, $emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(ap.id) as count')
            ->from($this->class, "ap")
            ->leftJoin('ap.assigned_ba', 'u')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('ap.client_name', ':filter'),
                    $query->expr()->like('ap.project_name', ':filter'),
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                    $query->expr()->like('DATE(ap.created_at)', ':filter')
                ),
                $query->expr()->eq('ap.created_by', ':emp_id')
            )
            ->setParameters(
                ['filter' => '%' . $search . '%', 'emp_id' => $emp_id]
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countAllApprovedProjects($emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(ap.id) as count')
            ->from($this->class, "ap")
            ->where(
                $query->expr()->eq('ap.created_by', ':emp_id')
            )
            ->setParameters(
                ['emp_id' => $emp_id]
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function getAllApprovedProjectsDataTable($order, $col, $search, $start, $length, $emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('ap,
                        u.id as emp_id,
                        u.firstname,
                        u.lastname,
                        u.profile_image,
                        fs.key_text,
                        fs.value_text,
                        f.id as project_flag_id')
            ->from($this->class, 'ap')
            ->leftJoin('ap.assigned_ba', 'u')
            ->leftJoin('ap.flags', 'f')
            ->leftJoin('f.flag_sales', 'fs')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('ap.project_name', ':filter'),
                    $query->expr()->like('ap.client_name', ':filter'),
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                    $query->expr()->like('DATE(ap.created_at)', ':filter')
                ),
                $query->expr()->eq('ap.created_by', ':emp_id')
            )
            ->orderBy(($col == 'firstname' ? 'u.' : 'ap.') . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                ['filter' => '%' . $search . '%', 'emp_id' => $emp_id]
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }
    public function getFilterRecords($search, $emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(ap.id) as count')
            ->from($this->class, "ap")
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('ap.project_name', ':filter'),
                    $query->expr()->like('DATE(ap.created_at)', ':filter'),
                    $query->expr()->like('ap.approved_est_time', ':filter')
                ),
                $query->expr()->eq('ap.assigned_ba', ':emp_id')
            )
            ->setParameters(
                ['filter' => '%' . $search . '%', 'emp_id' => $emp_id]
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countAllApprovedProjectsBa($emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(ap.id) as count')
            ->from($this->class, "ap")
            ->where(
                $query->expr()->eq('ap.assigned_ba', ':emp_id')
            )
            ->setParameters(
                ['emp_id' => $emp_id]
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }


    public function getAllApprovedProjectsBaDataTable($order, $col, $search, $start, $length, $emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('ap,
                                u.id as emp_id,
                                u.firstname as jr_ba_fname,
                                u.lastname as jr_ba_lname,
                                u.profile_image as jr_ba_profile,
                                fb.key_text as sr_flag_key,
                                fb.value_text as sr_flag_value,
                                fjb.key_text as jr_flag_key,
                                fjb.value_text as jr_flag_value,
                                f.id as project_flag_id')
            ->from($this->class, 'ap')
            ->leftJoin('ap.assigned_jr_ba', 'u')
            ->leftJoin('ap.flags', 'f')
            ->leftJoin('f.flag_ba', 'fb')
            ->leftJoin('f.flag_ba_to_jr', 'fjb')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('ap.project_name', ':filter'),
                    $query->expr()->like('DATE(ap.created_at)', ':filter'),
                    $query->expr()->like('ap.approved_est_time', ':filter')
                ),
                $query->expr()->eq('ap.assigned_ba', ':emp_id')
            )
            ->orderBy('ap.' . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                ['filter' => '%' . $search . '%', 'emp_id' => $emp_id]
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }
    public function getJrBaFilterRecords($search, $emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(ap.id) as count')
            ->from($this->class, "ap")
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('ap.project_name', ':filter'),
                    $query->expr()->like('DATE(ap.created_at)', ':filter'),
                    $query->expr()->like('ap.approved_est_time', ':filter')
                ),
                $query->expr()->eq('ap.assigned_jr_ba', ':emp_id')
            )
            ->setParameters(
                ['filter' => '%' . $search . '%', 'emp_id' => $emp_id]
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countAllApprovedProjectsJrBa($emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(ap.id) as count')
            ->from($this->class, "ap")
            ->where(
                $query->expr()->eq('ap.assigned_jr_ba', ':emp_id')
            )
            ->setParameters(
                ['emp_id' => $emp_id]
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function getAllApprovedProjectsJrBaDataTable($order, $col, $search, $start, $length, $emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('ap,
                                u.id as emp_id,
                                u.firstname as jr_ba_fname,
                                u.lastname as jr_ba_lname,
                                u.profile_image as jr_ba_profile,
                                fjb.key_text,
                                fjb.value_text,
                                f.id as project_flag_id')
            ->from($this->class, 'ap')
            ->leftJoin('ap.assigned_ba', 'u')
            ->leftJoin('ap.flags', 'f')
            ->leftJoin('f.flag_jr_ba', 'fjb')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('ap.project_name', ':filter'),
                    $query->expr()->like('DATE(ap.created_at)', ':filter'),
                    $query->expr()->like('ap.approved_est_time', ':filter')
                ),
                $query->expr()->eq('ap.assigned_jr_ba', ':emp_id')
            )
            ->orderBy('ap.' . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                ['filter' => '%' . $search . '%', 'emp_id' => $emp_id]
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }


    public function update(ApprovedProject $approvedProject, $data)
    {
        if (isset($data["main_project_id"])) {
            $approvedProject->setMainProjectId($data["main_project_id"]);
        }
        if (isset($data["client_name"])) {
            $approvedProject->setClientName($data["client_name"]);
        }
        if (isset($data["project_name"])) {
            $approvedProject->setProjectName($data["project_name"]);
        }
        if (isset($data["client_email"])) {
            $approvedProject->setClientEmail($data["client_email"]);
        }
        if (isset($data["skype_contact"])) {
            $approvedProject->setSkypeContact($data["skype_contact"]);
        }
        if (isset($data["project_description"])) {
            $approvedProject->setProjectDescription($data["project_description"]);
        }
        if (array_key_exists('project_doc', $data)) {
            $approvedProject->setProjectDoc($data["project_doc"]);
        }
        if (isset($data["created_by"])) {
            $approvedProject->setCreatedBy($data["created_by"]);
        }
        if (isset($data["assigned_ba"])) {
            $approvedProject->setAssignedBa($data["assigned_ba"]);
        }
        if (isset($data["assigned_jr_ba"])) {
            $approvedProject->setAssignedJrBa($data["assigned_jr_ba"]);
        }
        if (isset($data["threshold_limit1"])) {
            $approvedProject->setThresholdLimit1($data["threshold_limit1"]);
        }
        if (isset($data["threshold_limit2"])) {
            $approvedProject->setThresholdLimit2($data["threshold_limit2"]);
        }
        if (isset($data["deadline"])) {
            $approvedProject->setDeadline($data["deadline"]);
        }
        if (isset($data["est_time"])) {
            $approvedProject->setEstTime($data["est_time"]);
        }
        if (isset($data["approved_est_time"])) {
            $approvedProject->setApprovedEstTime($data["approved_est_time"]);
        }
        if (isset($data["is_started"])) {
            $approvedProject->setIsStarted($data["is_started"]);
        }
        if (array_key_exists("approved_extra_hours", $data)) {
            $approvedProject->setApprovedExtraHours($data["approved_extra_hours"]);
        }
        if (array_key_exists("approved_extra_hours_reason", $data)) {
            $approvedProject->setApprovedExtraHoursReason($data["approved_extra_hours_reason"]);
        }
        if (isset($data["ba_project_hours"])) {
            $approvedProject->setBaProjectHours($data["ba_project_hours"]);
        }
        if (isset($data["on_hold"])) {
            $approvedProject->setOnHold($data["on_hold"]);
        }
        if (array_key_exists("hold_comment", $data)) {
            $approvedProject->setHoldComment($data["hold_comment"]);
        }

        $this->_em->persist($approvedProject);
        $this->_em->flush();

        return $approvedProject;
    }
    public function getBaMailById($id) {
        $query = $this->_em->createQueryBuilder()
            ->select('u.email')
            ->from('App\Entities\user', 'u')
            ->where('u.id=:id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getArrayResult()[0]['email'];
    }


    //================================================ updated module ===================================//
    public function getAllApprovedProjectsBaTl($order, $col, $search, $start, $length, $emp_id){
        $query = $this->_em->createQueryBuilder();
        $query->select('ap,
                        u.id as emp_id,
                        u.firstname,
                        u.lastname,
                        u.profile_image,
                        fs.key_text,
                        fs.value_text,
                        f.id as project_flag_id')
            ->from($this->class, 'ap')
            ->leftJoin('ap.assigned_ba', 'u')
            ->leftJoin('ap.flags', 'f')
            ->leftJoin('f.flag_sales', 'fs')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('ap.project_name', ':filter'),
                    $query->expr()->like('ap.client_name', ':filter'),
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                    $query->expr()->like('DATE(ap.created_at)', ':filter')
                ),
                $query->expr()->eq('ap.created_by', ':emp_id')
            )
            ->orderBy(($col == 'firstname' ? 'u.' : 'ap.') . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                ['filter' => '%' . $search . '%', 'emp_id' => $emp_id]
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countAllApprovedProjectsBaTl(){

    }

    public function getBaTlFilteredRecords(){
        
    }
}
 
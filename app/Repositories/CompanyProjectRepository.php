<?php

namespace App\Repositories;

use App\Entities\CompanyProject;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class CompanyProjectRepository extends EntityRepository
{

    private $class = 'App\Entities\CompanyProject';

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\CompanyProject'));
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');
    }

    public function create(CompanyProject $companyProject)
    {
        $this->_em->persist($companyProject);
        $this->_em->flush();

        return $companyProject;
    }

    public function prepareData($data)
    {
        return new CompanyProject($data);
    }

    public function CompanyProjectOfId($id)
    {
        return $this->_em->getRepository('App\Entities\CompanyProject')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(CompanyProject $companyProject, $data)
    {
        if (isset($data["created_by"])) {
            $companyProject->setCreatedBy($data["created_by"]);
        }
        if (isset($data["updated_by"])) {
            $companyProject->setUpdatedBy($data["updated_by"]);
        }

        if (isset($data["flag"])) {
            $companyProject->setFlag($data["flag"]);
        }

        if (isset($data["client_name"])) {
            $companyProject->setClientName($data["client_name"]);
        }
        if (isset($data["project_name"])) {
            $companyProject->setProjectName($data["project_name"]);
        }
        if (isset($data["project_description"])) {
            $companyProject->setProjectDescription($data["project_description"]);
        }
        if (array_key_exists("project_doc", $data)) {
            if($data["project_doc"] == null)
            {
                $data["project_doc"] = '';
            }
            $companyProject->setProjectDoc($data["project_doc"]);
        }
        if (isset($data["threshold_limit1"])) {
            $companyProject->setThresholdLimit1($data["threshold_limit1"]);
        }
        if (isset($data["threshold_limit2"])) {
            $companyProject->setThresholdLimit2($data["threshold_limit2"]);
        }
        if (isset($data["deadline"])) {
            $companyProject->setDeadline($data["deadline"]);
        }
        if (isset($data["est_time"])) {
            $companyProject->setEstTime($data["est_time"]);
        }
        if (isset($data["extra_hours_comment"])) {
            $companyProject->setExtraHoursComment($data["extra_hours_comment"]);
        }
        if (isset($data["is_started"])) {
            $companyProject->setIsStarted($data["is_started"]);
        }
        if (isset($data["on_hold"])) {
            $companyProject->setOnHold($data["on_hold"]);
        }
        if (isset($data["is_tl"])) {
            $companyProject->setIsTl($data["is_tl"]);
        }
        if (isset($data["is_own"])) {
            $companyProject->setIsOwn($data["is_own"]);
        }
        if (isset($data["is_closed"])) {
            $companyProject->setIsClosed($data["is_closed"]);
        }
        if (isset($data["hold_comment"])) {
            $companyProject->setHoldComment($data["hold_comment"]);
        }

        $this->_em->persist($companyProject);
        $this->_em->flush();

        return $companyProject;
    }

    public function getAllProjectsDatatableBaTl($order, $col, $search, $start, $length, $emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('p.id,
                        p.project_name,
                        p.created_at,
                        p.est_time,
                        p.client_name,
                        p.project_doc,
                        p.project_description,
                        p.is_started,
                        p.on_hold,
                        p.is_closed,
                        u.id as emp_id,
                        u.firstname,
                        u.lastname,
                        u.profile_image,
                        ub.id as update_emp_id,
                        ub.firstname as update_fname,
                        ub.lastname as update_lname,
                        ub.profile_image as update_profile_image,
                        f.key_text,
                        f.value_text,SUM(cmt.record_hours) as total_hours')
            ->from($this->class, 'p')
            ->leftJoin('p.created_by', 'u')
            ->leftJoin('p.updated_by', 'ub')
            ->leftJoin('p.company_projects', 'cmt')
            ->leftJoin('p.flag', 'f')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('p.project_name', ':filter'),
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                    $query->expr()->like('DATE(p.created_at)', ':filter'),
                    $query->expr()->like('p.est_time', ':filter')
                ),
                $query->expr()->eq('p.is_closed', ':closed')
            )
            ->orderBy(($col == 'firstname' ? 'u.' : 'p.') . $col, strtoupper($order))
            ->groupBy('p.id')
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                [
                    'filter' => '%' . $search . '%',
                    'closed' => 0
                ]
            );
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function getAllClosedProjectsDatatableBaTl($order, $col, $search, $start, $length, $emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('p.id,
                        p.project_name,
                        p.created_at,
                        p.est_time,
                        p.client_name,
                        p.project_doc,
                        p.project_description,
                        p.is_started,
                        p.on_hold,
                        p.is_closed,
                        u.id as emp_id,
                        u.firstname,
                        u.lastname,
                        u.profile_image,
                        f.key_text,
                        f.value_text')
            ->from($this->class, 'p')
            ->leftJoin('p.created_by', 'u')
            ->leftJoin('p.flag', 'f')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('p.project_name', ':filter'),
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                    $query->expr()->like('DATE(p.created_at)', ':filter'),
                    $query->expr()->like('p.est_time', ':filter')
                ),
                $query->expr()->eq('p.is_closed', ':closed')
            )
            ->orderBy(($col == 'firstname' ? 'u.' : 'p.') . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                [
                    'filter' => '%' . $search . '%',
                    'closed' => 1
                ]
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countAllProjectsBaTl()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('count(p.id) as count')
            ->from($this->class, 'p')
            ->where('p.is_closed=:closed')
            ->setParameters(
                [
                    'closed' => 0
                ]
            )
            ->getQuery();

        return $query->getArrayResult();
    }

    public function countAllClosedProjectsBaTl()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('count(p.id) as count')
            ->from($this->class, 'p')
            ->where('p.is_closed=:closed')
            ->setParameters(
                [
                    'closed' => 1
                ]
            )
            ->getQuery();

        return $query->getArrayResult();
    }

    public function countFilteredProjectsBaTl($search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(p.id) as count')
            ->from($this->class, 'p')
            ->leftJoin('p.created_by', 'u')
            ->leftJoin('p.flag', 'f')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('p.project_name', ':filter'),
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                    $query->expr()->like('DATE(p.created_at)', ':filter'),
                    $query->expr()->like('p.est_time', ':filter')
                ),
                $query->expr()->eq('p.is_closed', ':closed')
            )
            ->setParameters(
                [
                    'filter' => '%' . $search . '%',
                    'closed' => 0
                ]
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countFilteredClosedProjectsBaTl($search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(p.id) as count')
            ->from($this->class, 'p')
            ->leftJoin('p.created_by', 'u')
            ->leftJoin('p.flag', 'f')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('p.project_name', ':filter'),
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                    $query->expr()->like('DATE(p.created_at)', ':filter'),
                    $query->expr()->like('p.est_time', ':filter')
                ),
                $query->expr()->eq('p.is_closed', ':closed')
            )
            ->setParameters(
                [
                    'filter' => '%' . $search . '%',
                    'closed' => 1
                ]
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function getOwnProjectsDatatableBaTl($order, $col, $search, $start, $length, $emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('p.id,
                        p.project_name,
                        p.created_at,
                        p.est_time,
                        p.client_name,
                        p.project_doc,
                        p.is_started,
                        p.on_hold,
                        p.is_closed,
                        p.project_description,
                        f.key_text,
                        f.value_text')
            ->from($this->class, 'p')
            ->leftJoin('p.flag', 'f')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('p.project_name', ':filter'),
                    $query->expr()->like('DATE(p.created_at)', ':filter'),
                    $query->expr()->like('p.est_time', ':filter')
                ),
                $query->expr()->eq('p.is_tl', ':is_tl'),
                $query->expr()->eq('p.is_own', ':is_own'),
                $query->expr()->eq('p.created_by', ':emp_id')
            )
            ->orderBy(($col == 'firstname' ? 'u.' : 'p.') . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                [
                    'filter' => '%' . $search . '%',
                    'emp_id' => $emp_id,
                    'is_tl' => 1,
                    'is_own' => 1
                ]
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countOwnProjectsBaTl($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('count(p.id) as count')
            ->from($this->class, 'p')
            ->where('p.created_by=:emp_id')
            ->andWhere('p.is_tl = 1')
            ->andWhere('p.is_own = 1')
            ->setParameters(['emp_id' => $emp_id])
            ->getQuery();

        return $query->getArrayResult();
    }

    public function countFilteredOwnProjectsBaTl($search, $emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(p.id) as count')
            ->from($this->class, 'p')
            ->leftJoin('p.flag', 'f')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('p.project_name', ':filter'),
                    $query->expr()->like('DATE(p.created_at)', ':filter'),
                    $query->expr()->like('p.est_time', ':filter')
                ),
                $query->expr()->eq('p.is_tl', ':is_tl'),
                $query->expr()->eq('p.is_own', ':is_own'),
                $query->expr()->eq('p.created_by', ':emp_id')
            )
            ->setParameters(
                [
                    'filter' => '%' . $search . '%',
                    'emp_id' => $emp_id,
                    'is_tl' => 1,
                    'is_own' => 1
                ]
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function getProjectById($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('cp,ba,ba_tl,u')
            ->from('App\Entities\CompanyProject', 'cp')
            ->leftJoin('cp.created_by', 'ba_tl')
            ->leftJoin('cp.assigned_ba', 'ba')
            ->leftJoin('ba.emp_id', 'u')
            ->where('cp.id=:id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getArrayResult()[0];
    }

    public function getProjectByIdBA($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('p,pb,u')
            ->from($this->class, 'p')
            ->leftJoin('p.assigned_ba', 'pb')
            ->leftJoin('p.created_by', 'u')
            ->where('p.id=:id')
            ->setParameters(['id' => $id])
            ->getQuery();

        return $query->getArrayResult()[0];
    }
    public function getBaProjectByIdBA($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('p,pb,pbe,u')
            ->from($this->class, 'p')
            ->leftJoin('p.assigned_ba', 'pb')
            ->leftJoin('pb.emp_id', 'pbe')
            ->leftJoin('p.created_by', 'u')
            ->where('p.id=:id')
            ->setParameters(['id' => $id])
            ->getQuery();

        return $query->getArrayResult()[0];
    }

    public function getAllTLEmail()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('ba_tl.email')
            ->from('App\Entities\CompanyProject', 'cp')
            ->leftJoin('cp.created_by', 'ba_tl')
            ->where('cp.is_tl=:id')
            ->setParameter('id', 1)
            ->getQuery();

        return $query->getArrayResult();
    }

    public function checkProjectNameExist($project_name, $proj_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('cp.project_name')
            ->from('App\Entities\CompanyProject', 'cp')
            ->where('cp.project_name=:project_name');
        if ($proj_id) {
            $query->andWhere('cp.id!=:proj_id')
                ->setParameters([
                    'project_name' => $project_name,
                    'proj_id' => $proj_id
                ]);
        } else {
            $query->setParameters([
                'project_name' => $project_name
            ]);
        }
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }
    public function getReportByEmp($start,$end_date,$emp_id,$project_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('cpe,cp')
            ->from('App\Entities\CompanyProjectEmpTimings', 'cpe')
            ->leftJoin('cpe.company_project_id','cp')
            ->orderBy('cpe.record_date','desc')
            ->where('cpe.emp_id=:id')
            ->andWhere('cpe.record_date>=:start')
            ->andWhere('cpe.record_date<=:end');
            if ($project_id) {
                $query->andWhere('cpe.company_project_id=:project_id')
                    ->setParameters(['id' => $emp_id,'project_id'=>$project_id,'start'=>$start,'end'=>$end_date]);
            } else {
                $query ->setParameters(['id' => $emp_id,'start'=>$start,'end'=>$end_date]);
            }

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }
    public function getAllProjectsList()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('cp.id,cp.project_name')
            ->from($this->class, 'cp')
            ->getQuery();

        return $query->getArrayResult();
    }
    public function getTotalHoursByProject($project_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('SUM(cp.record_hours) as sum')
            ->from('App\Entities\CompanyProjectEmpTimings', 'cp')
            ->where('cp.company_project_id=:id')
            ->setParameter('id', $project_id)
            ->getQuery();

        return $query->getArrayResult()[0];
    }

}

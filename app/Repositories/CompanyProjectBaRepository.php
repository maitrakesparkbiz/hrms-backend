<?php

namespace App\Repositories;

use App\Entities\CompanyProjectBa;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class CompanyProjectBaRepository extends EntityRepository
{

    private $class = 'App\Entities\CompanyProjectBa';
    private $projectClass = 'App\Entities\CompanyProject';

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\CompanyProjectBa'));
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');
    }

    public function create(CompanyProjectBa $companyProject)
    {
        $this->_em->persist($companyProject);
        $this->_em->flush();

        return $companyProject;
    }

    public function prepareData($data)
    {
        return new CompanyProjectBa($data);
    }

    public function CompanyProjectBaOfId($id)
    {
        return $this->_em->getRepository('App\Entities\CompanyProjectBa')->findOneBy([
            "id" => $id
        ]);
    }

    public function delete(CompanyProjectBa $companyProjectBa)
    {
        $this->_em->remove($companyProjectBa);
        $this->_em->flush();
    }

    public function update(CompanyProjectBa $companyProjectBa, $data)
    {
        if (isset($data['company_project_id'])) {
            $companyProjectBa->setCompanyProjectId($data['company_project_id']);
        }
        if (isset($data['emp_id'])) {
            $companyProjectBa->setEmpId($data['emp_id']);
        }
        if (isset($data['flag'])) {
            $companyProjectBa->setFlag($data['flag']);
        }
        if (isset($data['ba_tl_flag'])) {
            $companyProjectBa->setBaTlFlag($data['ba_tl_flag']);
        }

        $this->_em->persist($companyProjectBa);
        $this->_em->flush();

        return $companyProjectBa;
    }

    public function getProjectDataBaTl($proj_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('u.firstname,u.lastname,u.profile_image,f.key_text,f.value_text')
            ->from($this->class, 'pb')
            ->leftJoin('pb.emp_id', 'u')
            ->leftJoin('pb.ba_tl_flag', 'f')
            ->where('pb.company_project_id=:id')
            ->setParameter('id', $proj_id)
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getAllProjectsDatatableBa($order, $col, $search, $start, $length, $emp_id, $getClosed = false)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('p')
//        $query->select('p.client_name,p.created_at,p.deadline,p.deletedAt,p.est_time,p.extra_hours_comment,p.hold_comment,p.id,p.is_closed,p.is_own,p.is_started,p.is_tl,p.on_hold,p.project_description,p.project_doc,p.project_name,p.threshold_limit1,p.threshold_limit2,p.updated_at')
            ->from($this->projectClass, 'p')
            ->leftJoin('p.assigned_ba', 'pb');
        if ($getClosed) {
            $query->addSelect('u.id as emp_id,
                        u.firstname,
                        u.lastname,
                        u.profile_image,
                        pf.key_text as pf_key_text,
                        pf.value_text as pf_value_text')
                ->leftJoin('p.created_by', 'u')
                ->leftJoin('p.flag', 'pf')
                ->where(
                    $query->expr()->orX(
                        $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                        $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                        $query->expr()->like('p.project_name', ':filter'),
                        $query->expr()->like('DATE(p.created_at)', ':filter'),
                        $query->expr()->like('p.est_time', ':filter')
                    )
                )
                ->andWhere('p.is_closed=1')
                ->groupBy('p.id');
        } else {
            $query
//            ->addSelect('cmt')
//                ->addSelect('f.key_text,
//                        f.value_text,
//                        pf.key_text as pf_key_text,
//                        pf.value_text as pf_value_text')
//                ->leftJoin('p.flag', 'pf')
//                ->leftJoin('pb.flag', 'f')
//                ->leftJoin('p.company_projects', 'cmt')
                ->where('p.is_closed=0')
                ->andWhere(
                    $query->expr()->orX(
                        $query->expr()->like('p.project_name', ':filter'),
                        $query->expr()->like('DATE(p.created_at)', ':filter'),
                        $query->expr()->like('p.est_time', ':filter')
                    )
                )
                ->groupBy('p.id');
        }
        $query->andWhere(
            $query->expr()->orX(
                $query->expr()->eq('pb.emp_id', ':emp_id'),
                $query->expr()->eq('p.created_by', ':emp_id')
            )
        )->orderBy(($col == 'firstname' ? 'u.' : 'p.') . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                [
                    'filter' => '%' . $search . '%',
                    'emp_id' => $emp_id
                ]
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function getBaProjectFlags($order, $col, $search, $start, $length, $emp_id)
    {
        $query = $this->_em->createQueryBuilder();
//        $query->select('f.key_text,
//                        f.value_text,
//                        pf.key_text as pf_key_text,
//                        pf.value_text as pf_value_text,SUM(cmt.record_hours) as total_hours')
        $query->select('f.key_text,
                        f.value_text,
                        pf.key_text as pf_key_text,
                        pf.value_text as pf_value_text')
            ->from($this->projectClass, 'p')
            ->leftJoin('p.assigned_ba', 'pb')
            ->leftJoin('p.flag', 'pf')
            ->leftJoin('pb.flag', 'f')
            ->where('p.is_closed=0')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('p.project_name', ':filter'),
                    $query->expr()->like('DATE(p.created_at)', ':filter'),
                    $query->expr()->like('p.est_time', ':filter')
                )
            )->andWhere(
                $query->expr()->orX(
                    $query->expr()->eq('pb.emp_id', ':emp_id'),
                    $query->expr()->eq('p.created_by', ':emp_id')
                )
            )->orderBy(($col == 'firstname' ? 'u.' : 'p.') . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                [
                    'filter' => '%' . $search . '%',
                    'emp_id' => $emp_id
                ]
            );

        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countFilteredProjectsBa($search, $emp_id, $getClosed = false)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(distinct p.id) as count')
            ->from($this->projectClass, 'p')
            ->leftJoin('p.assigned_ba', 'pb');
        if ($getClosed) {
            $query->leftJoin('p.created_by', 'u')
                ->where(
                    $query->expr()->orX(
                        $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                        $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                        $query->expr()->like('p.project_name', ':filter'),
                        $query->expr()->like('DATE(p.created_at)', ':filter'),
                        $query->expr()->like('p.est_time', ':filter')
                    )
                )
                ->andWhere('p.is_closed=1');
        } else {
            $query->where('p.is_closed=0')
                ->andWhere(
                    $query->expr()->orX(
                        $query->expr()->like('p.project_name', ':filter'),
                        $query->expr()->like('DATE(p.created_at)', ':filter'),
                        $query->expr()->like('p.est_time', ':filter')
                    )
                );
        }
        $query->andWhere(
            $query->expr()->orX(
                $query->expr()->eq('pb.emp_id', ':emp_id'),
                $query->expr()->eq('p.created_by', ':emp_id')
            )
        )->setParameters(
            [
                'filter' => '%' . $search . '%',
                'emp_id' => $emp_id
            ]
        );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countAllProjectsBa($emp_id, $getClosed = false)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(distinct p.id) as count')
            ->from($this->projectClass, 'p')
            ->leftJoin('p.assigned_ba', 'pb')
            ->where(
                $query->expr()->orX(
                    $query->expr()->eq('pb.emp_id', ':emp_id'),
                    $query->expr()->eq('p.created_by', ':emp_id')
                )
            );
        if ($getClosed) {
            $query->andWhere('p.is_closed=1');
        } else {
            $query->andWhere('p.is_closed=0');
        }
        $query->setParameters(
            [
                'emp_id' => $emp_id
            ]
        );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function getAllBaOfProject($project_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('pb.id,u.id as emp_id')
            ->from('App\Entities\CompanyProjectBa', 'pb')
            ->leftJoin('pb.emp_id', 'u')
            ->where('pb.company_project_id=:project_id')
            ->setParameter('project_id', $project_id)
            ->getQuery();

        return $query->getArrayResult();
    }
}

<?php

namespace App\Repositories;

use App\Entities\JobApplications;
use App\Entities\JobOpenings;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class JobOpeningsRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\JobOpenings'));
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');
    }

    public function prepareData($data)
    {
        return new JobOpenings($data);
    }

    public function create(JobOpenings $job)
    {
        $this->_em->persist($job);
        $this->_em->flush();

        return $job->getId();
    }

    //    public function getAllOpenings(){
    //        $qry = $this->_em->createQueryBuilder()
    //                ->select('j')
    //                ->from('App\Entities\JobOpenings','j')
    //                ->getQuery();
    //
    //        return $qry->getArrayResult();
    //    }

    public function getAllOpenings($col, $order, $search, $start, $length, $status)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("j")
            ->from("App\Entities\JobOpenings", "j")
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('j.role', ':filter'),
                    $query->expr()->like('j.posted_as', ':filter'),
                    $query->expr()->like('DATE(j.posted_date)', ':filter'),
                    $query->expr()->like('DATE(j.last_date)', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->like('j.status', ':status')
                )
            )
            ->orderBy('j.' . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                [
                    'filter' => '%' . $search . '%',
                    'status' => ($status == 'all' ? '%' : $status)
                ]
            );

        $qb = $query->getQuery();

        $data = $qb->getResult(Query::HYDRATE_ARRAY);
        return $data;
    }

    public function countAllOpenings()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(j.id) as count')
            ->from("App\Entities\JobOpenings", "j");
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countFilteredOpenings($status, $search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(j.id) as count')
            ->from("App\Entities\JobOpenings", "j")
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('j.role', ':filter'),
                    $query->expr()->like('j.posted_as', ':filter'),
                    $query->expr()->like('DATE(j.posted_date)', ':filter'),
                    $query->expr()->like('DATE(j.last_date)', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->like('j.status', ':status')
                )
            )
            ->setParameters([
                'status' => ($status == 'all' ? '%' : $status),
                'filter' => '%' . $search . '%'
            ]);
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countAllSelfOpenings()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(j.id) as count')
            ->from('App\Entities\JobOpenings', 'j')
            ->where('j.posted_as IN(:posts)')
            ->andWhere('j.status=:status')
            ->setParameters(['posts' => ['Both', 'Internal'], 'status' => 'Open']);
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countFilteredRowsOpeningsSelf($search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(j.id) as count')
            ->from('App\Entities\JobOpenings', 'j')
            ->where('j.posted_as IN(:posts)')
            ->andWhere('j.status=:status')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('j.role', ':filter'),
                    $query->expr()->like('j.exp_required', ':filter'),
                    $query->expr()->like('DATE(j.last_date)', ':filter')
                )
            )
            ->setParameters(['posts' => ['Both', 'Internal'], 'status' => 'Open', 'filter' => '%' . $search . '%']);
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function getAllOpeningsSelf($col, $order, $search, $start, $length)
    {
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');

        $query = $this->_em->createQueryBuilder();
        $query->select("j")
            ->from("App\Entities\JobOpenings", "j")
            ->where('j.posted_as IN(:posts)')
            ->andWhere('j.status=:status')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('j.role', ':filter'),
                    $query->expr()->like('j.exp_required', ':filter'),
                    $query->expr()->like('DATE(j.last_date)', ':filter')
                )
            )
            ->orderBy('j.' . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                [
                    'filter' => '%' . $search . '%',
                    'status' => 'Open',
                    'posts' => ['Both', 'Internal']
                ]
            );

        $qb = $query->getQuery();

        $data = $qb->getResult(Query::HYDRATE_ARRAY);
        return $data;
    }

    public function getOpeningsPublic()
    {
        $qry = $this->_em->createQueryBuilder()
            ->select('j.id', 'j.role')
            ->from('App\Entities\JobOpenings', 'j')
            ->where('j.posted_as IN (:posts)')
            ->andWhere('j.status=:status')
            ->setParameters(['posts' => ['Both', 'Public'], 'status' => 'Open'])
            ->getQuery();
        return $qry->getArrayResult();
    }

    public function jobOfId($id)
    {
        return $this->_em->getRepository('App\Entities\JobOpenings')->findOneBy([
            "id" => $id
        ]);
    }

    public function getOpeningById($id, $isPublic = false)
    {
        $qry = $this->_em->createQueryBuilder()
            ->select('j')
            ->from('App\Entities\JobOpenings', 'j')
            ->where('j.id =:id');
        if ($isPublic) {
            $qry->andWhere('j.posted_as IN (:posts)')
                ->andWhere('j.status=:status')
                ->setParameters(['id' => $id, 'posts' => ['Both', 'Public'], 'status' => 'Open']);
        } else {
            $qry->setParameter('id', $id);
        }

        $qb = $qry->getQuery();
        return $qb->getArrayResult([0]);
    }

    public function updateJob(JobOpenings $jobOpenings, $data)
    {
        if (isset($data['role'])) {
            $jobOpenings->setRole($data['role']);
        }
        if (isset($data['exp_required'])) {
            $jobOpenings->setExpRequired($data['exp_required']);
        }
        if (isset($data['ctc'])) {
            $jobOpenings->setCtc($data['ctc']);
        }
        if (isset($data['vacancies'])) {
            $jobOpenings->setVacancies($data['vacancies']);
        }
        if (isset($data['introduction'])) {
            $jobOpenings->setIntroduction($data['introduction']);
        }
        if (isset($data['responsibilities'])) {
            $jobOpenings->setResponsibilities($data['responsibilities']);
        }
        if (isset($data['skill_set'])) {
            $jobOpenings->setSkillSet($data['skill_set']);
        }
        if (isset($data['last_date'])) {
            $jobOpenings->setLastDate($data['last_date']);
        }
        if (isset($data['posted_date'])) {
            $jobOpenings->setPostedDate($data['posted_date']);
        }
        if (isset($data['posted_as'])) {
            $jobOpenings->setPostedAs($data['posted_as']);
        }
        if (isset($data['status'])) {
            $jobOpenings->setStatus($data['status']);
        }

        $this->_em->persist($jobOpenings);
        $this->_em->flush();

        return $jobOpenings;
    }

    public function deleteOpening(JobOpenings $jobOpenings)
    {
        $this->_em->remove($jobOpenings);
        $this->_em->flush();
    }

    public function getJobOptionsData()
    {
        $qry = $this->_em->createQueryBuilder()
            ->select('j.id', 'j.role', 'j.exp_required')
            ->from('App\Entities\JobOpenings', 'j')
            ->getQuery();

        return $qry->getArrayResult();
    }
}

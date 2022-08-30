<?php

namespace App\Repositories;

use App\Entities\JobApplications;
use App\Entities\JobInterview;
use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class JobInterviewRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\JobInterview'));
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');
    }

    public function prepareData($data)
    {
        return new JobInterview($data);
    }

    public function create(JobInterview $jobInterview)
    {
        $this->_em->persist($jobInterview);
        $this->_em->flush();

        return $jobInterview->getId();
    }

    public function InterviewOfId($id)
    {
        return $this->_em->getRepository('App\Entities\JobInterview')->findOneBy([
            'id' => $id
        ]);
    }

    public function getJobIntById($id)
    {
        $qry = $this->_em->createQueryBuilder()
            ->select('ji')
            ->from('App\Entities\JobInterview', 'ji')
            ->leftJoin('ji.applicant_id', 'id')
            ->where('ji.applicant_id=:a_id')
            ->setParameter('a_id', $id)
            ->getQuery();

        return $qry->getArrayResult();
    }

    public function updateInterview(JobInterview $jobInterview, $data)
    {
        if (isset($data['interview_date'])) {
            $jobInterview->setInterviewDate($data['interview_date']);
        }
        if (isset($data['interview_time'])) {
            $jobInterview->setInterviewTime($data['interview_time']);
        }

        $this->_em->persist($jobInterview);
        $this->_em->flush();

        return $jobInterview->getId();
    }

    public function getIntByApplicantId($id)
    {
        $qry = $this->_em->createQueryBuilder()
            ->select('ji.interview_date', 'ji.interview_time', 'ja.applicant_name')
            ->from('App\Entities\JobInterview', 'ji')
            ->leftJoin('ji.applicant_id', 'ja')
            ->where('ja.id =:id')
            ->andWhere('ji.status =:status')
            ->setParameters(['id' => $id, 'status' => '1'])
            ->getQuery();

        return $qry->getArrayResult();
    }

    public function getRescheduleCount($id)
    {
        $qry = $this->_em->createQueryBuilder()
            ->select('count(ji) as re_count')
            ->from('App\Entities\JobInterview', 'ji')
            ->leftJoin('ji.applicant_id', 'ja')
            ->where('ja.id =:id')
            ->setParameter('id', $id)
            ->getQuery();

        return $qry->getArrayResult()[0];
    }

    public function getAllInterviews($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('ji.interview_date,ji.interview_time')
            ->from('App\Entities\JobInterview', 'ji')
            ->where('ji.applicant_id=:id')
            ->orderBy('ji.interview_date', 'DESC')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getTodayInterviewCount()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('count(ji) as count')
            ->from('App\Entities\JobInterview', 'ji')
            ->where('ji.interview_date=:date')
            ->setParameter('date', Carbon::today())
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getInterviewIdsByApplicantId($id)
    {
        $qry = $this->_em->createQueryBuilder()
            ->select('ji.id')
            ->from('App\Entities\JobInterview', 'ji')
            ->leftJoin('ji.applicant_id', 'ja')
            ->where('ja.id=:id')
            ->andWhere('ji.status=:status')
            ->setParameters(['status' => '1', 'id' => $id])
            ->getQuery();

        return $qry->getArrayResult();
    }

    public function changeInterviewStatus(JobInterview $jobInterview)
    {
        $jobInterview->setStatus('0');

        $this->_em->persist($jobInterview);
        $this->_em->flush();

        return $jobInterview->getId();
    }
}

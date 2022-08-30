<?php

namespace App\Repositories;

use App\Entities\JobApplications;
use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class JobApplicationsRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\JobApplications'));
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');
    }

    public function create(JobApplications $jobApplications)
    {
        $this->_em->persist($jobApplications);
        $this->_em->flush();
        return $jobApplications->getId();
    }

    public function prepareData($data)
    {
        return new JobApplications($data);
    }

    public function applicationsOfId($id)
    {
        return $this->_em->getRepository('App\Entities\JobApplications')->findOneBy([
            "id" => $id
        ]);
    }

    //    public function getAllJobApplications()
    //    {
    //        $qry = $this->_em->createQueryBuilder()
    //            ->select('ja', 'j.role', 'j.exp_required', 'js.stage_name', 'js.id as stage_id')
    //            ->from('App\Entities\JobApplications', 'ja')
    //            ->leftJoin('ja.stage', 'js')
    //            ->leftJoin('ja.job_id', 'j');
    //        $qb = $qry->getQuery();
    //        return $qb->getArrayResult();
    //    }

    public function countJobApplications()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(j.id) as count')
            ->from("App\Entities\JobApplications", "j");
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countFilteredRowsAll($status, $search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(j.id) as count')
            ->from("App\Entities\JobApplications", "j")
            ->leftJoin('j.job_id', 'ja')
            ->leftJoin('j.stage', 'js')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('j.applicant_name', ':filter'),
                    $query->expr()->like('DATE(j.created_at)', ':filter'),
                    $query->expr()->like('ja.role', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->like('js.stage_name', ':status')
                )
            )
            ->setParameter('status', ($status == 'all' ? '%' : $status))
            ->setParameter('filter', '%' . $search . '%');
        $qb = $query->getQuery();
        return $qb->getArrayResult();

    }

    public function getAllJobApplications($col, $order, $search, $start, $length, $status)
    {

        $query = $this->_em->createQueryBuilder();
        $query->select('ja', 'j.role', 'j.exp_required', 'js.stage_name', 'js.id as stage_id')
            ->from('App\Entities\JobApplications', 'ja')
            ->leftJoin('ja.stage', 'js')
            ->leftJoin('ja.job_id', 'j')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('ja.applicant_name', ':filter'),
                    $query->expr()->like('DATE(ja.created_at)', ':filter'),
                    $query->expr()->like('j.role', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->like('js.stage_name', ':status')
                )
            )
            ->orderBy(($col == 'stage_name' ? 'js.' : ($col == 'role' ? 'j.' : 'ja.')) . $col, strtoupper($order))
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



    //    public function getApplicationByEmpId($id)
    //    {
    //        $qry = $this->_em->createQueryBuilder()
    //            ->select('ja', 'j.role', 'j.exp_required', 'js.stage_name')
    //            ->from('App\Entities\JobApplications', 'ja')
    //            ->leftJoin('ja.stage', 'js')
    //            ->leftJoin('ja.job_id', 'j')
    //            ->leftJoin('ja.assoc_emp_id', 'u')
    //            ->where('u.id=:id')
    //            ->setParameter('id', $id);
    //        $qb = $qry->getQuery();
    //        return $qb->getArrayResult();
    //    }

    public function countJobApplicationsByEmpId($emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(j.id) as count')
            ->from("App\Entities\JobApplications", "j")
            ->leftJoin('j.assoc_emp_id', 'u')
            ->where('u.id=:id')
            ->setParameters(['id' => $emp_id]);
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function getApplicationByEmpId($emp_id, $col, $order, $search, $start, $length, $status)
    {
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');
        $query = $this->_em->createQueryBuilder();

        $query->select('ja', 'j.role', 'j.exp_required', 'js.stage_name')
            ->from('App\Entities\JobApplications', 'ja')
            ->leftJoin('ja.stage', 'js')
            ->leftJoin('ja.job_id', 'j')
            ->leftJoin('ja.assoc_emp_id', 'u')
            ->where('u.id=:id')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('ja.applicant_name', ':filter'),
                    $query->expr()->like('DATE(ja.created_at)', ':filter'),
                    $query->expr()->like('j.role', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->like('js.stage_name', ':status')
                )
            )
            ->orderBy(($col == 'stage_name' ? 'js.' : ($col == 'role' ? 'j.' : 'ja.')) . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                [
                    'filter' => '%' . $search . '%',
                    'status' => ($status == 'all' ? '%' : $status),
                    'id' => $emp_id
                ]
            );

        $qb = $query->getQuery();

        $data = $qb->getResult(Query::HYDRATE_ARRAY);
        return $data;
    }






    //    public function getAllJobApplicationsInt()
    //    {
    //        $qry = $this->_em->createQueryBuilder()
    //            ->select('ja', 'j.role', 'j.exp_required', 'js.stage_name', 'js.id as stage_id')
    //            ->from('App\Entities\JobApplications', 'ja')
    //            ->leftJoin('ja.stage', 'js')
    //            ->leftJoin('ja.job_id', 'j')
    //            ->where('js.id =:id')
    //            ->setParameter('id', 2);
    //        $qb = $qry->getQuery();
    //        return $qb->getArrayResult();
    //    }

    public function getAllJobApplicationsInt($col, $order, $search, $start, $length)
    {


        $query = $this->_em->createQueryBuilder();
        $query->select('ja', 'j.role', 'j.exp_required', 'js.stage_name', 'js.id as stage_id','MAX(ji.interview_date) as interview_date')
            ->from('App\Entities\JobApplications', 'ja')
            ->leftJoin('ja.stage', 'js')
            ->leftJoin('ja.job_int', 'ji')
            ->leftJoin('ja.job_id', 'j')
            ->where('js.id=:id')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('ja.applicant_name', ':filter'),
                    $query->expr()->like('DATE(ji.interview_date)', ':filter'),
                    $query->expr()->like('j.role', ':filter'),
                    $query->expr()->like('js.stage_name', ':filter')
                )
            )
            ->orderBy(($col == 'stage_name' ? 'js.' : ($col == 'role' ? 'j.' : 'ja.')) . $col, strtoupper($order))
            ->groupBy('ja.id')
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                ['filter' => '%' . $search . '%', 'id' => 2]
            );
        $qb = $query->getQuery();

        $data = $qb->getResult(Query::HYDRATE_ARRAY);
        return $data;
    }

    public function countJobApplicationsInt()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('j.id')
            ->from("App\Entities\JobApplications", "j")
            ->leftJoin('j.stage', 'js')
            ->where('js.id=:id')
            ->groupBy('j.id')
            ->setParameters(['id' => 2]);
        $qb = $query->getQuery();

        $data = $qb->getResult(Query::HYDRATE_ARRAY);
        return count($data);
    }

    public function countFilteredRowsJobsInt($search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('j.id')
            ->from("App\Entities\JobApplications", "j")
            ->leftJoin('j.stage', 'js')
            ->leftJoin('j.job_int', 'ji')
            ->leftJoin('j.job_id', 'ja')
            ->where('js.id=:id')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('j.applicant_name', ':filter'),
                    $query->expr()->like('DATE(ji.interview_date)', ':filter'),
                    $query->expr()->like('ja.role', ':filter'),
                    $query->expr()->like('js.stage_name', ':filter')
                )
            )
            ->groupBy('j.id')
            ->setParameters(['id' => 2, 'filter' => '%' . $search . '%']);
        $qb = $query->getQuery();

        $data = $qb->getResult(Query::HYDRATE_ARRAY);

        return count($data);

    }

    public function getAllJobApplicationsTodayInt($col, $order, $search, $start, $length)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('ja', 'j.role', 'j.exp_required', 'js.stage_name', 'js.id as stage_id')
            ->from('App\Entities\JobApplications', 'ja')
            ->leftJoin('ja.stage', 'js')
            ->leftJoin('ja.job_id', 'j')
            ->leftJoin('ja.job_int', 'ji')
            ->where('js.id=:id')
            ->andWhere('ji.interview_date=:today')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('ja.applicant_name', ':filter'),
                    $query->expr()->like('DATE(ja.created_at)', ':filter'),
                    $query->expr()->like('j.role', ':filter'),
                    $query->expr()->like('js.stage_name', ':filter')
                )
            )
            ->orderBy(($col == 'stage_name' ? 'js.' : ($col == 'role' ? 'j.' : 'ja.')) . $col, strtoupper($order))
            ->groupBy('ja.id')
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                ['filter' => '%' . $search . '%', 'id' => 2, 'today' => Carbon::today()]
            );
        $qb = $query->getQuery();

        $data = $qb->getResult(Query::HYDRATE_ARRAY);
        return $data;
    }

    public function countJobApplicationsTodayInt()
    {
        $query = $this->_em->createQueryBuilder();
        $query
            ->select('COUNT(DISTINCT(j.id)) as count')
            ->from("App\Entities\JobApplications", "j")
            ->leftJoin('j.stage', 'js')
            ->leftJoin('j.job_int', 'ji')
            ->where('js.id=:id')
            ->andWhere('ji.interview_date=:today')
            ->setParameters(['id' => 2, 'today' => Carbon::today()]);
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countFilteredRowsTodayInt($search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('COUNT(DISTINCT j.id) as count')
            ->from("App\Entities\JobApplications", "j")
            ->leftJoin('j.stage', 'js')
            ->leftJoin('j.job_id', 'ja')
            ->leftJoin('j.job_int', 'ji')
            ->where('js.id=:id')
            ->andWhere('ji.interview_date=:today')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('j.applicant_name', ':filter'),
                    $query->expr()->like('DATE(j.created_at)', ':filter'),
                    $query->expr()->like('ja.role', ':filter'),
                    $query->expr()->like('js.stage_name', ':filter')
                )
            )
            ->setParameters(['id' => 2, 'filter' => '%' . $search . '%', 'today' => Carbon::today()]);
        $qb = $query->getQuery();
        return $qb->getArrayResult();

    }

    public function getApplicationById($id)
    {
        $qry = $this->_em->createQueryBuilder()
            ->select('ja', 'j.id as job_id', 'j.role', 'j.exp_required', 'js.stage_name', 'js.id as stage_id')
            ->from('App\Entities\JobApplications', 'ja')
            ->leftJoin('ja.stage', 'js')
            ->leftJoin('ja.job_id', 'j')
            ->where('ja.id =:id')
            ->setParameter('id', $id);
        $qb = $qry->getQuery();
        return $qb->getArrayResult()[0];
    }

    public function updateApplication(JobApplications $jobApplications, $data)
    {
        if (isset($data['job_id'])) {
            $jobApplications->setJobId($data['job_id']);
        }
        if (isset($data['assoc_emp_id'])) {
            $jobApplications->setAssocEmpId($data['assoc_emp_id']);
        }
        if (isset($data['applicant_name'])) {
            $jobApplications->setApplicantName($data['applicant_name']);
        }
        if (isset($data['location'])) {
            $jobApplications->setLocation($data['location']);
        }
        if (isset($data['contact_email'])) {
            $jobApplications->setContactEmail($data['contact_email']);
        }
        if (isset($data['phone_number1'])) {
            $jobApplications->setPhoneNumber1($data['phone_number1']);
        }
        if (isset($data['phone_number2'])) {
            $jobApplications->setPhoneNumber2($data['phone_number2']);
        }
        if (isset($data['current_company'])) {
            $jobApplications->setCurrentCompany($data['current_company']);
        }
        if (isset($data['current_ctc'])) {
            $jobApplications->setCurrentCtc($data['current_ctc']);
        }
        if (isset($data['expected_ctc'])) {
            $jobApplications->setExpectedCtc($data['expected_ctc']);
        }
        if (isset($data['degree'])) {
            $jobApplications->setDegree($data['degree']);
        }
        if (isset($data['source'])) {
            $jobApplications->setSource($data['source']);
        }
        if (isset($data['resume'])) {
            $jobApplications->setResume($data['resume']);
        }
        if (isset($data['reject_reason'])) {
            $jobApplications->setRejectReason($data['reject_reason']);
        }

        if (isset($data['stage'])) {
            $jobApplications->setStage($data['stage']);
        }

        $this->_em->persist($jobApplications);
        $this->_em->flush();

        return $jobApplications->getId();
    }

    public function getApplicantInfo($id)
    {
        $qry = $this->_em->createQueryBuilder()
            ->select('ja,jo')
            ->from('App\Entities\JobApplications', 'ja')
            ->leftJoin('ja.job_id', 'jo')
            ->where('ja.id=:id')
            ->setParameter('id', $id)
            ->getQuery();

        return $qry->getArrayResult()[0];
    }

    public function countPendingjobs()
    {
        $qry = $this->_em->createQueryBuilder()
            ->select('count(ja) as count')
            ->from('App\Entities\JobApplications', 'ja')
            ->leftJoin('ja.stage', 'js')
            ->where('js.stage_name=:stage')
            ->setParameter('stage', 'initial')
            ->getQuery();

        return $qry->getArrayResult();
    }

    public function getScheduledCount()
    {
        $qry = $this->_em->createQueryBuilder()
            ->select('count(ja) as count')
            ->from('App\Entities\JobApplications', 'ja')
            ->leftJoin('ja.stage', 'js')
            ->where('js.id>:stage')
            ->setParameter('stage', 1)
            ->getQuery();

        return $qry->getArrayResult();
    }

    public function getTotalCandidatesCount()
    {
        $qry = $this->_em->createQueryBuilder()
            ->select('count(ja) as count')
            ->from('App\Entities\JobApplications', 'ja')
            ->getQuery();

        return $qry->getArrayResult();
    }
}
 
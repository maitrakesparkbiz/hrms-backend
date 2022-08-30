<?php

namespace App\Repositories;

use App\Entities\CandidatesInfo;
use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class CandidatesInfoRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\CandidatesInfo'));
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');
    }

    public function create(CandidatesInfo $candidatesInfo)
    {
        $this->_em->persist($candidatesInfo);
        $this->_em->flush();
        return $candidatesInfo->getId();
    }

    public function prepareData($data)
    {
        return new CandidatesInfo($data);
    }

    public function candidatesInfoOfId($id)
    {
        return $this->_em->getRepository('App\Entities\CandidatesInfo')->findOneBy([
            "id" => $id
        ]);
    }


    public function countCandidatesInfo()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(j.id) as count')
            ->from("App\Entities\CandidatesInfo", "j");
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countFilteredRowsAll($col, $order, $search, $start, $length, $expSearch, $cat_filter, $sal_filter, $sal)
    {
        $query = $this->_em->createQueryBuilder();
//        $query->select('count(ci.id) as count')
//            ->from("App\Entities\CandidatesInfo", "ci")
//            ->leftJoin('ci.category', 'ja')
//            ->where(
//                $query->expr()->orX(
//                    $query->expr()->like('ci.candidate_name', ':filter'),
//                    $query->expr()->like('DATE(ci.created_at)', ':filter'),
//                    $query->expr()->like('ja.role', ':filter'),
//                    $query->expr()->like('ci.expected_ctc', ':filter'),
//                    $query->expr()->like('ci.experiance', ':filter')
//                )
//            )
//            ->setParameter('filter', '%' . $search . '%');
//        $qb = $query->getQuery();
//        return $qb->getArrayResult();

        $query->select('count(ci.id) as count')
            ->from('App\Entities\CandidatesInfo', 'ci')
            ->leftJoin('ci.category', 'j')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('ci.candidate_name', ':filter'),
                    $query->expr()->like('DATE(ci.created_at)', ':filter'),
                    $query->expr()->like('j.role', ':filter'),
                    $query->expr()->like('ci.expected_ctc', ':filter'),
                    $query->expr()->like('ci.experiance', ':filter')
                )
//                $query->expr()->andX(
//                    $query->expr()->like('ci.experiance', ':expSearch'),
////                    $query->expr()->like('j.id', ':cat_filter')
//                )
            );
        if($expSearch!='' && $expSearch!=null)
        {
            $query->andWhere('ci.experiance = :expSearch')
                ->setParameter('expSearch', $expSearch);
        }
        if($cat_filter!=''&& $cat_filter!=null && $cat_filter !=0)
        {
            $query->andWhere('j.id = :cat_filter')
                ->setParameter('cat_filter',$cat_filter);
        }
        if (count($sal) > 0) {
            $query->andWhere('ci.expected_ctc >= :min_sal')
                ->setParameter('min_sal', intval($sal[0]));

            if ($sal[1] != 0) {
                $query->andWhere('ci.expected_ctc <= :max_sal')
                    ->setParameter('max_sal', intval($sal[1]));
            }
        }
        $query->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameter('filter' , '%' . $search . '%');
//                ->setParameters(
//                    [
//                        'filter' => '%' . $search . '%',
////                        'expSearch' => ($expSearch == null ? '%' : $expSearch),
////                        'cat_filter' => ($cat_filter == '0' ? '%' : $cat_filter)
//                    ]
//                );



        $qb = $query->getQuery();

        $data = $qb->getResult(Query::HYDRATE_ARRAY);
        return $data;

    }

    public function getAllCandidatesInfo($col, $order, $search, $start, $length, $expSearch, $cat_filter, $sal_filter, $sal)
    {


        $query = $this->_em->createQueryBuilder();
        $query->select('ci', 'j')
            ->from('App\Entities\CandidatesInfo', 'ci')
            ->leftJoin('ci.category', 'j')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('ci.candidate_name', ':filter'),
                    $query->expr()->like('DATE(ci.created_at)', ':filter'),
                    $query->expr()->like('j.role', ':filter'),
                    $query->expr()->like('ci.expected_ctc', ':filter'),
                    $query->expr()->like('ci.experiance', ':filter')
                )
//                $query->expr()->andX(
//                    $query->expr()->like('ci.experiance', ':expSearch'),
////                    $query->expr()->like('j.id', ':cat_filter')
//                )
            );
        if($expSearch!='' && $expSearch!=null)
        {
            $query->andWhere('ci.experiance = :expSearch')
                ->setParameter('expSearch', $expSearch);
        }
        if($cat_filter!=''&& $cat_filter!=null && $cat_filter !=0)
        {
            $query->andWhere('j.id = :cat_filter')
                ->setParameter('cat_filter',$cat_filter);
        }
        if (count($sal) > 0) {
            $query->andWhere('ci.expected_ctc >= :min_sal')
                    ->setParameter('min_sal', intval($sal[0]));

            if ($sal[1] != 0) {
                $query->andWhere('ci.expected_ctc <= :max_sal')
                    ->setParameter('max_sal', intval($sal[1]));
             }
        }
        $query->orderBy(($col == 'role' ? 'j.' : 'ci.') . $col, strtoupper($order));
        $query->setFirstResult($start)
                ->setMaxResults($length)
                ->setParameter('filter' , '%' . $search . '%');
//                ->setParameters(
//                    [
//                        'filter' => '%' . $search . '%',
////                        'expSearch' => ($expSearch == null ? '%' : $expSearch),
////                        'cat_filter' => ($cat_filter == '0' ? '%' : $cat_filter)
//                    ]
//                );



        $qb = $query->getQuery();

        $data = $qb->getResult(Query::HYDRATE_ARRAY);
        return $data;
    }



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


    public function getCandidatesInfoById($id)
    {
        $qry = $this->_em->createQueryBuilder()
            ->select('ja', 'j')
            ->from('App\Entities\CandidatesInfo', 'ja')
            ->leftJoin('ja.category', 'j')
            ->where('ja.id =:id')
            ->setParameter('id', $id);
        $qb = $qry->getQuery();
        return $qb->getArrayResult()[0];
    }


    public function updateCandidatesInfo(CandidatesInfo $candidatesInfo, $data)
    {
        if (isset($data['category'])) {
            $candidatesInfo->setCategory($data['category']);
        }
        if (isset($data['candidate_name'])) {
            $candidatesInfo->setCandidateName($data['candidate_name']);
        }
        if (isset($data['contact_email'])) {
            $candidatesInfo->setContactEmail($data['contact_email']);
        }
        if (isset($data['current_company'])) {
            $candidatesInfo->setCurrentCompany($data['current_company']);
        }
        if (isset($data['current_ctc'])) {
            $candidatesInfo->setCurrentCtc($data['current_ctc']);
        }
        if (isset($data['expected_ctc'])) {
            $candidatesInfo->setExpectedCtc($data['expected_ctc']);
        }
        if (isset($data['experiance'])) {
            $candidatesInfo->setExperiance($data['experiance']);
        }
        if (isset($data['phone_number'])) {
            $candidatesInfo->setPhoneNumber($data['phone_number']);
        }
        if (isset($data['resume'])) {
            $candidatesInfo->setResume($data['resume']);
        }


        $this->_em->persist($candidatesInfo);
        $this->_em->flush();

        return $candidatesInfo->getId();
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

}

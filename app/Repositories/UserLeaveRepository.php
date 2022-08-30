<?php

namespace App\Repositories;

use App\Entities\UserLeave;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class UserLeaveRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\UserLeave'));
    }

    public function prepareData($data)
    {
        return new UserLeave($data);
    }

    public function create(UserLeave $userLeave)
    {
        $this->_em->persist($userLeave);
        $this->_em->flush();

        return $userLeave;
    }

    public function UserLeaveOfId($id)
    {
        return $this->_em->getRepository('App\Entities\UserLeave')->find([
            'id' => $id
        ]);
    }

    public function update(UserLeave $userLeave, $data)
    {
        if (isset($data['cl'])) {
            $userLeave->setCl($data['cl']);
        }
        if (isset($data['pl'])) {
            $userLeave->setPl($data['pl']);
        }
        if (isset($data['sl'])) {
            $userLeave->setSl($data['sl']);
        }
        if (isset($data['used_upl'])) {
            $userLeave->setUsedUpl($data['used_upl']);
        }
        if (isset($data['employment_started'])) {
            $userLeave->setEmploymentStarted($data['employment_started']);
        }
        if (isset($data['one_year_completed'])) {
            $userLeave->setOneYearCompleted($data['one_year_completed']);
        }

        $this->_em->persist($userLeave);
        $this->_em->flush();

        return $userLeave;
    }

    public function getAllUsersWithLeaves()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('u.id, u.firstname, u.lastname, u.employee_id, u.profile_image, u.on_training, u.probation_end_date,ul')
            ->from('App\Entities\UserLeave', 'ul')
            ->leftJoin('ul.emp_id', 'u')
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getLeaveIdByEmp($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('ul.id')
            ->from('App\Entities\UserLeave', 'ul')
            ->leftJoin('ul.emp_id', 'u')
            ->where('u.id=:id')
            ->setParameter('id', $emp_id)
            ->getQuery();

        return $query->getArrayResult()[0];
    }

    public function getLeaveByEmp($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('ul')
            ->from('App\Entities\UserLeave', 'ul')
            ->leftJoin('ul.emp_id', 'u')
            ->where('u.id=:id')
            ->setParameter('id', $emp_id)
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getRegularUsers()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('ul')
            ->from('App\Entities\UserLeave', 'ul')
            ->where('ul.employment_started=:status')
            ->andWhere('ul.one_year_completed=:status')
            ->setParameter('status', 1)
            ->getQuery();

        return $query->getArrayResult();
    }

    public function countAllUserLeaves()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(ul.id) as count')
            ->from("App\Entities\UserLeave", "ul")
            ->leftJoin('ul.emp_id', 'u')
            ->where('u.user_exit_status=:status')
            // ->groupBy('u.id')
            ->setParameters([ 'status' => 1]);
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function countFilteredRows($search){
        $query = $this->_em->createQueryBuilder();
        $query->select('count(ul.id) as count,(ul.cl + ul.pl + ul.sl) as balance, u.firstname, u.lastname, u.user_exit_status')
            ->from("App\Entities\UserLeave", "ul")
            ->leftJoin('ul.emp_id', 'u')
            ->having(
                $query->expr()->orX(
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                    $query->expr()->like('balance', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->eq('u.user_exit_status', ':status')
                )
            )
            ->setParameters(['filter' => '%' . $search . '%', 'status' => 1])
            ->groupBy('ul.id');
        $qb = $query->getQuery();

        return $qb->getArrayResult();

    }

    public function getAllUserLeaves($col, $order, $search, $start, $length)
    {
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');

        $query = $this->_em->createQueryBuilder();
        $query->select("ul.id,
         em.id as emp_id,
         em.firstname,
         em.lastname,
         em.profile_image, 
         em.user_exit_status,
         (ul.cl + ul.pl + ul.sl) as balance,
         ul.cl,
         ul.pl,
         ul.sl,
         ul.used_upl,
         ul.id
         ")
            ->from("App\Entities\UserLeave", "ul")
            ->leftJoin('ul.emp_id', 'em')
            
            ->having(
                $query->expr()->orX(
                    $query->expr()->like("concat(em.firstname,' ',em.lastname)", ':filter'),
                    $query->expr()->like('balance', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->eq('em.user_exit_status', ':status')
                )
            )
            ->orderBy(($col == 'balance' ? 'balance' : 'em.firstname'), strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(['filter' => '%' . $search . '%', 'status' => 1])
            ->groupBy('ul.id');            

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }
    public function getLeaveBalance($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('ul,em')
            ->from('App\Entities\UserLeave', 'ul')
            ->leftJoin('ul.emp_id', 'em')
            ->where('ul.id=:id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getArrayResult()[0];
    }
}
 

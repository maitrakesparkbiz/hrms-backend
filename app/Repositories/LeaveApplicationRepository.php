<?php

namespace App\Repositories;

use App\Entities\LeaveApplication;
use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class LeaveApplicationRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\LeaveApplication'));
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');
    }

    public function prepareData($data)
    {
        return new LeaveApplication($data);
    }

    public function create(LeaveApplication $leaveApplication)
    {
        $this->_em->persist($leaveApplication);
        $this->_em->flush();

        return $leaveApplication;
    }

    public function delete(LeaveApplication $expense)
    {
        $this->_em->remove($expense);
        $this->_em->flush();
    }

    public function Leave_applicationOfId($id)
    {
        return $this->_em->getRepository('App\Entities\LeaveApplication')->findOneBy([
            "id" => $id
        ]);
    }

    public function getLeaveTypeById($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select("l,g,s,ou")
            ->from("App\Entities\Leave_type", "l")
            ->leftJoin("l.gender", "g")
            ->leftJoin("l.status", "s")
            ->leftJoin("l.over_utilization", "ou")
            ->where("l.id = :id")
            ->setParameter("id", $id)
            ->getQuery();
        return $query->getArrayResult([0]);
    }

    public function getAllLeave_application($col, $order, $search, $start, $length, $duration, $endDuration, $status)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("l,u,le,ou")
            ->from("App\Entities\LeaveApplication", "l")
            ->leftJoin("l.user_id", "u")
            ->leftJoin("l.leave_type", "le")
            ->leftJoin('le.over_utilization', 'ou')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                    $query->expr()->like('l.leave_date', ':filter'),
                    $query->expr()->like('l.reason', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->like('l.status', ':status')
                ),
                $query->expr()->andX(
                    $query->expr()->gte('l.leave_date', ':duration')
                ),
                $query->expr()->andX(
                    $query->expr()->lte('l.leave_date', ':endDuration')
                )
            )
            ->orderBy(($col == 'firstname' ? 'u.' : 'l.') . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                [
                    'filter' => '%' . $search . '%',
                    'status' => ($status == 'all' ? '%' : $status),
                    'duration' => $duration == 'all' ? '%' : $duration,
                    'endDuration' => $endDuration == '' ? $this->getMaxLeaveDate() : $endDuration
                ]
            );
        //            ->orderBy('l.created_at', 'DESC');

        $qb = $query->getQuery();

        $data = $qb->getResult(Query::HYDRATE_ARRAY);
        return $data;
    }

    public function getMaxLeaveDate()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('MAX(l.leave_date) as leave_date')
            ->from("App\Entities\LeaveApplication", "l")
            ->getQuery();

        return $query->getArrayResult()[0]['leave_date'];
    }

    public function countAllRow()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(l.id) as count')
            ->from("App\Entities\LeaveApplication", "l");
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countFilteredRow($status, $endDuration, $duration, $search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(l.id) as count')
            ->from("App\Entities\LeaveApplication", "l")
            ->leftJoin("l.user_id", "u")
            ->where(
                $query->expr()->orX(
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                    $query->expr()->like('l.leave_date', ':filter'),
                    $query->expr()->like('l.reason', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->like('l.status', ':status')
                ),
                $query->expr()->andX(
                    $query->expr()->gte('l.leave_date', ':duration')
                ),
                $query->expr()->andX(
                    $query->expr()->lte('l.leave_date', ':endDuration')
                )
            )
            ->setParameters([
                'status' => ($status == 'all' ? '%' : $status),
                'duration' => $duration == 'all' ? '%' : $duration,
                'endDuration' => $endDuration == '' ? $this->getMaxLeaveDate() : $endDuration,
                'filter' => '%' . $search . '%'
            ]);
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function getLeave_applicationById($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select("l,u,le")
            ->from("App\Entities\LeaveApplication", "l")
            ->leftJoin("l.user_id", "u")
            ->leftJoin("l.leave_type", "le")
            ->where("l.id = :id")
            ->setParameter("id", $id)
            ->getQuery();
        return $query->getArrayResult([0]);
    }

    public function getFirstApprovedApplications($col, $order, $search, $start, $length, $duration, $endDuration)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("l,u,le")
            ->from("App\Entities\LeaveApplication", "l")
            ->leftJoin("l.user_id", "u")
            ->leftJoin("l.leave_type", "le")
            ->where('l.status=:status')
            //            ->andWhere('l.final_approve=:flag')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                    $query->expr()->like('l.leave_date', ':filter'),
                    $query->expr()->like('l.reason', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->gte('l.leave_date', ':duration')
                ),
                $query->expr()->andX(
                    $query->expr()->lte('l.leave_date', ':endDuration')
                )
            )
            ->orderBy(($col == 'firstname' ? 'u.' : 'l.') . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                [
                    'filter' => '%' . $search . '%',
                    'duration' => $duration == 'all' ? '%' : $duration,
                    'endDuration' => $endDuration == '' ? $this->getMaxLeaveDate() : $endDuration,
                    'status' => 'Accept'
                    //                    'flag' => 0
                ]
            );
        //            ->orderBy('l.created_at', 'DESC');

        $qb = $query->getQuery();

        $data = $qb->getResult(Query::HYDRATE_ARRAY);
        return $data;
    }


    public function countFirstApprovedRows()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(l.id) as count')
            ->from("App\Entities\LeaveApplication", "l")
            ->where('l.status=:status')
            ->setParameter('status', 'Accept');

        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countFirstApprovedFilteredRows($endDuration, $duration, $search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(l.id) as count')
            ->from("App\Entities\LeaveApplication", "l")
            ->leftJoin("l.user_id", "u")
            ->where(
                $query->expr()->orX(
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                    $query->expr()->like('l.leave_date', ':filter'),
                    $query->expr()->like('l.reason', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->eq('l.status', ':status')
                ),
                $query->expr()->andX(
                    $query->expr()->gte('l.leave_date', ':duration')
                ),
                $query->expr()->andX(
                    $query->expr()->lte('l.leave_date', ':endDuration')
                )
            )
            ->setParameters([
                'status' => 'Accept',
                'duration' => $duration == 'all' ? '%' : $duration,
                'endDuration' => $endDuration == '' ? $this->getMaxLeaveDate() : $endDuration,
                'filter' => '%' . $search . '%'
            ]);
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function getuserLeave($data)
    {
        $query = $this->_em->createQueryBuilder()
            ->select("l,u,le")
            ->from("App\Entities\LeaveApplication", "l")
            ->leftJoin("l.user_id", "u")
            ->leftJoin("l.leave_type", "le")
            ->where("u.id = :id")
            ->setParameter("id", $data['user_id'])
            ->andWhere('l.leave_date BETWEEN :from AND :to')
            ->orderBy('l.created_at', 'DESC')
            ->setParameter('from', $data['startdate']->format('Y-m-d'))
            ->setParameter('to', $data['enddate']->format('Y-m-d'))
            ->getQuery();
        $data = $query->getResult(Query::HYDRATE_ARRAY);

        return $data;
    }

    public function getEmployeeleaves($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('la,u,lt')
            ->from('App\Entities\LeaveApplication', 'la')
            ->leftJoin('la.leave_type', 'lt')
            ->leftJoin('la.user_id', 'u')
            ->where('u.id=:id')
            ->orderBy('la.created_at', 'DESC')
            ->setParameter('id', $emp_id)
            ->getQuery();

        return $query->getArrayResult();
    }
public function getFilterRecords($emp_id, $status, $endDuration, $duration, $search)
{
    $query = $this->_em->createQueryBuilder();
    $query->select('count(l.id) as count')
        ->from("App\Entities\LeaveApplication", "l")
        ->leftJoin('l.leave_type', 'lt')
        ->where('l.user_id=:emp_id')
        ->andWhere(
            $query->expr()->orX(
                $query->expr()->like("lt.leavetype", ':filter'),
                $query->expr()->like('l.leave_date', ':filter'),
                $query->expr()->like('l.leave_count', ':filter')
            ),
            $query->expr()->andX(
                $query->expr()->like('l.status', ':status')
            ),
            $query->expr()->andX(
                $query->expr()->gte('l.leave_date', ':duration')
            ),
            $query->expr()->andX(
                $query->expr()->lte('l.leave_date', ':endDuration')
            )
        )
        ->setParameters([
            'status' => ($status == 'all' ? '%' : $status),
            'duration' => $duration == 'all' ? '%' : $duration,
            'endDuration' => $endDuration == '' ? $this->getMaxLeaveDate() : $endDuration,
            'emp_id' => $emp_id,
            'filter' => '%' . $search . '%'
        ]);

    $qb = $query->getQuery();
    return $qb->getArrayResult();
}

    public function countEmployeeLeaves($emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(l.id) as count')
            ->from("App\Entities\LeaveApplication", "l")
            ->where('l.user_id=:emp_id')
            ->setParameters([
                'emp_id' => $emp_id
            ]);

        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function getEmployeeLeavesDatatable($emp_id, $col, $order, $search, $start, $length, $duration, $endDuration, $status)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("la,lt")
            ->from('App\Entities\LeaveApplication', 'la')
            ->leftJoin('la.leave_type', 'lt')
            ->leftJoin('la.user_id', 'u')
            ->where('u.id=:id')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like("lt.leavetype", ':filter'),
                    $query->expr()->like('la.leave_date', ':filter'),
                    $query->expr()->like('la.leave_count', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->like('la.status', ':status')
                ),
                $query->expr()->andX(
                    $query->expr()->gte('la.leave_date', ':duration')
                ),
                $query->expr()->andX(
                    $query->expr()->lte('la.leave_date', ':endDuration')
                )
            )
            ->orderBy(($col == 'leavetype' ? 'lt.' : 'la.') . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                [
                    'filter' => '%' . $search . '%',
                    'status' => ($status == 'all' ? '%' : $status),
                    'duration' => $duration == 'all' ? '%' : $duration,
                    'endDuration' => $endDuration == '' ? $this->getMaxLeaveDate() : $endDuration,
                    'id' => $emp_id
                ]
            );

        $qb = $query->getQuery();

        $data = $qb->getResult(Query::HYDRATE_ARRAY);
        return $data;
    }

    public function update(LeaveApplication $leave_application, $data)
    {
        if (isset($data["user_id"])) {
            $leave_application->setUserId($data["user_id"]);
        }
        if (isset($data["leave_type"])) {
            $leave_application->setLeave_type($data["leave_type"]);
        }
        if (isset($data["leave_date"])) {
            $leave_application->setLeaveDate($data["leave_date"]);
        }
        if (isset($data["half_day"])) {
            $leave_application->setHalf_day($data["half_day"]);
        }
        if (isset($data["reason"])) {
            $leave_application->setReason($data["reason"]);
        }
        if (isset($data["status"])) {
            $leave_application->setStatus($data["status"]);
        }
        if (isset($data["leave_count"])) {
            $leave_application->setLeave_count($data["leave_count"]);
        }
        if (isset($data['final_approve'])) {
            $leave_application->setFinalApprove($data['final_approve']);
        }
        if (isset($data['reject_reason'])) {
            $leave_application->setRejectReason($data['reject_reason']);
        }

        $this->_em->persist($leave_application);
        $this->_em->flush();

        return $leave_application;
    }

    public function getTodayLeaves()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('la,u,lt,lt.leavetype,u.id as emp_id, u.firstname, u.lastname, u.profile_image,d.name as designation,l.name as batch')
            ->from('App\Entities\LeaveApplication', 'la')
            ->leftJoin('la.leave_type', 'lt')
            ->leftJoin('la.user_id', 'u')
            ->leftJoin('u.designation', 'd')
            ->leftJoin('u.location', 'l')
            ->where('la.leave_date=:today')
            ->andWhere('la.status=:status')
            ->andWhere('la.final_approve=:flag')
            ->setParameter('today', Carbon::today())
            ->setParameter('status', 'Accept')
            ->setParameter('flag', 0)
            ->getQuery();
        return $query->getArrayResult();
    }

    public function getRecentLeaves()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('la,lt.leavetype,u.id as emp_id, u.firstname, u.lastname, u.profile_image,d.name as designation,l.name as batch')
            ->from('App\Entities\LeaveApplication', 'la')
            ->leftJoin('la.leave_type', 'lt')
            ->leftJoin('la.user_id', 'u')
            ->leftJoin('u.designation', 'd')
            ->leftJoin('u.location', 'l')
            ->where('la.leave_date>=:today')
            ->andWhere('la.leave_date<=:end_date')
            ->andWhere('la.status IN (:status)')
            ->setParameters(['today' => Carbon::today(), 'end_date' => Carbon::today()->addDays(15), 'status' => ['Pending', 'Accept']])
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getLeavesSelfDashboard($date, $emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('la,lt.leavetype,u.firstname, u.lastname,u.id')
            ->from('App\Entities\LeaveApplication', 'la')
            ->leftJoin('la.user_id', 'u')
            ->leftJoin('la.leave_type', 'lt')
            ->where('la.leave_date>=:start')
            ->andWhere('la.leave_date<=:end')
            ->andWhere('la.user_id=:emp_id')
            ->orderBy('la.leave_date')
            ->setParameters(['start' => Carbon::parse($date)->startOfMonth(), 'end' => Carbon::parse($date)->endOfMonth(), 'emp_id' => $emp_id])
            ->getQuery();

        $leaveApplication = $query->getArrayResult();

        $leave_approved = $this->_em->createQueryBuilder()
            ->select('la,lt.leavetype,u.firstname, u.lastname,u.id')
            ->from('App\Entities\LeaveApproved', 'la')
            ->leftJoin('la.user_id', 'u')
            ->leftJoin('la.leave_type', 'lt')
            ->where('la.leave_date>=:start')
            ->andWhere('la.leave_date<=:end')
//            ->andWhere('la.status!=:status')
            ->andWhere('la.user_id=:emp_id')
            ->andWhere('la.leave_id IS NULL')
            ->orderBy('la.leave_date')
            ->setParameters(['start' => Carbon::parse($date)->startOfMonth(), 'end' => Carbon::parse($date)->endOfMonth(), 'emp_id' => $emp_id])
            ->getQuery();
        $leaveApproved = $leave_approved->getArrayResult();
        if(count($leaveApproved)>0) {
            return array_merge($leaveApplication, $leaveApproved);
        }
        return $leaveApplication;

    }
    public function getLeavesSelfTeamLeaderDashboard($date, $emp_id)
    {
        $query1 = $this->_em->createQueryBuilder()
            ->select('me.id')
            ->from('App\Entities\Team', 't')
            ->leftJoin('t.team_employee', 'te')
            ->leftJoin('te.member', 'me')
            ->where('t.leader =:leader')
//            ->andWhere('t.id=te.team_id')
            ->setParameters(['leader' => $emp_id])
            ->getQuery();
        $member = $query1->getArrayResult();
        $member = array_map(function($member)
        {
            return $member['id'];
        }, $member);
        if($member) {
            $query = $this->_em->createQueryBuilder()
                ->select('la,lt.leavetype,u.firstname, u.lastname,u.id')
                ->from('App\Entities\LeaveApplication', 'la')
                ->leftJoin('la.user_id', 'u')
                ->leftJoin('la.leave_type', 'lt')
                ->where('la.leave_date>=:start')
                ->andWhere('la.leave_date<=:end');
//                ->andWhere('la.status!=:status');
//            ->andWhere('la.user_id=:emp_id');

            $query->andWhere($query->expr()->in('u.id', $member));
            $query->orderBy('la.leave_date')
                ->setParameters(['start' => Carbon::parse($date)->startOfMonth(), 'end' => Carbon::parse($date)->endOfMonth()]);


            $qb = $query->getQuery();
            $leaveApplication= $qb->getArrayResult();
//            return $leaveApplication;
            $leave_approved = $this->_em->createQueryBuilder()
                ->select('la,lt.leavetype,u.firstname, u.lastname,u.id')
                ->from('App\Entities\LeaveApproved', 'la')
                ->leftJoin('la.user_id', 'u')
                ->leftJoin('la.leave_type', 'lt')
                ->where('la.leave_date>=:start')
                ->andWhere('la.leave_date<=:end')
//            ->andWhere('la.status!=:status')
//                ->andWhere('la.user_id=:emp_id')
                ->andWhere('la.leave_id IS NULL');
            $leave_approved->andWhere($query->expr()->in('u.id', $member));
            $leave_approved->orderBy('la.leave_date')
                ->setParameters(['start' => Carbon::parse($date)->startOfMonth(), 'end' => Carbon::parse($date)->endOfMonth()]);
            $qb1= $leave_approved->getQuery();
            $leaveApproved = $qb1->getArrayResult();
            if(count($leaveApproved)>0) {
                return array_merge($leaveApplication, $leaveApproved);
            }
            return $leaveApplication;
        }
    }

    public function getUpcomingLeavesMember($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('la.leave_date,la.reason,la.half_day')
            ->from('App\Entities\LeaveApplication', 'la')
            ->leftJoin('la.user_id', 'u')
            ->leftJoin('la.leave_type', 'lt')
            ->where('DATE(la.leave_date)>=:start')
            ->andWhere('DATE(la.leave_date)<=:end')
            ->andWhere('la.status!=:status')
            ->andWhere('la.user_id=:emp_id')
            ->orderBy('la.leave_date')
            ->setParameters(['start' => Carbon::today(), 'end' => Carbon::today()->addDays(7), 'status' => 'Reject', 'emp_id' => $emp_id])
            ->getQuery();

        return $query->getArrayResult();
    }

    public function countPendingLeaves()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('count(la) as count')
            ->from('App\Entities\LeaveApplication', 'la')
            ->where('la.status=:status')
            ->setParameter('status', 'Pending')
            ->getQuery();

        return $query->getArrayResult();
    }

    public function countTodayLeaves()
    {
        $qry = $this->_em->createQueryBuilder()
            ->select('count(l.id) as count')
            ->from("App\Entities\LeaveApplication", "l")
            ->where('l.leave_date=:today')
            ->andWhere('l.status=:status')
            ->andWhere('l.final_approve=:flag')
            ->setParameters(['today' => Carbon::today(), 'status' => 'Accept', 'flag' => 0])
            ->getQuery();

        return $qry->getArrayResult();
    }

    public function getYearMonthLeaves($date)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('la,lt.leavetype,u.id as emp_id, u.firstname, u.lastname, u.profile_image')
            ->from('App\Entities\LeaveApplication', 'la')
            ->leftJoin('la.user_id', 'u')
            ->leftJoin('la.leave_type', 'lt')
            ->where('la.leave_date>=:start')
            ->andWhere('la.leave_date<=:end')
            ->andWhere('la.status!=:status')
            ->andWhere('la.status!=:statusCancel')
            ->andWhere('la.final_approve=:flag')
            ->orderBy('la.leave_date')
            ->setParameters(['start' => Carbon::parse($date)->startOfMonth(), 'end' => Carbon::parse($date)->endOfMonth(), 'status' => 'Reject','statusCancel'=>'Cancel', 'flag' => 0])
            ->getQuery();

        return $query->getArrayResult();
    }
}

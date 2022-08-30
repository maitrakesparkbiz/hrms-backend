<?php

namespace App\Repositories;

use App\Entities\LeaveApproved;
use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class LeaveApprovedRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\LeaveApproved'));
    }

    public function prepareData($data)
    {
        return new LeaveApproved($data);
    }

    public function create(LeaveApproved $leaveApproved)
    {
        $this->_em->persist($leaveApproved);
        $this->_em->flush();

        return $leaveApproved;
    }

    public function LeaveApprovedOfId($id)
    {
        return $this->_em->getRepository('App\Entities\LeaveApproved')->findOneBy([
            'id' => $id
        ]);
    }

    //    public function getAllApprovedLeaves()
    //    {
    //        $query = $this->_em->createQueryBuilder()
    //            ->select('la', 'u', 'lt')
    //            ->from('App\Entities\LeaveApproved', 'la')
    //            ->leftJoin('la.user_id', 'u')
    //            ->leftJoin('la.leave_type', 'lt')
    //            ->where('la.status=:status')
    //            ->andWhere('la.is_deleted=:deleted')
    //            ->orderBy('la.created_at', 'DESC')
    //            ->setParameter('status', 'Accept')
    //            ->setParameter('deleted', 0)
    //            ->getQuery();
    //
    //        return $query->getArrayResult();
    //    }


    public function countLeavesBetweenDates($mainDate, $userId)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('count(l.id) as count')
            ->from("App\Entities\LeaveApproved", "l")
            ->where('l.leave_date >= :mainDate')
            ->andWhere('l.user_id=:user_id')
            ->setParameter('mainDate', $mainDate)
            ->setParameter('user_id', $userId)
            ->getQuery();

        return $query->getArrayResult();
    }

    public function countAllFilteredRows($endDuration, $duration, $search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(l.id) as count')
            ->from("App\Entities\LeaveApproved", "l")
            ->leftJoin("l.user_id", "u")
            ->where(
                $query->expr()->orX(
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                    $query->expr()->like('l.leave_date', ':filter'),
                    $query->expr()->like('l.reason', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->eq('l.is_deleted', ':deleted')
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
                'duration' => $duration == 'all' ? '%' : $duration,
                'endDuration' => $endDuration == '' ? $this->getMaxLeaveDate() : $endDuration,
                'status' => 'Accept',
                'deleted' => 0,
                'filter' => '%' . $search . '%'
            ]);
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function getAllApprovedLeaves($col, $order, $search, $start, $length, $duration, $endDuration)
    {

        $query = $this->_em->createQueryBuilder();
        $query->select("l,u,le")
            ->from("App\Entities\LeaveApproved", "l")
            ->leftJoin("l.user_id", "u")
            ->leftJoin("l.leave_type", "le")
            ->where('l.status=:status')
            ->andWhere('l.is_deleted=:deleted')
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
                    'status' => 'Accept',
                    'deleted' => 0,
                    'filter' => '%' . $search . '%',
                    'duration' => $duration == 'all' ? '%' : $duration,
                    'endDuration' => $endDuration == '' ? $this->getMaxLeaveDate() : $endDuration
                ]
            );

        $qb = $query->getQuery();

        $data = $qb->getResult(Query::HYDRATE_ARRAY);
        return $data;
    }



    public function countEmpAllTakenLeaves($emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(l.id) as count')
            ->from("App\Entities\LeaveApproved", "l")
            ->where('l.user_id=:emp_id')
            ->andWhere(
                $query->expr()->andX(
                    $query->expr()->eq('l.is_deleted', ':deleted')
                ),
                $query->expr()->andX(
                    $query->expr()->eq('l.status', ':status')
                )
            )
            ->setParameters([
                'status' => 'Accept',
                'deleted' => 0,
                'emp_id' => $emp_id
            ]);
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countEmpFilteredRows($emp_id, $endDuration, $duration, $search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(l.id) as count')
            ->from("App\Entities\LeaveApproved", "l")
            ->leftJoin('l.leave_type', 'lt')
            ->where('l.user_id=:emp_id')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like("lt.leavetype", ':filter'),
                    $query->expr()->like('l.leave_date', ':filter'),
                    $query->expr()->like('l.leave_count', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->eq('l.is_deleted', ':deleted')
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
                'duration' => $duration == 'all' ? '%' : $duration,
                'endDuration' => $endDuration == '' ? $this->getMaxLeaveDate() : $endDuration,
                'status' => 'Accept',
                'deleted' => 0,
                'emp_id' => $emp_id,
                'filter' => '%' . $search . '%'
            ]);
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function sumEmployeeTakenLeaves($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('la.leave_count')
            ->from('App\Entities\LeaveApproved', 'la')
            ->leftJoin('la.leave_type', 'lt')
            ->leftJoin('la.user_id', 'u')
            ->where('u.id=:id')
            ->andWhere('la.status=:status')
            ->andWhere('la.is_deleted=:deleted')
            ->orderBy('la.created_at', 'DESC')
            ->setParameter('id', $emp_id)
            ->setParameter('status', 'Accept')
            ->setParameter('deleted', 0)
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getEmpAllTakenLeaves($emp_id, $col, $order, $search, $start, $length, $duration, $endDuration)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('la,lt')
            ->from('App\Entities\LeaveApproved', 'la')
            ->leftJoin('la.leave_type', 'lt')
            ->leftJoin('la.user_id', 'u')
            ->where('u.id=:id')
            ->andWhere('la.status=:status')
            ->andWhere('la.is_deleted=:deleted')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like("lt.leavetype", ':filter'),
                    $query->expr()->like('la.leave_date', ':filter'),
                    $query->expr()->like('la.leave_count', ':filter')
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
            ->setParameters([
                'id' => $emp_id,
                'status' => 'Accept',
                'deleted' => 0,
                'filter' => '%' . $search . '%',
                'duration' => $duration == 'all' ? '%' : $duration,
                'endDuration' => $endDuration == '' ? $this->getMaxLeaveDate() : $endDuration
            ])
            ->getQuery();

        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countAllRow()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(l.id) as count')
            ->from("App\Entities\LeaveApproved", "l")
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('l.is_deleted', ':deleted')
                ),
                $query->expr()->andX(
                    $query->expr()->eq('l.status', ':status')
                )
            )
            ->setParameters([
                'status' => 'Accept',
                'deleted' => 0,
            ]);
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function getMaxLeaveDate()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('MAX(l.leave_date) as leave_date')
            ->from("App\Entities\LeaveApproved", "l")
            ->getQuery();

        return $query->getArrayResult()[0]['leave_date'];
    }

    public function getEmpFinalLeaves($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('la,lt')
            ->from('App\Entities\LeaveApproved', 'la')
            ->leftJoin('la.leave_type', 'lt')
            ->leftJoin('la.user_id', 'u')
            ->where('u.id=:id')
            ->andWhere('la.status=:status')
            ->andWhere('la.is_deleted=:deleted')
            ->orderBy('la.created_at', 'DESC')
            ->setParameter('id', $emp_id)
            ->setParameter('status', 'Accept')
            ->setParameter('deleted', 0)
            ->getQuery();

        return $query->getArrayResult();
    }

    function getSelfEmpFinalLeaves($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('la.id,la.leave_date, la.leave_count,lt.leavetype')
            ->from('App\Entities\LeaveApproved', 'la')
            ->leftJoin('la.leave_type', 'lt')
            ->leftJoin('la.user_id', 'u')
            ->where('u.id=:id')
            ->andWhere('la.status=:status')
            ->andWhere('la.is_deleted=:deleted')
            ->orderBy('la.created_at', 'DESC')
            ->setParameter('id', $emp_id)
            ->setParameter('status', 'Accept')
            ->setParameter('deleted', 0)
            ->getQuery();

        return $query->getArrayResult();
    }

    function getTodayLeaves()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('la,u,lt,lp,lt.leavetype,u.id as emp_id, u.firstname, u.lastname, u.profile_image,d.name as designation,l.name as batch')
            ->from('App\Entities\LeaveApproved', 'la')
            ->leftJoin('la.leave_id', 'lp')
            ->leftJoin('la.leave_type', 'lt')
            ->leftJoin('la.user_id', 'u')
            ->leftJoin('u.designation', 'd')
            ->leftJoin('u.location', 'l')
            ->where('la.leave_date=:today')
            ->andWhere('la.status=:status')
            ->setParameter('today', Carbon::today())
            ->setParameter('status', 'Accept')
            ->getQuery();
        return $query->getArrayResult();
    }

    public function getRejectedLeaves()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('la,u,le')
            ->from('App\Entities\LeaveApproved', 'la')
            ->leftJoin("la.user_id", "u")
            ->leftJoin("la.leave_type", "le")
            ->where('la.status=:status')
            ->orderBy('la.created_at', 'DESC')
            ->setParameter('status', 'Reject')
            ->getQuery();
        return $query->getArrayResult();
    }

    public function getEmpUPL($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('la,u,le')
            ->from('App\Entities\LeaveApproved', 'la')
            ->leftJoin('la.user_id', 'u')
            ->leftJoin('la.leave_type', 'le')
            ->where('le.leavetype=:leavetype')
            ->andWhere('u.id=:id')
            ->andWhere('la.is_deleted=:deleted')
            ->andWhere('la.status=:status')
            ->orderBy('la.created_at', 'DESC')
            ->setParameters(['leavetype' => 'UPL', 'id' => $emp_id, 'deleted' => 0, 'status' => 'Accept'])
            ->getQuery();

        return $query->getArrayResult();
    }

    public function update(LeaveApproved $leaveApproved, $data)
    {
        if (isset($data["user_id"])) {
            $leaveApproved->setUserId($data["user_id"]);
        }
        if (isset($data["leave_type"])) {
            $leaveApproved->setLeaveType($data["leave_type"]);
        }
        if (isset($data["leave_date"])) {
            $leaveApproved->setLeaveDate($data["leave_date"]);
        }
        if (isset($data["half_day"])) {
            $leaveApproved->setHalfDay($data["half_day"]);
        }
        if (isset($data["reason"])) {
            $leaveApproved->setReason($data["reason"]);
        }
        if (isset($data["status"])) {
            $leaveApproved->setStatus($data["status"]);
        }
        if (isset($data["leave_count"])) {
            $leaveApproved->setLeaveCount($data["leave_count"]);
        }
        if (isset($data['reject_reason'])) {
            $leaveApproved->setRejectReason($data['reject_reason']);
        }
        if (isset($data['is_deleted'])) {
            $leaveApproved->setIsDeleted($data['is_deleted']);
        }
        if (isset($data['leave_id'])) {
            $leaveApproved->setLEaveId($data['leave_id']);
        }
        $this->_em->persist($leaveApproved);
        $this->_em->flush();

        return $leaveApproved;
    }

    public function getUserLeaveByDate($emp_id, $start, $end)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('la')
            ->from('App\Entities\LeaveApproved', 'la')
            ->leftJoin('la.user_id', 'u')
            ->where('u.id=:emp_id')
            ->andWhere('la.leave_date>=:start')
            ->andWhere('la.leave_date<=:end')
            ->andWhere('la.is_deleted=:deleted')
            ->setParameters(['emp_id' => $emp_id, 'start' => $start, 'end' => $end, 'deleted' => 0])
            ->getQuery();

        return $query->getArrayResult();
    }
    public function getTeamLeaveByDate($emp_id, $start, $end)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('la')
            ->from('App\Entities\LeaveApplication', 'la')
            ->leftJoin('la.user_id', 'u')
            ->where('u.id=:emp_id')
            ->andWhere('la.leave_date>=:start')
            ->andWhere('la.leave_date<=:end')
            ->setParameters(['emp_id' => $emp_id, 'start' => $start, 'end' => $end])
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getUserLeaveProductivity($emp_id, $start, $end)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('la')
            ->from('App\Entities\LeaveApproved', 'la')
            ->leftJoin('la.user_id', 'u')
            ->where('u.id=:emp_id')
            ->andWhere('la.leave_date>=:start')
            ->andWhere('la.leave_date<=:end')
            ->andWhere('la.is_deleted=:deleted')
            ->setParameters(['emp_id' => $emp_id, 'start' => $start, 'end' => $end, 'deleted' => 0])
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getYearMonthFinalLeaves($date)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('la,lp,lt.leavetype,u.id as emp_id, u.firstname, u.lastname, u.profile_image')
            ->from('App\Entities\LeaveApproved', 'la')
            ->leftJoin('la.user_id', 'u')
            ->leftJoin('la.leave_type', 'lt')
            ->leftJoin('la.leave_id', 'lp')
            ->where('la.leave_date>=:start')
            ->andWhere('la.leave_date<=:end')
            ->andWhere('la.is_deleted=:status')
            ->orderBy('la.leave_date')
            ->setParameters(['start' => Carbon::parse($date)->startOfMonth(), 'end' => Carbon::parse($date)->endOfMonth(), 'status' => 0])
            ->getQuery();

        return $query->getArrayResult();
    }
    public function getFinalApprovedLeave($emp_id, $startDate, $endDate)
    {
        $leave_approved = $this->_em->createQueryBuilder()
            ->select('la,lt.leavetype,u.firstname, u.lastname,u.id')
            ->from('App\Entities\LeaveApproved', 'la')
            ->leftJoin('la.user_id', 'u')
            ->leftJoin('la.leave_type', 'lt')
            ->where('la.leave_date>=:start')
            ->andWhere('la.leave_date<=:end')
            ->andWhere('la.status=:status')
            ->andWhere('la.user_id=:emp_id')
//            ->andWhere('la.leave_id IS NULL')
            ->orderBy('la.leave_date')
            ->setParameters(['start' => Carbon::parse($startDate)->startOfMonth(), 'end' => Carbon::parse($endDate)->endOfMonth(), 'emp_id' => $emp_id,'status' => 'Accept'])
            ->getQuery();
        $leaveApproved = $leave_approved->getArrayResult();
        return $leaveApproved;
    }
}

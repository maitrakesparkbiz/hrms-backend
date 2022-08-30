<?php

namespace App\Repositories;

use App\Entities\CheckIn;
use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

use Doctrine\ORM\Query\ResultSetMapping;

class CheckInRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\CheckIn'));
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');
    }

    public function prepareData($data)
    {
        return new CheckIn($data);
    }

    public function create(CheckIn $checkin)
    {
        $this->_em->persist($checkin);
        $this->_em->flush();

        return $checkin->getId();
    }

    public function CheckInOfId($id)
    {
        return $this->_em->getRepository('App\Entities\CheckIn')->findOneBy([
            "id" => $id
        ]);
    }

    public function getCheckInByEmpId($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('c')
            ->from('App\Entities\CheckIn', 'c')
            ->leftJoin('c.emp_id', 'u')
            ->where('u.id=:emp_id')
            ->setParameter('emp_id', $emp_id)
            ->getQuery();

        return $query->getArrayResult();
    }

    public function verifyTodayCheckin($emp_id, $selected_date)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('c')
            ->from('App\Entities\CheckIn', 'c')
            ->leftJoin('c.emp_id', 'u')
            ->where('u.id=:emp_id')
            ->andWhere('DATE(c.check_in_time)=:selected')
            ->setParameters(['emp_id' => $emp_id, 'selected' => Carbon::parse($selected_date)->setTime(0, 0, 0)])
            ->getQuery();

        return $query->getArrayResult();
    }


    public function getMemberTodayCheckin($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('ch,b')
            ->from('App\Entities\CheckIn', 'ch')
            ->leftJoin('ch.breaks', 'b')
            ->where('ch.emp_id=:emp_id')
            ->andWhere('DATE(ch.check_in_time)=:today')
            ->setParameters(['emp_id' => $emp_id, 'today' => Carbon::today()])
            ->getQuery();

        return $query->getArrayResult();
    }

    public function verifyCheckOutOfEmp($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('c')
            ->from('App\Entities\CheckIn', 'c')
            ->leftJoin('c.emp_id', 'u')
            ->where('u.id=:emp_id')
            ->andWhere('c.check_out_emp_id is NULL')
            ->andWhere('c.check_out_time is NULL')
            ->andWhere('c.created_at<:today')
            ->setParameters(['emp_id' => $emp_id, 'today' => Carbon::today()])
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getCheckInIdByDate($emp_id, $startTime, $endTime)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('c')
            ->from('App\Entities\CheckIn', 'c')
            ->leftJoin('c.emp_id', 'u')
            ->where('u.id=:emp_id')
            ->andWhere('c.check_in_time>=:startTime')
            ->andWhere('c.check_in_time<=:endTime')
            ->setParameters(['emp_id' => $emp_id, 'startTime' => $startTime, 'endTime' => $endTime])
            ->getQuery();

        return $query->getArrayResult();
    }

    public function update(CheckIn $checkIn, $data)
    {
        if (isset($data['emp_id'])) {
            $checkIn->setEmpId($data['emp_id']);
        }
        if (isset($data['check_out_emp_id'])) {
            $checkIn->setCheckOutId($data['check_out_emp_id']);
        }
        if (isset($data['check_in_time'])) {
            $checkIn->setCheckInTime($data['check_in_time']);
        }
        if (array_key_exists('check_out_time', $data)) {
            $checkIn->setCheckOutTime($data['check_out_time']);
        }
        if (isset($data['check_in_ip'])) {
            $checkIn->setCheckInIp($data['check_in_ip']);
        }
        if (isset($data['check_out_ip'])) {
            $checkIn->setCheckOutIp($data['check_out_ip']);
        }
        if (array_key_exists('is_late', $data)) {
            $checkIn->setIsLate($data['is_late']);
        }
        if (array_key_exists('late_minutes', $data)) {
            $checkIn->setLateMinutes($data['late_minutes']);
        }
        $this->_em->persist($checkIn);
        $this->_em->flush();

        return $checkIn;
    }

    public function getEmpMonthYearData($emp_id, $startDate, $endDate)
    {
        $params = [
            'emp_id' => $emp_id,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        $query = $this->_em->createQueryBuilder()
            ->select('ch', 'b', 'c')
            ->from('App\Entities\CheckIn', 'ch')
            ->leftJoin('ch.emp_id', 'u')
            ->leftJoin('ch.breaks', 'b')
            ->leftJoin('ch.comments', 'c')
            ->where('u.id=:emp_id')
            ->andWhere('ch.check_in_time>=:startDate')
            ->andWhere('ch.check_in_time<=:endDate')
            ->setParameters($params)
            ->getQuery();

        return $query->getArrayResult();
    }

    //    public function getEmpMonthYearData1($emp_id, $startDate, $endDate)
    //    {
    //        $params = [
    //            'emp_id' => $emp_id,
    //            'startDate' => $startDate,
    //            'endDate' => $endDate
    //        ];
    //
    //        $query = $this->_em->createQueryBuilder()
    //            ->select('ch', 'b', 'c')
    //            ->from('App\Entities\CheckIn', 'ch')
    //            ->leftJoin('ch.emp_id', 'u')
    //            ->leftJoin('ch.breaks', 'b')
    //            ->leftJoin('ch.comments', 'c')
    //            ->where('u.id=:emp_id')
    //            ->andWhere('ch.check_in_time>=:startDate')
    //            ->andWhere('ch.check_in_time<=:endDate')
    //            ->andWhere('ch.check_in_time<=:endDate')
    //            ->setParameters($params)
    //            ->getQuery();
    //
    //        return $query->getArrayResult();
    //    }

    public function getStaffingHours($emp_id, $startDate, $endDate)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "select DISTINCT 
                    checkin.id,
                    TIMEDIFF(checkin.check_out_time,checkin.check_in_time) as initialStaffing,
                    TIMEDIFF(
                        TIMEDIFF(checkin.check_out_time,checkin.check_in_time),
                        TIMEDIFF(
                            (select SEC_TO_TIME(SUM(TIME_TO_SEC(break_out_time))) from breaks where breaks.check_in_id = checkin.id AND breaks.deletedAt IS NULL),
                            (select SEC_TO_TIME(SUM(TIME_TO_SEC(break_in_time))) from breaks where breaks.check_in_id = checkin.id AND breaks.deletedAt IS NULL)
                        )
                    ) as staffing,
                    TIMEDIFF(
                            (select SEC_TO_TIME(SUM(TIME_TO_SEC(break_out_time))) from breaks where breaks.check_in_id = checkin.id AND breaks.deletedAt IS NULL),
                            (select SEC_TO_TIME(SUM(TIME_TO_SEC(break_in_time))) from breaks where breaks.check_in_id = checkin.id AND breaks.deletedAt IS NULL)
                        ) as break
                from checkin
                WHERE checkin.emp_id = $emp_id
                AND checkin.check_in_time BETWEEN '$startDate' AND '$endDate'";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getCheckInDataById($check_in_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('ch', 'b', 'c', 'u.id as emp_id')
            ->from('App\Entities\CheckIn', 'ch')
            ->leftJoin('ch.breaks', 'b')
            ->leftJoin('ch.comments', 'c')
            ->leftjoin('ch.emp_id', 'u')
            ->where('ch.id=:id')
            ->setParameter('id', $check_in_id)
            ->getQuery();

        return $query->getArrayResult()[0];
    }

    //    public function getUserAttendanceByDate($emp_id, $start, $end)
    //    {
    //        $query = $this->_em->createQueryBuilder();
    //        $query->select('ch', 'b')
    //            ->from('App\Entities\CheckIn', 'ch')
    //            ->leftJoin('ch.emp_id', 'u')
    //            ->leftJoin('ch.breaks', 'b')
    //            ->where('u.id=:emp_id')
    //            ->andWhere('ch.check_in_time >=:start_date')
    //            ->andWhere('ch.check_in_time <=:end_date')
    //            ->orderBy('ch.check_in_time', 'DESC')
    //            ->setParameters([
    //                'start_date' => $start,
    //                'end_date' => $end,
    //                'emp_id' => $emp_id
    //            ]);
    //        $res = $query->getQuery();
    //        return $res->getArrayResult();
    //    }


    public function getUserAttendanceByDate($emp_id, $start, $length, $start_date, $end_date)
    {

        $query = $this->_em->createQueryBuilder();

        $query->select('ch', 'b')
            ->from('App\Entities\CheckIn', 'ch')
            ->leftJoin('ch.emp_id', 'u')
            ->leftJoin('ch.breaks', 'b')
            ->where('u.id=:emp_id')
            ->andWhere('ch.check_in_time >=:start_date')
            ->andWhere('ch.check_in_time <=:end_date')
            ->orderBy('DATE(ch.check_in_time)', 'DESC')
            //            ->setFirstResult($start)
            //            ->setMaxResults($length)
            ->setParameters(
                [
                    'emp_id' => $emp_id,
                    'start_date' => $start_date,
                    'end_date' => $end_date
                ]
            );

        $qb = $query->getQuery();

        $data = $qb->getResult(Query::HYDRATE_ARRAY);
        return $data;
    }


    public function countUserAttendanceByDate($emp_id, $start_date, $end_date)
    {
        $query = $this->_em->createQueryBuilder();

        $query->select('count(ch.id) as count')
            ->from('App\Entities\CheckIn', 'ch')
            ->leftJoin('ch.emp_id', 'u')
            ->where('u.id=:emp_id')
            ->andWhere('ch.check_in_time >=:start_date')
            ->andWhere('ch.check_in_time <=:end_date')
            ->setParameters(
                [
                    'emp_id' => $emp_id,
                    'start_date' => $start_date,
                    'end_date' => $end_date
                ]
            );

        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function getPreviousCheckIn($user_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('ch', 'b')
            ->from('App\Entities\CheckIn', 'ch')
            ->leftJoin('ch.breaks', 'b')
            ->where('ch.emp_id=:user_id')
            ->andWhere('ch.check_out_time IS NOT NULL')
            ->setParameter('user_id', $user_id)
            ->setMaxResults(1)
            ->orderBy('DATE(ch.check_in_time)', 'DESC')
            ->getQuery();
        return $query->getArrayResult();
    }
}
 
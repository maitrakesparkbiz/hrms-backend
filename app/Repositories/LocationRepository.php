<?php

namespace App\Repositories;

use App\Entities\Location;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class LocationRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\Location'));
    }

    public function create(Location $location)
    {
        $this->_em->persist($location);
        $this->_em->flush();

        return $location;
    }

    public function prepareData($data)
    {
        return new Location($data);
    }

    public function LocationOfId($id)
    {
        return $this->_em->getRepository('App\Entities\Location')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(Location $location, $data)
    {
        if (isset($data["name"])) {
            $location->setName($data["name"]);
        }
        if (isset($data["address"])) {
            $location->setAddress($data["address"]);
        }
        if (isset($data["office_start_time"])) {
            $location->setOffice_start_time($data["office_start_time"]);
        }
        if (isset($data["office_end_time"])) {
            $location->setOffice_end_time($data["office_end_time"]);
        }
        if (isset($data["leave_start_month"])) {
            $location->setLeave_start_month($data["leave_start_month"]);
        }
        if (isset($data["employee_self_checking"])) {
            $location->setEmployee_self_checking($data["employee_self_checking"]);
        }
        if (isset($data["overtime_pay"])) {
            $location->setOvertime_pay($data["overtime_pay"]);
        }
        if (isset($data["half_day_allowed"])) {
            $location->setHalfDayAllowed($data["half_day_allowed"]);
        }
        if (array_key_exists('allowed_ip', $data)) {
            $location->setAllowed_ip($data["allowed_ip"]);
        }
        if (array_key_exists("half_day_hours", $data)) {
            $location->setHalfDayHours($data["half_day_hours"]);
        }
        if (isset($data["clock_reminder"])) {
            $location->setClock_reminder($data["clock_reminder"]);
        }
        if (isset($data["clock_reminder_time"])) {
            $location->setClock_reminder_time($data["clock_reminder_time"]);
        }
        if (isset($data["late_mark_after_minute"])) {
            $location->setLate_mark_after_minute($data["late_mark_after_minute"]);
        }
        if (array_key_exists('working_days', $data)) {
            $location->setWorkingDays($data["working_days"]);
        }
        if (array_key_exists('alt_sat', $data)) {
            $location->setAltSat($data["alt_sat"]);
        }

        $this->_em->persist($location);
        $this->_em->flush();

        return $location;
    }

    public function getAllLocation()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("d,ls,u")
            ->from("App\Entities\Location", "d")
            ->leftJoin("d.leave_start_month", 'ls')
            ->leftJoin('d.employees', 'u', 'with', 'u.user_exit_status=:status')
            ->setParameter('status', 1)
            ->getQuery();

        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }
    public function getFilterRecords($search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(d.id) as count')
            ->from("App\Entities\Location", "d")
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('d.name', ':filter')
                )
            )
            ->setParameters(
                ['filter' => '%' . $search . '%']
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countAllLocation()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(d.id) as count')
            ->from("App\Entities\Location", "d");
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function getAllLocationDatatable($order, $col, $search, $start, $length)
    {

        $query = $this->_em->createQueryBuilder();
        $query->select("d.id", "d.name", "count(u.id) as employees")
            ->from("App\Entities\Location", "d")
            ->leftJoin('d.employees', 'u', 'with', 'u.user_exit_status=:status')
            ->groupBy('d.id');

        $query->having(
            $query->expr()->orX(
                $query->expr()->like('d.name', ':filter'),
                $query->expr()->like('employees', ':filter')
            )
        );


        $query->orderBy(($col == 'employees' ? 'employees' : 'd.name'), strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                ['filter' => '%' . $search . '%', 'status' => 1]
            );

        $qb = $query->getQuery();

        $data = $qb->getResult(Query::HYDRATE_ARRAY);
        return $data;
    }

    public function delete(Location $location)
    {
        $this->_em->remove($location);
        $this->_em->flush();
    }

    public function checkDept($name)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("d")
            ->from("App\Entities\Location", "d")
            ->where('d.name =: name')
            ->setParameter('name', $name);

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function getAllLocationOpt()
    {
        $qry = $this->_em->createQueryBuilder()
            ->select('l.name', 'l.id', 'l.working_days', 'l.alt_sat')
            ->from('App\Entities\Location', 'l')
            ->getQuery();

        return $qry->getArrayResult();
    }

    public function getUserBatch($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('l.late_mark_after_minute,l.office_start_time,l.office_end_time,l.half_day_hours')
            ->from('App\Entities\Location', 'l')
            ->leftjoin('l.employees', 'u')
            ->where('u.id=:emp_id')
            ->setParameter('emp_id', $emp_id)
            ->getQuery();

        return $query->getArrayResult();
    }
    public function getEmpWorkingHours($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('l.office_start_time,l.office_end_time')
            ->from('App\Entities\Location', 'l')
            ->leftjoin('l.employees', 'u')
            ->where('u.id=:emp_id')
            ->setParameter('emp_id', $emp_id)
            ->getQuery();

        return $query->getArrayResult();
    }
}

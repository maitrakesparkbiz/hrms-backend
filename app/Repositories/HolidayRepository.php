<?php

namespace App\Repositories;

use App\Entities\Holiday;
use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class HolidayRepository extends EntityRepository
{

//    $this->class = 'App\Entites\Holiday';

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\Holiday'));
    }

    public function prepareData($data)
    {
        return new Holiday($data);
    }

    public function create(Holiday $holiday)
    {
        $this->_em->persist($holiday);
        $this->_em->flush();

        return $holiday->getId();
    }

    public function HolidayOfId($id)
    {
        return $this->_em->getRepository('App\Entities\Holiday')->findOneBy([
            "id" => $id
        ]);
    }

    public function getHolidayDataByYear($year)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('h')
            ->from('App\Entities\Holiday', 'h')
            ->where('h.year=:yr')
            ->setParameter('yr', $year)
            ->getQuery();

        $qr = $query->getArrayResult();
        return $qr;
    }

    public function getHolidayInfo($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('h')
            ->from('App\Entities\Holiday', 'h')
            ->where('h.id=:id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getArrayResult()[0];
    }

    public function delete(Holiday $holiday)
    {
        $this->_em->remove($holiday);
        $this->_em->flush();
    }

    public function updateHolidayInfo(Holiday $holiday, $data)
    {
        if (isset($data['event_name'])) {
            $holiday->setEventName($data['event_name']);
        }
        if (array_key_exists('description', $data)) {
            $holiday->setDescription($data['description']);
        }
        if (isset($data['start_date'])) {
            $holiday->setStartDate($data['start_date']);
        }
        if (isset($data['end_date'])) {
            $holiday->setEndDate($data['end_date']);
        }

        $this->_em->persist($holiday);
        $this->_em->flush();

        return $holiday;
    }

    public function getHolidaysDashboard()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('h')
            ->from('App\Entities\Holiday', 'h')
            ->where('h.year=:year')
            ->andWhere('h.is_company_event=:flag')
            ->setParameters(['year' => Carbon::today()->year, 'flag' => 0])
            ->getQuery();

        return $query->getArrayResult();
    }
    public function getFilterRecords($year, $search)
    {
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');
        $query = $this->_em->createQueryBuilder();
        $query->select('count(h.id) as count')
            ->from('App\Entities\Holiday', 'h')
            ->where('h.year=:yr')
            ->andWhere('h.is_company_event=:flag')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('h.event_name', ':filter')
                    , $query->expr()->like('DATE(h.start_date)', ':filter')
                )
            )
            ->setParameters(['yr' => $year, 'flag' => 0, 'filter' => '%' . $search . '%']);
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

public function countYearHolidays($year, $search)
    {
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');
        $query = $this->_em->createQueryBuilder();
        $query->select('count(h.id) as count')
            ->from('App\Entities\Holiday', 'h')
            ->where('h.year=:yr')
            ->andWhere('h.is_company_event=:flag')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('h.event_name', ':filter')
                    , $query->expr()->like('DATE(h.start_date)', ':filter')
                )
            )
            ->setParameters(['yr' => $year, 'flag' => 0, 'filter' => '%' . $search . '%']);
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function getHolidaysDatatable($year, $col, $order, $search, $start, $length)
    {
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');

        $query = $this->_em->createQueryBuilder();
        $query->select('h')
            ->from('App\Entities\Holiday', 'h')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('h.event_name', ':filter')
                    , $query->expr()->like('DATE(h.start_date)', ':filter')
                ),
                $query->expr()->eq('h.year', ':year'),
                $query->expr()->eq('h.is_company_event', ':flag')
            )
            ->orderBy('h.' . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                ['filter' => '%' . $search . '%', 'year' => $year, 'flag' => 0]
            );

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

}

?>
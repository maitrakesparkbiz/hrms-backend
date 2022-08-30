<?php

namespace App\Repositories;

use App\Entities\Holiday_employee;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class HolidayEmployeeRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\Holiday_employee'));
    }

    public function prepareData($data)
    {
        return new Holiday_employee($data);
    }

    public function create(Holiday_employee $holiday_employee)
    {
        $this->_em->persist($holiday_employee);
        $this->_em->flush();

        return $holiday_employee->getId();
    }

    public function HolidayEmployeeOfId($id)
    {
        return $this->_em->getRepository('App\Entities\Holiday_employee')->findOneBy([
            "id" => $id
        ]);
    }

    public function getAssocEmpIds($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('u.id')
            ->from('App\Entities\Holiday_employee', 'he')
            ->leftJoin('he.emp_id', 'u')
            ->leftJoin('he.holiday_id', 'h')
            ->where('h.id=:id')
            ->setParameter('id', $id);

        $q = $query->getQuery();

        return $q->getArrayResult();

    }

    public function getAllRowOfId($holiday_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('he', 'u.id as user_id', 'h.id')
            ->from('App\Entities\Holiday_employee', 'he')
            ->leftJoin('he.holiday_id', 'h')
            ->leftJoin('he.emp_id', 'u')
            ->where('h.id=:holiday_id')
            ->setParameter('holiday_id', $holiday_id);

        $q = $query->getQuery();
        return $q->getArrayResult();
    }

    public function deleteRow(Holiday_employee $holiday_employee)
    {
        $this->_em->remove($holiday_employee);
        $this->_em->flush();
    }

    public function deleteAllHolidayEmp($id)
    {
        $query = $this->createQueryBuilder('he')
            ->delete()
            ->where('he.holiday_id= :holiday_id')
            ->setParameter('holiday_id', $id)
            ->getQuery();

        return $query->getArrayResult();
    }
}

?>
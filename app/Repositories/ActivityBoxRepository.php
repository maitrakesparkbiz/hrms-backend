<?php

namespace App\Repositories;

use App\Entities\ActivityBox;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ActivityBoxRepository extends EntityRepository
{

    public $class = 'App\Entities\ActivityBox';
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\ActivityBox'));
    }

    public function prepareData($data)
    {
        return new ActivityBox($data);
    }

    public function create(ActivityBox $activityBox)
    {
        $this->_em->persist($activityBox);
        $this->_em->flush();

        return $activityBox;
    }

    public function ActivityBoxOfId($id)
    {
        return $this->_em->getRepository('App\Entities\ActivityBox')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(ActivityBox $activityBox, $data)
    {
        if (isset($data["emp_id"])) {
            $activityBox->setEmpId($data["emp_id"]);
        }
        if (isset($data["from_emp"])) {
            $activityBox->setFromEmp($data["from_emp"]);
        }
        if (isset($data["title"])) {
            $activityBox->setTitle($data["title"]);
        }
        if (isset($data["details"])) {
            $activityBox->setDetails($data["details"]);
        }
        if (isset($data["is_read"])) {
            $activityBox->setIsRead($data["is_read"]);
        }


        $this->_em->persist($activityBox);
        $this->_em->flush();

        return $activityBox;
    }

    public function delete(ActivityBox $activityBox)
    {
        $this->_em->remove($activityBox);
        $this->_em->flush();
    }

    public function getEmpNotifications($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('a')
            ->from($this->class, 'a')
            ->where('a.emp_id=:emp_id')
            ->orderBy('a.created_at', 'DESC')
            ->setParameter('emp_id', $emp_id)
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getEmpUnReadNotifications($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('a')
            ->from($this->class, 'a')
            ->where('a.emp_id=:emp_id')
            ->andWhere('a.is_read = 0')
            ->setParameter('emp_id', $emp_id)
            ->getQuery();

        return $query->getArrayResult();
    }
}

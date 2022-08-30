<?php

namespace App\Repositories;

use App\Entities\Appreciation;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class AppreciationRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\Appreciation'));
    }

    public function prepareData($data)
    {
        return new Appreciation($data);
    }

    public function create(Appreciation $contact)
    {
        $this->_em->persist($contact);
        $this->_em->flush();

        return $contact;
    }

    public function AppreciationOfByID($id)
    {
        return $this->_em->getRepository('App\Entities\Appreciation')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(Appreciation $contact, $data)
    {
        if (isset($data["emp_id"])) {
            $contact->setEmpId($data["emp_id"]);
        }
        if (isset($data["award_id"])) {
            $contact->setAwardId($data["award_id"]);
        }
        if (isset($data["prize"])) {
            $contact->setPrize($data["prize"]);
        }
        if (isset($data["date"])) {
            $contact->setDate($data["date"]);
        }
        if (isset($data["image"]) || $data["image"] == null) {
            $contact->setImage($data["image"]);
        }


        $this->_em->persist($contact);
        $this->_em->flush();

        return $contact;
    }

    public function getAllAppreciation()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("c,e,a")
            ->from("App\Entities\Appreciation", "c")
            ->leftJoin("c.emp_id", "e")
            ->leftJoin("c.award_id", "a");

        $qb = $query->getQuery();
        $data = $qb->getResult(Query::HYDRATE_ARRAY);
        return $data;
    }

    public function countAllAppreciation()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(ap.id) as count')
            ->from("App\Entities\Appreciation", "ap");
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function countFilteredRows($search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(ap.id) as count')
            ->from("App\Entities\Appreciation", "ap")
            ->leftJoin("ap.emp_id", "e")
            ->leftJoin("ap.award_id", "a")
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('a.name', ':filter'),
                    $query->expr()->like("concat(e.firstname,' ',e.lastname)", ':filter'),
                    $query->expr()->like("concat(e.lastname,' ',e.firstname)", ':filter'),
                    $query->expr()->like('DATE(ap.date)', ':filter')
                )
            )
            ->setParameters(
                ['filter' => '%' . $search . '%']
            );
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function getAllAppreciationDatatable($col, $order, $search, $start, $length)
    {
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');

        $query = $this->_em->createQueryBuilder();
        $query->select("c,c.id,c.image,e.firstname, e.lastname, e.profile_image, e.id as emp_id,a.name as award_name, a.id as award_id")
            ->from("App\Entities\Appreciation", "c")
            ->leftJoin("c.emp_id", "e")
            ->leftJoin("c.award_id", "a")
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('a.name', ':filter'),
                    $query->expr()->like("concat(e.firstname,' ',e.lastname)", ':filter'),
                    $query->expr()->like("concat(e.lastname,' ',e.firstname)", ':filter'),
                    $query->expr()->like('DATE(c.date)', ':filter')
                )
            )
            ->orderBy(($col == 'firstname' ? 'e.' : ($col == 'name' ? 'a.' : 'c.')) . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                ['filter' => '%' . $search . '%']
            );

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function delete(Appreciation $appreciation)
    {
        $this->_em->remove($appreciation);
        $this->_em->flush();
    }

    public function getAppreciationById($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select("c,e,a")
            ->from("App\Entities\Appreciation", "c")
            ->leftJoin("c.emp_id", "e")
            ->leftJoin("c.award_id", "a")
            ->where("c.id = :id")
            ->setParameter("id", $id)
            ->getQuery();
        return $query->getArrayResult([0]);
    }

    public function getAppreciationByEmpId($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select("c,e,a")
            ->from("App\Entities\Appreciation", "c")
            ->leftJoin("c.emp_id", "e")
            ->leftJoin("c.award_id", "a")
            ->where("c.emp_id = :id")
            ->setParameter("id", $emp_id)
            ->getQuery();
        return $query->getArrayResult();
    }
}

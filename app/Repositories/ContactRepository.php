<?php

namespace App\Repositories;

use App\Entities\Contact;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ContactRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\Contact'));
    }

    public function prepareData($data)
    {
        return new Contact($data);
    }

    public function create(Contact $contact)
    {
        $this->_em->persist($contact);
        $this->_em->flush();

        return $contact;
    }

    public function ContactOfId($id)
    {
        return $this->_em->getRepository('App\Entities\Contact')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(Contact $contact, $data)
    {

        if (isset($data["name"])) {
            $contact->setName($data["name"]);
        }
        if (isset($data["number"])) {
            $contact->setNumber($data["number"]);
        }
        if (isset($data["email"])) {
            $contact->setEmail($data["email"]);
        }
        if (isset($data["service"])) {
            $contact->setService($data["service"]);
        }
        if (isset($data["description"])) {
            $contact->setDescription($data["description"]);
        }


        $this->_em->persist($contact);
        $this->_em->flush();

        return $contact;
    }

    public function getAllContact()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("c")
            ->from("App\Entities\Contact", "c");

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function countAllContacts()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(c) as count')
            ->from('App\Entities\Contact', 'c');
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function countFilteredRows($search)
    {
        $query
            = $this->_em->createQueryBuilder();
        $query->select('count(c) as count')
            ->from('App\Entities\Contact', 'c')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('c.name', ':filter'),
                    $query->expr()->like('c.email', ':filter'),
                    $query->expr()->like('c.number', ':filter'),
                    $query->expr()->like('c.service', ':filter')
                )
            )
            ->setParameters(
                ['filter' => '%' . $search . '%']
            );
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function getAllContactsDatatable($col, $order, $search, $start, $length)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('c')
            ->from("App\Entities\Contact", "c")
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('c.name', ':filter'),
                    $query->expr()->like('c.email', ':filter'),
                    $query->expr()->like('c.number', ':filter'),
                    $query->expr()->like('c.service', ':filter')
                )
            )
            ->orderBy('c.' . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                ['filter' => '%' . $search . '%']
            );
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function delete(Contact $contact)
    {
        $this->_em->remove($contact);
        $this->_em->flush();
    }

    public function getContactById($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select("c")
            ->from("App\Entities\Contact", "c")
            ->where("c.id = :id")
            ->setParameter("id", $id)
            ->getQuery();
        return $query->getArrayResult([0]);
    }
}

<?php

namespace App\Repositories;

use App\Entities\Expense_category;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ExpenseCategoryRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\Expense_category'));
    }

    public function create(Expense_category $expense)
    {
        $this->_em->persist($expense);
        $this->_em->flush();

        return $expense;
    }

    public function prepareData($data)
    {
        return new Expense_category($data);
    }

    public function ExpenseOfId($id)
    {
        return $this->_em->getRepository('App\Entities\Expense_category')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(Expense_category $expense, $data)
    {
        if (isset($data["name"])) {
            $expense->setName($data["name"]);
        }
        if (isset($data["description"])) {
            $expense->setDescription($data["description"]);
        }

        $this->_em->persist($expense);
        $this->_em->flush();

        return $expense;
    }

    public function getAllExpense()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("d")
            ->from("App\Entities\Expense_category", "d");

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }
    public function getFilterRecords($search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(c) as count')
            ->from("App\Entities\Expense_category", "c")
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('c.name', ':filter')
                    , $query->expr()->like('c.description', ':filter')
                )
            )
            ->setParameters(
                ['filter' => '%' . $search . '%']
            );
        $qb = $query->getQuery();

        return $qb->getArrayResult();

    }
    public function countAllCat()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(c) as count')
            ->from("App\Entities\Expense_category", "c");
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }


    public function getAllCatDatatable($col, $order, $search, $start, $length)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("d")
            ->from("App\Entities\Expense_category", "d")
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('d.name', ':filter'),
                    $query->expr()->like('d.description', ':filter')
                )
            )
            ->orderBy('d.' . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                ['filter' => '%' . $search . '%']
            );

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function delete(Expense_category $expense)
    {
        $this->_em->remove($expense);
        $this->_em->flush();
    }

    public function checkDept($name)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("d")
            ->from("App\Entities\Designation", "d")
            ->where('d.name =: name')
            ->setParameter('name', $name);

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }
}

<?php

namespace App\Repositories;

use App\Entities\Expense;
use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use phpDocumentor\Reflection\Types\Null_;

class ExpenseRepository extends EntityRepository
{


    private $class = 'App\Entities\Expense';

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\Expense'));
    }

    public function prepareData($data)
    {
        return new Expense($data);
    }

    public function create(Expense $news)
    {
        $this->_em->persist($news);
        $this->_em->flush();

        return $news->getId();
    }

    public function ExpenseOfId($id)
    {
        return $this->_em->getRepository('App\Entities\Expense')->findOneBy([
            "id" => $id
        ]);
    }


    public function countAllExpense()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(e.id) as count')
            ->from('App\Entities\Expense', 'e')
            ->where('e.status IN (:status)')
            ->andWhere('e.is_expense=:flag')
            ->setParameters(['status' => ['approve', ''], 'flag' => 1]);
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function countFilteredRowsAll($search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(e.id) as count')
            ->from('App\Entities\Expense', 'e')
            ->leftJoin('e.category_id', 'ec')
            ->where('e.status IN (:status)')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('DATE(e.bill_date)', ':filter'),
                    $query->expr()->like('e.amount', ':filter'),
                    $query->expr()->like('e.title', ':filter'),
                    $query->expr()->like('ec.name', ':filter')
                )
            )
            ->setParameter('status', ['approve', ''])
            ->setParameter('filter', '%' . $search . '%');
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function countAllClaims()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(ecs.id) as count')
            ->from("App\Entities\Expense", "ecs")
            ->where('ecs.is_claim=:flag')
            ->setParameter('flag', 1);
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function countFilteredRowsClaims($status, $search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(ecs.id) as count')
            ->from("App\Entities\Expense", "ecs")
            ->leftJoin('ecs.emp_id', 'em')
            ->leftJoin('ecs.category_id', 'ec')
            ->where('ecs.is_claim=:flag')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like("concat(em.firstname,' ',em.lastname)", ':filter'),
                    $query->expr()->like("concat(em.lastname,' ',em.firstname)", ':filter'),
                    $query->expr()->like('ecs.amount', ':filter'),
                    $query->expr()->like('ecs.title', ':filter'),
                    $query->expr()->like('ec.name', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->like('ecs.status', ':status')
                )
            )
            ->setParameters([
                'status' => ($status == 'all' ? '%' : $status),
                'filter' => '%' . $search . '%',
                'flag' => 1
            ]);
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function getAllExpense($col, $order, $search, $start, $length)
    {
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');

        $query = $this->_em->createQueryBuilder();
        $query->select("e,em,ec,u")
            ->from("App\Entities\Expense", "e")
            ->leftJoin('e.emp_id', 'em')
            ->leftJoin('e.actioned_by', 'u')
            ->leftJoin('e.category_id', 'ec')
            ->where('e.status IN (:status)')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('DATE(e.bill_date)', ':filter'),
                    $query->expr()->like('e.amount', ':filter'),
                    $query->expr()->like('e.title', ':filter'),
                    $query->expr()->like('ec.name', ':filter')
                )
            )
            ->orderBy(($col == 'name' ? 'ec.' : 'e.') . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(['filter' => '%' . $search . '%', 'status' => ['approve', '']]);

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }


    public function getAllClaims($col, $order, $search, $start, $length, $status)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("e,em,ec,u")
            ->from("App\Entities\Expense", "e")
            ->leftJoin('e.emp_id', 'em')
            ->leftJoin('e.actioned_by', 'u')
            ->leftJoin('e.category_id', 'ec')
            ->where('e.is_claim=:flag')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like("concat(em.firstname,' ',em.lastname)", ':filter'),
                    $query->expr()->like("concat(em.lastname,' ',em.firstname)", ':filter'),
                    $query->expr()->like('e.amount', ':filter'),
                    $query->expr()->like('e.title', ':filter'),
                    $query->expr()->like('ec.name', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->like('e.status', ':status')
                )
            )
            ->orderBy(($col == 'firstname' ? 'em.' : ($col == 'name' ? 'ec.' : 'e.')) . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(['flag' => 1, 'filter' => '%' . $search . '%', 'status' => ($status == 'all' ? '%' : $status)]);

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }


    //    public function getClaimsSelf($id)
    //    {
    //        $query = $this->_em->createQueryBuilder()
    //            ->select('e,emp,u,ec')
    //            ->from('App\Entities\Expense', 'e')
    //            ->leftJoin('e.emp_id', 'emp')
    //            ->leftJoin('e.actioned_by', 'u')
    //            ->leftJoin('e.category_id', 'ec')
    //            ->where('emp.id=:id')
    //            ->andWhere('e.is_claim=:flag')
    //            ->setParameter('flag', 1)
    //            ->setParameter('id', $id)
    //            ->getQuery();
    //
    //        return $query->getArrayResult();
    //    }

    public function getRowCountSelf($emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(e.id) as count')
            ->from("App\Entities\Expense", "e")
            ->where('e.is_claim=:flag')
            ->andWhere('e.emp_id=:emp_id')
            ->setParameters(['flag' => 1, 'emp_id' => $emp_id]);
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }

    public function countFilteredSelfClaims($search, $status, $id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(e.id) as count')
            ->from("App\Entities\Expense", "e")
            ->leftJoin('e.emp_id', 'em')
            ->leftJoin('e.actioned_by', 'u')
            ->leftJoin('e.category_id', 'ec')
            ->where('e.is_claim=:flag')
            ->andWhere('e.emp_id=:emp_id')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('e.title', ':filter'),
                    $query->expr()->like('e.amount', ':filter'),
                    $query->expr()->like('ec.name', ':filter'),
                    $query->expr()->like('e.bill_date', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->like('e.status', ':status')
                )
            )
            ->setParameters(
                [
                    'flag' => 1,
                    'emp_id' => $id,
                    'filter' => '%' . $search . '%',
                    'status' => ($status == 'all' ? '%' : $status)
                ]
            );

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }


    public function getClaimsSelf($id, $col, $order, $search, $start, $length, $status)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("e,em,ec,u")
            ->from("App\Entities\Expense", "e")
            ->leftJoin('e.emp_id', 'em')
            ->leftJoin('e.actioned_by', 'u')
            ->leftJoin('e.category_id', 'ec')
            ->where('e.is_claim=:flag')
            ->andWhere('e.emp_id=:emp_id')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('e.title', ':filter'),
                    $query->expr()->like('e.amount', ':filter'),
                    $query->expr()->like('ec.name', ':filter'),
                    $query->expr()->like('e.bill_date', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->like('e.status', ':status')
                )
            )
            ->orderBy(($col == 'name' ? 'ec.' : 'e.') . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(['flag' => 1, 'emp_id' => $id, 'filter' => '%' . $search . '%', 'status' => ($status == 'all' ? '%' : $status)]);

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function delete(Expense $expense)
    {
        $this->_em->remove($expense);
        $this->_em->flush();
    }

    public function getExpenseById($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select("e,em,ec")
            ->from("App\Entities\Expense", "e")
            ->leftJoin("e.emp_id", 'em')
            ->leftJoin('e.category_id', 'ec')
            ->where("e.id = :id")
            ->setParameter("id", $id)
            ->getQuery();

        return $query->getArrayResult()[0];
    }

    public function getPendingClaims()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('e,u.id as emp_id, u.firstname, u.lastname, u.profile_image')
            ->from($this->class, 'e')
            ->leftJoin('e.emp_id', 'u')
            ->where('e.status=:status')
            ->setParameter('status', 'pending')
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getExpenseByYear($cat_id, $year)
    {
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');

        $query = $this->_em->createQueryBuilder();
        $query->select('e')
            ->from($this->class, 'e')
            ->where('e.category_id=:cat_id')
            ->andWhere('YEAR(e.bill_date)=:year')
            ->andWhere('e.status in (:status)')
            ->setParameters(['cat_id' => $cat_id, 'year' => $year, 'status' => ['', 'approve']]);
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function getExpenseByYearOnly($year)
    {
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');

        $query = $this->_em->createQueryBuilder();
        $query->select('e')
            ->from($this->class, 'e')
            ->leftJoin('e.emp_id', 'u')
            ->where('e.status in (:status)')
            ->andWhere('YEAR(e.bill_date)=:year')
            ->setParameter('year', $year)
            ->setParameter('status', ['approve', '']);
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function update(Expense $expense, $data)
    {
        if (isset($data["title"])) {
            $expense->setTitle($data["title"]);
        }
        if (array_key_exists("receipt_image", $data)) {
            $expense->setReceiptImage($data["receipt_image"]);
        }
        if (isset($data["amount"])) {
            $expense->setAmount($data["amount"]);
        }
        if (isset($data["description"])) {
            $expense->setDescription($data["description"]);
        }
        if (isset($data["bill_date"])) {
            $expense->setBillDate($data["bill_date"]);
        }
        if (isset($data["merchant"])) {
            $expense->setMerchant($data["merchant"]);
        }
        if (isset($data["payment_method"])) {
            $expense->setPaymentMethod($data["payment_method"]);
        }
        if (isset($data["category_id"])) {
            $expense->setCategoryId($data["category_id"]);
        }
        if (isset($data["status"])) {
            $expense->setStatus($data["status"]);
        }
        if (isset($data["emp_id"])) {
            $expense->setEmpId($data["emp_id"]);
        }
        if (isset($data['actioned_by'])) {
            $expense->setActionedBy($data['actioned_by']);
        }

        if (isset($data['is_expense'])) {
            $expense->setIsExpense($data['is_expense']);
        }

        if (isset($data['is_claim'])) {
            $expense->setIsClaim($data['is_claim']);
        }
        $this->_em->persist($expense);
        $this->_em->flush();

        return $expense;
    }

    public function countPendingClaims()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('count(e) as count')
            ->from('App\Entities\Expense', 'e')
            ->where('e.status=:status')
            ->setParameter('status', 'pending')
            ->getQuery();

        return $query->getArrayResult();
    }
}

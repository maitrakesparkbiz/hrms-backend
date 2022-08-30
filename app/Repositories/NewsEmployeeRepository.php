<?php

namespace App\Repositories;

use App\Entities\News_employee;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Illuminate\Support\Facades\DB;

class NewsEmployeeRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\News_employee'));
    }

    public function prepareData($data)
    {
        return new News_employee($data);
    }

    public function create(News_employee $news)
    {
        $this->_em->persist($news);
        $this->_em->flush();

        return $news;
    }

    function checkExists($data)
    {

        $query = $this->_em->createQueryBuilder()
            ->select("ne")
            ->from("App\Entities\News_employee", "ne")
            ->where("ne.news_id = :news_id")
            ->andWhere("ne.emp_id= :emp_id")
            ->setParameter("news_id", $data['news_id'])
            ->setParameter("emp_id", $data['emp_id'])
            ->getQuery();
        return $query->getArrayResult([0]);
    }

    function getnewsEmp($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select("e.id")
            ->from("App\Entities\News_employee", "ne")
            ->leftJoin("ne.emp_id", "e")
            ->where("ne.news_id = :news_id")
            ->setParameter("news_id", $id)
            ->getQuery();
        return $query->getArrayResult();
    }

    public function NewsEmployeeOfID($id)
    {
        return $this->_em->getRepository('App\Entities\News_employee')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(News_employee $news, $data)
    {

        if (isset($data["news_id"])) {
            $news->setNewsId($data["news_id"]);
        }
        if (isset($data["emp_id"])) {
            $news->setEmpId($data["emp_id"]);
        }
        if (isset($data["is_read"])) {
            $news->setIsRead($data["is_read"]);
        }


        $this->_em->persist($news);
        $this->_em->flush();

        return $news;
    }

    public function getAllContact()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("c")
            ->from("App\Entities\News", "c");

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function delete($id)
    {
        $query = $this->createQueryBuilder("ne")
            ->delete()
            ->where("ne.news_id = :news_id")
            ->setParameter("news_id", $id)
            ->getQuery();
        return $query->getArrayResult();
    }

    public function deleteEmp($id)
    {
        $query = $this->createQueryBuilder("ne")
            ->delete()
            ->where("ne.emp_id = :emp_id")
            ->setParameter("emp_id", $id)
            ->getQuery();
        return $query->getArrayResult();
    }

    public function getNewsById($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select("c")
            ->from("App\Entities\News", "c")
            ->where("c.id = :id")
            ->setParameter("id", $id)
            ->getQuery();
        return $query->getArrayResult([0]);
    }

    public function getNewsByEmployee($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('n.id', 'n.title', 'n.description', 'n.status', 'n.publish_date')
            ->from('App\Entities\News_employee', 'ne')
            ->leftJoin('ne.news_id', 'n')
            ->where('ne.emp_id=:emp_id')
            ->andWhere('n.status=:status')
            ->setParameters(['emp_id' => $emp_id, 'status' => 'Publish'])
            ->getQuery();

        return $query->getArrayResult();
    }
    public function checkIsRead($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('ne')
            ->from('App\Entities\News_employee', 'ne')
            ->leftJoin('ne.news_id', 'n')
            ->where('ne.emp_id=:emp_id')
            ->andWhere('n.status=:status')
            ->andWhere('ne.is_read=:read')
            ->setParameters(['emp_id' => $emp_id, 'status' => 'Publish','read'=>0])
            ->getQuery();

        return $query->getArrayResult();
    }
    public function setIsRead($emp_id)
    {

        $sql = DB::select("UPDATE news_employee SET is_read=1 WHERE emp_id=".$emp_id);

        return true;


    }

}
<?php

namespace App\Repositories;

use App\Entities\News;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;

class NewsRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\News'));
    }

    public function prepareData($data)
    {
        return new News($data);
    }

    public function create(News $news)
    {
        $this->_em->persist($news);
        $this->_em->flush();

        return $news->getId();
    }

    public function NewsOfId($id)
    {
        return $this->_em->getRepository('App\Entities\News')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(News $news, $data)
    {
        if (isset($data["title"])) {
            $news->setTitle($data["title"]);
        }
        if (isset($data["status"])) {
            $news->setStatus($data["status"]);
        }
        if (isset($data["description"])) {
            $news->setDescription($data["description"]);
        }
        if (isset($data["publish_date"])) {
            $news->setPublishDate($data["publish_date"]);
        }

        $this->_em->persist($news);
        $this->_em->flush();

        return $news;
    }

    public function getAllNews($col, $order, $search, $start, $length, $status)
    {
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');

        $query = $this->_em->createQueryBuilder();
        $query->select("n,ne,e")
            ->from("App\Entities\News", "n")
            ->leftJoin('n.news_employee', 'ne')
            ->leftJoin('ne.emp_id', 'e')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('DATE(n.publish_date)', ':filter'),
                    $query->expr()->like('n.title', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->like('n.status', ':status')
                )
            )
            ->orderBy('n.' . $col, strtoupper($order))
            ->setParameters(
                ['filter' => '%' . $search . '%', 'status' => ($status == 'all' ? '%' : $status)]
            );
        $qb = $query->getQuery();

        $data = $qb->getResult(Query::HYDRATE_ARRAY);
        return array_slice($data, $start, $length);
    }

    public function countAllNews()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(n.id) as count')
            ->from("App\Entities\News", "n");

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function countFilteredRows($status, $search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(n.id) as count')
            ->from("App\Entities\News", "n")
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('DATE(n.publish_date)', ':filter'),
                    $query->expr()->like('n.title', ':filter')
                ),
                $query->expr()->like('n.status', ':status')
            )
            ->setParameters(['status' => ($status == 'all' ? '%' : ''), 'filter' => '%' . $search . '%']);

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function delete(News $news)
    {
        $this->_em->remove($news);
        $this->_em->flush();
    }

    public function getNewsById($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select("n,ne,e")
            ->from("App\Entities\News", "n")
            ->leftJoin("n.news_employee", 'ne')
            ->leftJoin("ne.emp_id", 'e')
            ->where("n.id = :id")
            ->setParameter("id", $id)
            ->getQuery();
        return $query->getArrayResult()[0];
    }
}

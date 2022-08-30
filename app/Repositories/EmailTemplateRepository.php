<?php

namespace App\Repositories;

use App\Entities\EmailTemplates;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class EmailTemplateRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\EmailTemplates'));
    }

    public function create(EmailTemplates $emailTemplates)
    {
        $this->_em->persist($emailTemplates);
        $this->_em->flush();

        return $emailTemplates;
    }

    public function prepareData($data)
    {
        return new EmailTemplates($data);
    }

    public function delete(EmailTemplates $emailTemplates)
    {
        $this->_em->remove($emailTemplates);
        $this->_em->flush();
    }

    public function EmailTemplatesOfId($id)
    {
        return $this->_em->getRepository('App\Entities\EmailTemplates')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(EmailTemplates $emailTemplates, $data)
    {
        if (isset($data["template_name"])) {
            $emailTemplates->setTemplateName($data["template_name"]);
        }

        if (isset($data["content"])) {
            $emailTemplates->setContent($data["content"]);
        }

        $this->_em->persist($emailTemplates);
        $this->_em->flush();

        return $emailTemplates;
    }

    public function getEmailTemplate()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('et')
            ->from('App\Entities\EmailTemplates', 'et')
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getAllEmailTemplateDataTable($order, $col, $search, $start, $length)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("e")
            ->from("App\Entities\EmailTemplates", "e")
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('e.template_name', ':filter')
                )
            )
            ->orderBy('e.template_name', strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                ['filter' => '%' . $search . '%']
            );

        $qb = $query->getQuery();

        $data = $qb->getResult(Query::HYDRATE_ARRAY);
        return $data;
    }

    public function getFilterRecords($search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select("count(e.id) as count")
            ->from("App\Entities\EmailTemplates", "e")
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('e.template_name', ':filter')
                )
            )
            ->setParameters(
                ['filter' => '%' . $search . '%']
            );

        $qb = $query->getQuery();

        $data = $qb->getResult(Query::HYDRATE_ARRAY);
        return $data;
    }
    public function countAllEmailTemplate()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('count(e.id) as count')
            ->from("App\Entities\EmailTemplates", "e")
            ->getQuery();

        return $query->getArrayResult();
    }
    public function getTemplateById($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('et')
            ->from('App\Entities\EmailTemplates', 'et')
            ->where('et.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getArrayResult()[0];
    }

    public function getAllTemplatesOption()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('et')
            ->from('App\Entities\EmailTemplates', 'et')
            ->getQuery();
        return $query->getArrayResult();
    }
}

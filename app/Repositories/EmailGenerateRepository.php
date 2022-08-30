<?php

namespace App\Repositories;

use App\Entities\EmailGenerate;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class EmailGenerateRepository extends EntityRepository
{

    protected $class = 'App\Entities\EmailGenerate';
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\EmailGenerate'));
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');
    }

    public function create(EmailGenerate $emailGenerate)
    {
        $this->_em->persist($emailGenerate);
        $this->_em->flush();

        return $emailGenerate;
    }

    public function prepareData($data)
    {
        return new EmailGenerate($data);
    }

    public function EmailGenerateOfId($id)
    {
        return $this->_em->getRepository('App\Entities\EmailGenerate')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(EmailGenerate $emailGenerate, $data)
    {
        if (array_key_exists("emp_id", $data)) {
            $emailGenerate->setEmpId($data["emp_id"]);
        }

        if (array_key_exists("created_by", $data)) {
            $emailGenerate->setCreatedBy($data["created_by"]);
        }

        if (array_key_exists("email_template_id", $data)) {
            $emailGenerate->setEmailTemplateId($data["email_template_id"]);
        }

        if (array_key_exists("spacing", $data)) {
            $emailGenerate->setSpacing($data["spacing"]);
        }

        if (array_key_exists("description", $data)) {
            $emailGenerate->setDescription($data["description"]);
        }

        $this->_em->persist($emailGenerate);
        $this->_em->flush();

        return $emailGenerate;
    }

    public function getAllGeneratedEmailsDatatable($order, $col, $search, $start, $length)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('eg.created_at,
                        eg.id,
                        u.id as emp_id,
                        u.firstname,
                        u.lastname,
                        u.profile_image,
                        uc.id as creator_id,
                        uc.firstname as creator_fname,
                        uc.lastname as creator_lname,
                        uc.profile_image as creator_profile_image,
                        et.template_name')
            ->from($this->class, 'eg')
            ->leftJoin('eg.emp_id', 'u')
            ->leftJoin('eg.created_by', 'uc')
            ->leftJoin('eg.email_template_id', 'et')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                    $query->expr()->like("et.template_name", ':filter'),
                    $query->expr()->like("DATE(et.created_at)", ':filter')
                )
            )
            ->orderBy(($col == 'firstname' ? 'u.' : ($col == 'template' ? 'et.' : 'eg.')) . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                [
                    'filter' => '%' . $search . '%'
                ]
            );

        $qb = $query->getQuery();

        $data = $qb->getArrayResult();
        return $data;
    }

    public function countFilteredRecords($search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(eg.id) as count')
            ->from($this->class, 'eg')
            ->leftJoin('eg.emp_id', 'u')
            ->leftJoin('eg.created_by', 'uc')
            ->leftJoin('eg.email_template_id', 'et')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                    $query->expr()->like("et.template_name", ':filter'),
                    $query->expr()->like("DATE(et.created_at)", ':filter')
                )
            )
            ->setParameters(
                [
                    'filter' => '%' . $search . '%'
                ]
            );

        $qb = $query->getQuery();

        $data = $qb->getArrayResult();
        return $data;
    }

    public function countAllGeneratedEmails()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(eg.id) as count')
            ->from($this->class, 'eg');
        $qb = $query->getQuery();

        $data = $qb->getArrayResult();
        return $data;
    }
}

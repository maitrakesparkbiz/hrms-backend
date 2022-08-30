<?php

namespace App\Repositories;

use App\Entities\CompanyPolicy;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class CompanyPolicyRepository extends EntityRepository
{
    private $class = 'App\Entities\CompanyPolicy';

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\CompanyPolicy'));
    }
    public function prepareData($data)
    {
        return new CompanyPolicy($data);
    }
    public function create(CompanyPolicy $company)
    {
        $this->_em->persist($company);
        $this->_em->flush();

        return $company;
    }

    public function delete(CompanyPolicy $companyPolicy)
    {
        $this->_em->remove($companyPolicy);
        $this->_em->flush();
    }

    public function CompanyPolicyOfId($id)
    {
        return $this->_em->getRepository('App\Entities\CompanyPolicy')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(CompanyPolicy $company_policy, $data)
    {
        if (isset($data["title"])) {
            $company_policy->setTitle($data["title"]);
        }
        if (isset($data["short_description"])) {
            $company_policy->setShortDescription($data["short_description"]);
        }
        if (isset($data["long_description"])) {
            $company_policy->setLongDescription($data["long_description"]);
        }
        if (array_key_exists("policy_doc", $data)) {
            $company_policy->setPolicyDoc($data["policy_doc"]);
        }
        if (isset($data["is_published"])) {
            $company_policy->setIsPublished($data["is_published"]);
        }

        $this->_em->persist($company_policy);
        $this->_em->flush();

        return $company_policy;
    }

    public function companyPolicyById($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('p')
            ->from($this->class, 'p')
            ->where('p.id=:id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getArrayResult()[0];
    }

    public function countCompanyPolicy($isSelf = false)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('count(cp.id) as count')
            ->from($this->class, 'cp');
        if ($isSelf) {
            $query->where('cp.is_published=1');
        }
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }


    public function countFilteredCompanyPolicy($search, $isSelf = false)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(p.id) as count')
            ->from($this->class, 'p')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('p.title', ':filter'),
                    $query->expr()->like('p.short_description', ':filter'),
                    $query->expr()->like('p.long_description', ':filter')
                )
            );
        if ($isSelf) {
            $query->andWhere('p.is_published=1');
        }
        $query->setParameters(['filter' => '%' . $search . '%']);
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }
    public function getCompanyPolicyDatatable($order, $col, $search, $start, $length, $isSelf = false)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('p')
            ->from($this->class, 'p')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like('p.title', ':filter'),
                    $query->expr()->like('p.short_description', ':filter'),
                    $query->expr()->like('p.long_description', ':filter')
                )
            );
        if ($isSelf) {
            $query->andWhere('p.is_published=1');
        }
        $query->orderBy('p.' . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                [
                    'filter' => '%' . $search . '%'
                ]
            );
        $qb = $query->getQuery();
        return $qb->getArrayResult();
    }
}

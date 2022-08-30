<?php

namespace App\Repositories;

use App\Entities\Company;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class CompanyRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\Company'));
    }
    public function prepareData($data)
    {
        return new Company($data);
    }
    public function create(Company $company)
    {
        $this->_em->persist($company);
        $this->_em->flush();

        return $company;
    }
    public function getCompanyById($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select("c,co,fi,ls,ti,cu,cd")
            ->from("App\Entities\Company", "c")
            ->leftJoin("c.timezone", "ti")
            ->leftJoin("c.country", "co")
            ->leftJoin("c.currency", "cu")
            ->leftJoin("c.datetimeformat", "cd")
            ->leftJoin("c.financial_year_start_month", "fi")
            ->leftJoin("c.leave_start_month", "ls")
            ->where("c.id = :id")
            ->setParameter("id", $id)
            ->getQuery();
        return $query->getArrayResult([0]);
    }
    public function CompanyOfId($id)
    {
        return $this->_em->getRepository('App\Entities\Company')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(Company $company, $data)
    {
        if (isset($data["name"])) {
            $company->setName($data["name"]);
        }
        if (isset($data["logo"])) {
            $company->setLogo($data["logo"]);
        }
        if (isset($data["logo_url"])) {
            $company->setLogo_url($data["logo_url"]);
        }
        if (isset($data["country"])) {
            $company->setCountry($data["country"]);
        }
        if (isset($data["contact_email"])) {
            $company->setContact_email($data["contact_email"]);
        }
        if (isset($data["contact_person"])) {
            $company->setContact_person($data["contact_person"]);
        }
        if (isset($data["contact_number"])) {
            $company->setContact_number($data["contact_number"]);
        }
        if (isset($data["contact_address"])) {
            $company->setContact_address($data["contact_address"]);
        }
        if (isset($data["from_email"])) {
            $company->setFrom_email($data["from_email"]);
        }
        if (isset($data["from_name"])) {
            $company->setFrom_name($data["from_name"]);
        }
        if (isset($data["currency"])) {
            $company->setCurrency($data["currency"]);
        }
        if (isset($data["currency_symbol"])) {
            $company->setCurrency_symbol($data["currency_symbol"]);
        }
        if (isset($data["website"])) {
            $company->setWebsite($data["website"]);
        }
        if (isset($data["timezone"])) {
            $company->setTimezone($data["timezone"]);
        }
        if (isset($data["financial_year_start_month"])) {
            $company->setFinancial_year_start_month($data["financial_year_start_month"]);
        }
        if (isset($data["leave_start_month"])) {
            $company->setLeaveStartMonth($data["leave_start_month"]);
        }
        if (isset($data["datetimeformat"])) {
            $company->setDatetimeformat($data["datetimeformat"]);
        }
        if (isset($data["default_break_time"])) {
            $company->setDefaultBreakTime($data["default_break_time"]);
        }


        $this->_em->persist($company);
        $this->_em->flush();

        return $company;
    }
    function getPath()
    {
        $query = $this->createQueryBuilder('g')
            ->select('g.logo')
            ->getQuery();
        return $query->getArrayResult();
    }

    function getDateFormat()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('c.id,op.value_text')
            ->from('App\Entities\Company', 'c')
            ->leftJoin('c.datetimeformat', 'op')
            ->getQuery();

        return $query->getArrayResult();
    }

    function getCompanyLetterData()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('c.name as COMPANY_NAME,
                    c.contact_address as CONTACT_ADDRESS')
            ->from('App\Entities\Company', 'c')
            ->getQuery();

        return $query->getArrayResult();
    }
    function getDefaultBreakTime()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('c.default_break_time')
            ->from('App\Entities\Company', 'c')
            ->getQuery();

        return $query->getArrayResult()[0]['default_break_time'];
    }
}

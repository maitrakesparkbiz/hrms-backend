<?php

namespace App\Repositories;

use App\Entities\EmailSetting;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class EmailSettingRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\EmailSetting'));
    }

    public function create(EmailSetting $emailSetting)
    {
        $this->_em->persist($emailSetting);
        $this->_em->flush();

        return $emailSetting;
    }

    public function prepareData($data)
    {
        return new EmailSetting($data);
    }

    public function EmailSettingOfId($id)
    {
        return $this->_em->getRepository('App\Entities\EmailSetting')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(EmailSetting $emailSetting, $data)
    {
        if (array_key_exists("admin_emails",$data)){
            $emailSetting->setAdminEmails($data["admin_emails"]);
        }

        if ( array_key_exists( "hr_emails", $data)) {
            $emailSetting->setHrEmails($data["hr_emails"]);
        }

        if ( array_key_exists( "cto_emails", $data)) {
            $emailSetting->setCtoEmails($data["cto_emails"]);
        }

        if ( array_key_exists( "hr_contact_number", $data)) {
            $emailSetting->setHrContactNumber($data["hr_contact_number"]);
        }

        if ( array_key_exists( "hr_name", $data)) {
            $emailSetting->setHrName($data["hr_name"]);
        }

        if (isset($data[ "company_site"])) {
            $emailSetting->setCompanySite($data["company_site"]);
        }

        if (isset($data["company_address"])) {
            $emailSetting->setCompanyAddress($data["company_address"]);
        }

        $this->_em->persist($emailSetting);
        $this->_em->flush();

        return $emailSetting;
    }

    public function getEmailSettings()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('es')
            ->from('App\Entities\EmailSetting', 'es')
            ->getQuery();

        return $query->getArrayResult();
    }
}
 
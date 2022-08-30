<?php


namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="email_setting")
 */

class EmailSetting
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $admin_emails;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $hr_emails;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $cto_emails;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $hr_contact_number;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $hr_name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $company_site;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $company_address;


    /**
     * @var \DateTime $created
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;

    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    protected $deletedAt;

    public function __construct($data)
    {
        $this->admin_emails = isset($data['admin_emails']) ? $data['admin_emails'] : '';
        $this->hr_emails = isset($data['hr_emails']) ? $data['hr_emails'] : '';
        $this->cto_emails = isset($data['cto_emails']) ? $data['cto_emails'] : '';
        $this->hr_contact_number = isset($data['hr_contact_number']) ? $data['hr_contact_number'] : '';
        $this->hr_name = isset($data['hr_name']) ? $data['hr_name'] : '';
        $this->company_site = isset($data['company_site']) ? $data['company_site'] : '';
        $this->company_address = isset($data['company_address']) ? $data['company_address'] : '';
    }

    /**
     * @return mixed
     */
    public function getAdminEmails()
    {
        return $this->admin_emails;
    }

    /**
     * @param mixed $admin_emails
     */
    public function setAdminEmails($admin_emails): void
    {
        $this->admin_emails = $admin_emails;
    }

    /**
     * @return mixed
     */
    public function getHrEmails()
    {
        return $this->hr_emails;
    }

    /**
     * @param mixed $hr_emails
     */
    public function setHrEmails($hr_emails): void
    {
        $this->hr_emails = $hr_emails;
    }

    /**
     * @return mixed
     */
    public function getCtoEmails()
    {
        return $this->cto_emails;
    }

    /**
     * @param mixed $cto_emails
     */
    public function setCtoEmails($cto_emails): void
    {
        $this->cto_emails = $cto_emails;
    }

    /**
     * @return mixed
     */
    public function getHrContactNumber()
    {
        return $this->hr_contact_number;
    }

    /**
     * @param mixed $hr_contact_number
     */
    public function setHrContactNumber($hr_contact_number): void
    {
        $this->hr_contact_number = $hr_contact_number;
    }

    /**
     * @return mixed
     */
    public function getHrName()
    {
        return $this->hr_name;
    }

    /**
     * @param mixed $hr_name
     */
    public function setHrName($hr_name): void
    {
        $this->hr_name = $hr_name;
    }

    /**
     * @return mixed
     */
    public function getCompanySite()
    {
        return $this->company_site;
    }

    /**
     * @param mixed $company_site
     */
    public function setCompanySite($company_site): void
    {
        $this->company_site = $company_site;
    }

    /**
     * @return mixed
     */
    public function getCompanyAddress()
    {
        return $this->company_address;
    }

    /**
     * @param mixed $company_address
     */
    public function setCompanyAddress($company_address): void
    {
        $this->company_address = $company_address;
    }
}
 
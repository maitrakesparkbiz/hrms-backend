<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="company")
 */
class Company
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     *
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $logo;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $logo_url;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $website;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Option_master",inversedBy="Company")
     * @ORM\JOinColumn(name="country",referencedColumnName="id")
     */
    protected $country;

    /**
     *
     * @ORM\Column(type="string")
     */
    protected $contact_email;

    /**
     *
     * @ORM\Column(type="string")
     */
    protected $contact_person;

    /**
     *
     * @ORM\Column(type="string")
     */
    protected $contact_number;

    /**
     *
     * @ORM\Column(type="text")
     */
    protected $contact_address;

    /**
     *
     * @ORM\Column(type="string")
     */
    protected $from_email;

    /**
     *
     * @ORM\Column(type="string")
     */
    protected $from_name;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Option_master",inversedBy="Company")
     * @ORM\JOinColumn(name="currency",referencedColumnName="id")
     */
    protected $currency;
    /**
     *
     * @ORM\ManyToOne(targetEntity="Option_master",inversedBy="Company")
     * @ORM\JOinColumn(name="datetimeformat",referencedColumnName="id")
     */
    protected $datetimeformat;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $currency_symbol;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Option_master",inversedBy="Company")
     * @ORM\JOinColumn(name="timezone",referencedColumnName="id")
     */
    protected $timezone;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Option_master",inversedBy="Company")
     * @ORM\JOinColumn(name="financial_year_start_month",referencedColumnName="id")
     */
    protected $financial_year_start_month;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Option_master",inversedBy="Company")
     * @ORM\JOinColumn(name="leave_start_month",referencedColumnName="id")
     */
    protected $leave_start_month;

    /**
     *
     * @ORM\Column(type="string")
     */
    protected $default_break_time;

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
        $this->name = isset($data["name"]) ? $data["name"] : "";
        $this->logo = isset($data["logo"]) ? $data["logo"] : "";
        $this->logo_url = isset($data["logo_url"]) ? $data["logo_url"] : "";
        $this->country = isset($data["country"]) ? $data["country"] : NULL;
        $this->contact_email = isset($data["contact_email"]) ? $data["contact_email"] : "";
        $this->contact_person = isset($data["contact_person"]) ? $data["contact_person"] : "";
        $this->contact_number = isset($data["contact_number"]) ? $data["contact_number"] : "";
        $this->contact_address = isset($data["contact_address"]) ? $data["contact_address"] : "";
        $this->from_email = isset($data["from_email"]) ? $data["from_email"] : "";
        $this->from_name = isset($data["from_name"]) ? $data["from_name"] : "";
        $this->currency = isset($data["currency"]) ? $data["currency"] : NULL;
        $this->datetimeformat = isset($data["datetimeformat"]) ? $data["datetimeformat"] : NULL;
        $this->currency_symbol = isset($data["currency_symbol"]) ? $data["currency_symbol"] : "";
        $this->default_break_time = isset($data["default_break_time"]) ? $data["default_break_time"] : "";
        $this->timezone = isset($data["timezone"]) ? $data["timezone"] : NULL;
        $this->financial_year_start_month = isset($data["financial_year_start_month"]) ? $data["financial_year_start_month"] : NULL;
        $this->website = isset($data["website"]) ? $data["website"] : '';
        $this->leave_start_month = isset($data["leave_start_month"]) ? $data["leave_start_month"] : NULL;
    }

    function getId()
    {
        return $this->id;
    }

    function getName()
    {
        return $this->name;
    }

    function getLogo()
    {
        return $this->logo;
    }

    /**
     * @return mixed
     */
    public function getLeaveStartMonth()
    {
        return $this->leave_start_month;
    }

    /**
     * @param mixed $leave_start_month
     */
    public function setLeaveStartMonth($leave_start_month): void
    {
        $this->leave_start_month = $leave_start_month;
    }


    /**
     * @return mixed
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param mixed $website
     */
    public function setWebsite($website): void
    {
        $this->website = $website;
    }

    function getLogo_url()
    {
        return $this->logo_url;
    }

    function getCountry()
    {
        return $this->country;
    }

    function getContact_email()
    {
        return $this->contact_email;
    }

    function getContact_person()
    {
        return $this->contact_person;
    }

    function getContact_number()
    {
        return $this->contact_number;
    }

    function getContact_address()
    {
        return $this->contact_address;
    }

    function getFrom_email()
    {
        return $this->from_email;
    }

    function getFrom_name()
    {
        return $this->from_name;
    }

    function getCurrency()
    {
        return $this->currency;
    }

    function getCurrency_symbol()
    {
        return $this->currency_symbol;
    }

    function getTimezone()
    {
        return $this->timezone;
    }


    public function getDatetimeformat()
    {
        return $this->datetimeformat;
    }


    public function setDatetimeformat($datetimeformat)
    {
        $this->datetimeformat = $datetimeformat;
    }

    function getFinancial_year_start_month()
    {
        return $this->financial_year_start_month;
    }

    function setName($name)
    {
        $this->name = $name;
    }

    function setLogo($logo)
    {
        $this->logo = $logo;
    }

    function setLogo_url($logo_url)
    {
        $this->logo_url = $logo_url;
    }

    function setCountry($country)
    {
        $this->country = $country;
    }

    function setContact_email($contact_email)
    {
        $this->contact_email = $contact_email;
    }

    function setContact_person($contact_person)
    {
        $this->contact_person = $contact_person;
    }

    function setContact_number($contact_number)
    {
        $this->contact_number = $contact_number;
    }

    function setContact_address($contact_address)
    {
        $this->contact_address = $contact_address;
    }

    function setFrom_email($from_email)
    {
        $this->from_email = $from_email;
    }

    function setFrom_name($from_name)
    {
        $this->from_name = $from_name;
    }

    function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    function setCurrency_symbol($currency_symbol)
    {
        $this->currency_symbol = $currency_symbol;
    }

    function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    function setFinancial_year_start_month($financial_year_start_month)
    {
        $this->financial_year_start_month = $financial_year_start_month;
    }

    /**
     * @return mixed
     */
    public function getDefaultBreakTime()
    {
        return $this->default_break_time;
    }

    /**
     * @param mixed $default_break_time
     */
    public function setDefaultBreakTime($default_break_time): void
    {
        $this->default_break_time = $default_break_time;
    }




}
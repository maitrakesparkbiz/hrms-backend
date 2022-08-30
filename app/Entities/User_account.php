<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;
/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="user_account")
 */
class User_account
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="Option_master",inversedBy="User_account")
     * @ORM\JoinColumn(name="account_type",referencedColumnName="id")
     */
    protected $account_type;     

    /**
     * 
     * @ORM\Column(type="string", nullable=true)
     */
    protected $account_holder_name;     

    /**
     * 
     * @ORM\Column(type="string", nullable=true)
     */
    protected $account_name;     

    /**
     * 
     * @ORM\Column(type="string", nullable=true)
     */
    protected $bank_code;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $crn_number;


    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $account_number;

    /**
     * 
     * @ORM\Column(type="string", nullable=true)
     */
    protected $bank_branch;     

    /**
     * 
     * @ORM\Column(type="string", nullable=true)
     */
    protected $bank_name;     
    /**
     * @ORM\ManyToOne(targetEntity="User",inversedBy="User_account")
     * @ORM\JOinColumn(name="user",referencedColumnName="id")
     */
    protected $user;  
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
         $this->account_type = isset($data["account_type"]) ? $data["account_type"] : NULL;
         $this->account_holder_name = isset($data["account_holder_name"]) ? $data["account_holder_name"] : "";
         $this->account_name = isset($data["account_name"]) ? $data["account_name"] : "";
         $this->bank_code = isset($data["bank_code"]) ? $data["bank_code"] : "";
         $this->bank_branch = isset($data["bank_branch"]) ? $data["bank_branch"] : "";
         $this->bank_name = isset($data["bank_name"]) ? $data["bank_name"] : "";
         $this->user = isset($data["user"]) ? $data["user"] : "";
         $this->crn_number = isset($data["crn_number"]) ? $data["crn_number"] : "";
         $this->account_number = isset($data["account_number"]) ? $data["account_number"] : "";
    }
    
    function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getAccountNumber()
    {
        return $this->account_number;
    }

    /**
     * @param mixed $account_number
     */
    public function setAccountNumber($account_number): void
    {
        $this->account_number = $account_number;
    }


    /**
     * @return mixed
     */
    public function getCrnNumber()
    {
        return $this->crn_number;
    }

    /**
     * @param mixed $crn_number
     */
    public function setCrnNumber($crn_number): void
    {
        $this->crn_number = $crn_number;
    }



    function getAccount_type()
    {
        return $this->account_type;
    }

    function getAccount_holder_name()
    {
        return $this->account_holder_name;
    }

    function getAccount_name()
    {
        return $this->account_name;
    }

    function getBank_code()
    {
        return $this->bank_code;
    }

    function getBank_branch()
    {
        return $this->bank_branch;
    }

    function setAccount_type($account_type)
    {
        $this->account_type = $account_type;
    }

    function setAccount_holder_name($account_holder_name)
    {
        $this->account_holder_name = $account_holder_name;
    }

    function setAccount_name($account_name)
    {
        $this->account_name = $account_name;
    }

    function setBank_code($bank_code)
    {
        $this->bank_code = $bank_code;
    }

    function setBank_branch($bank_branch)
    {
        $this->bank_branch = $bank_branch;
    }
    function getBank_name()
    {
        return $this->bank_name;
    }

    function setBank_name($bank_name)
    {
        $this->bank_name = $bank_name;
    }

    function getUser()
    {
        return $this->user;
    }

    function setUser($user)
    {
        $this->user = $user;
    }



}
<?php

namespace App\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Table(name="expense")
 */
class Expense
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;
    /**
     * @ORM\ManyToOne(targetEntity="Expense_Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category_id;
    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;
    /**
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $amount;

    /**
     *
     * @ORM\Column(type="date", nullable=true)
     */
    protected $bill_date;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $payment_method;


    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $status;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $merchant;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $receipt_image;


    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="emp_id", referencedColumnName="id")
     */
    protected $emp_id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="actioned_by", referencedColumnName="id")
     */
    protected $actioned_by;

    /**
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $is_expense;

    /**
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $is_claim;

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
        $this->title = isset($data["title"]) ? $data["title"] : "";
        $this->bill_date = isset($data["bill_date"]) ? $data["bill_date"] : NULL;
        $this->emp_id = isset($data["emp_id"]) ? $data["emp_id"] : NULL;
        $this->actioned_by = isset($data['actioned_by']) ? $data['actioned_by'] : NULL;
        $this->description = isset($data["description"]) ? $data["description"] : "";
        $this->status = isset($data["status"]) ? $data["status"] : "";
        $this->merchant = isset($data["merchant"]) ? $data["merchant"] : "";
        $this->payment_method = isset($data["payment_method"]) ? $data["payment_method"] : "";
        $this->category_id = isset($data["category_id"]) ? $data["category_id"] : NULL;
        $this->amount = isset($data["amount"]) ? $data["amount"] : NULL;
        $this->receipt_image = isset($data["receipt_image"]) ? $data["receipt_image"] : NULL;
        $this->is_expense = isset($data["is_expense"]) ? $data["is_expense"] : NULL;
        $this->is_claim = isset($data["is_claim"]) ? $data["is_claim"] : NULL;
    }

    function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getActionedBy()
    {
        return $this->actioned_by;
    }

    /**
     * @param mixed $actioned_by
     */
    public function setActionedBy($actioned_by): void
    {
        $this->actioned_by = $actioned_by;
    }

    /**
     * @return mixed
     */
    public function getisExpense()
    {
        return $this->is_expense;
    }

    /**
     * @param mixed $is_expense
     */
    public function setIsExpense($is_expense): void
    {
        $this->is_expense = $is_expense;
    }

    /**
     * @return mixed
     */
    public function getisClaim()
    {
        return $this->is_claim;
    }

    /**
     * @param mixed $is_claim
     */
    public function setIsClaim($is_claim): void
    {
        $this->is_claim = $is_claim;
    }



    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getCategoryId()
    {
        return $this->category_id;
    }

    /**
     * @param mixed $category_id
     */
    public function setCategoryId($category_id): void
    {
        $this->category_id = $category_id;
    }


    public function getDescription()
    {
        return $this->description;
    }


    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getAmount()
    {
        return $this->amount;
    }


    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getBillDate()
    {
        return $this->bill_date;
    }

    public function setBillDate($bill_date)
    {
        $this->bill_date = $bill_date;
    }

    public function getPaymentMethod()
    {
        return $this->payment_method;
    }

    public function setPaymentMethod($payment_method)
    {
        $this->payment_method = $payment_method;
    }

    public function getMerchant()
    {
        return $this->merchant;
    }


    public function setMerchant($merchant)
    {
        $this->merchant = $merchant;
    }

    public function getReceiptImage()
    {
        return $this->receipt_image;
    }

    public function setReceiptImage($receipt_image)
    {
        $this->receipt_image = $receipt_image;
    }

    public function getEmpId()
    {
        return $this->emp_id;
    }

    public function setEmpId($emp_id)
    {
        $this->emp_id = $emp_id;
    }


    public function getStatus()
    {
        return $this->status;
    }


    public function setStatus($status)
    {
        $this->status = $status;
    }


}
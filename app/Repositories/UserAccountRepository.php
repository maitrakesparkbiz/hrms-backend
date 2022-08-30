<?php

namespace App\Repositories;

use App\Entities\User_account;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class UserAccountRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\User_account'));
    }

    public function create(User_account $user_account)
    {
        $this->_em->persist($user_account);
        $this->_em->flush();

        return $user_account;
    }

    public function prepareData($data)
    {
        return new User_account($data);
    }

    public function User_accountOfId($id)
    {
        return $this->_em->getRepository('App\Entities\User_account')->findOneBy([
            "id" => $id
        ]);
    }

    public function delete(User_account $user_account)
    {
        $this->_em->remove($user_account);
        $this->_em->flush();
    }

    public function update(User_account $user_account, $data)
    {
        if (isset($data["account_type"])) {
            $user_account->setAccount_type($data["account_type"]);
        }
        if (isset($data["account_holder_name"])) {
            $user_account->setAccount_holder_name($data["account_holder_name"]);
        }
        if (isset($data["account_name"])) {
            $user_account->setAccount_name($data["account_name"]);
        }
        if (isset($data["bank_code"])) {
            $user_account->setBank_code($data["bank_code"]);
        }
        if (isset($data["bank_branch"])) {
            $user_account->setBank_branch($data["bank_branch"]);
        }
        if (isset($data["bank_name"])) {
            $user_account->setBank_name($data["bank_name"]);
        }
        if (isset($data["user"])) {
            $user_account->setUser($data["user"]);
        }
        if(isset($data["crn_number"])){
            $user_account->setCrnNumber($data["crn_number"]);
        }
        if(isset($data["account_number"])){
            $user_account->setAccountNumber($data["account_number"]);
        }

        $this->_em->persist($user_account);
        $this->_em->flush();

        return $user_account;
    }



}

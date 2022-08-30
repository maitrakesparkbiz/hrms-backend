<?php

namespace App\Repositories;

use App\Entities\User_work_experiance;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class UserworkexperienceRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\User_work_experiance'));
    }

    public function create(User_work_experiance $user_work_experiance)
    {
        $this->_em->persist($user_work_experiance);
        $this->_em->flush();

        return $user_work_experiance;
    }

    public function prepareData($data)
    {
        return new User_work_experiance($data);
    }

    public function User_work_experianceOfId($id)
    {
        return $this->_em->getRepository('App\Entities\User_work_experiance')->findOneBy([
            "id" => $id
        ]);
    }

    public function delete(User_work_experiance $user_work_experiance)
    {
        $this->_em->remove($user_work_experiance);
        $this->_em->flush();
    }

    public function update(User_work_experiance $user_work_experiance, $data)
    {
        if (isset($data["company_name"])) {
            $user_work_experiance->setCompanyName($data["company_name"]);
        }
        if (isset($data["designation"])) {
            $user_work_experiance->setDesignation($data["designation"]);
        }
        if (isset($data["from_date"])) {
            $user_work_experiance->setFrom_date($data["from_date"]);
        }
        if (isset($data["to_date"])) {
            $user_work_experiance->setTo_date($data["to_date"]);
        }
        if (isset($data["details"])) {
            $user_work_experiance->setDetails($data["details"]);
        }
        if (isset($data["user"])) {
            $user_work_experiance->setUser($data["user"]);
        }
        if (array_key_exists('relieving_letter', $data) || $data['relieving_letter'] == null) {
            $user_work_experiance->setRelievingLetter($data['relieving_letter']);
        }
        if (array_key_exists('exp_letter', $data) || $data['exp_letter'] == null) {
            $user_work_experiance->setExpLetter($data['exp_letter']);
        }
        if (array_key_exists('salary_slip', $data) || $data['salary_slip'] == null) {
            $user_work_experiance->setSalarySlip($data['salary_slip']);
        }

        $this->_em->persist($user_work_experiance);
        $this->_em->flush();

        return $user_work_experiance;
    }

}

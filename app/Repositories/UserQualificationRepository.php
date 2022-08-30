<?php

namespace App\Repositories;

use App\Entities\User_qualification;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class UserQualificationRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\User_qualification'));
    }

    public function create(User_qualification $user_qualification)
    {
        $this->_em->persist($user_qualification);
        $this->_em->flush();

        return $user_qualification;
    }

    public function prepareData($data)
    {
        return new User_qualification($data);
    }

    public function User_qualificationOfId($id)
    {
        return $this->_em->getRepository('App\Entities\User_qualification')->findOneBy([
            "id" => $id
        ]);
    }

    public function delete(User_qualification $user_qualification)
    {
        $this->_em->remove($user_qualification);
        $this->_em->flush();
    }

    public function update(User_qualification $user_qualification, $data)
    {

        if (isset($data["education_type"]))
        {
            $user_qualification->setEducation_type($data["education_type"]);
        }
        if (isset($data["account_holder_name"]))
        {
            $user_qualification->setAccount_holder_name($data["account_holder_name"]);
        }
        if (isset($data["university_name"]))
        {
            $user_qualification->setUniversity_name($data["university_name"]);
        }
        if (isset($data["start_date"]))
        {
            $user_qualification->setStart_date($data["start_date"]);
        }
        if (isset($data["end_date"]))
        {
            $user_qualification->setEnd_date($data["end_date"]);
        }
        if (isset($data["details"]))
        {
            $user_qualification->setDetails($data["details"]);
        }
        if (isset($data["degree"]))
        {
            $user_qualification->setDegree($data["degree"]);
        }
        if (array_key_exists('doc_copy',$data) || $data['doc_copy'] == null)
        {
            $user_qualification->setDocCopy($data["doc_copy"]);
        }
        if (isset($data["user"]))
        {
            $user_qualification->setUser($data["user"]);
        }

        $this->_em->persist($user_qualification);
        $this->_em->flush();

        return $user_qualification;
    }


}

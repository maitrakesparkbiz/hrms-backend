<?php

namespace App\Repositories;

use App\Entities\Comments;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

class CommentsRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\Comments'));
    }

    public function prepareData($data)
    {
        return new Comments($data);
    }

    public function create(Comments $comments)
    {
        $this->_em->persist($comments);
        $this->_em->flush();

        return $comments;
    }

    public function CommentsOfId($id)
    {
        return $this->_em->getRepository('App\Entities\Comments')->findOneBy([
            "id" => $id
        ]);
    }

    public function update(Comments $comments, $data){
        if(isset($data['emp_id'])){
            $comments->setEmpId($data['emp_id']);
        }

        if(isset($data['check_in_id'])){
            $comments->setCheckInId($data['check_in_id']);
        }

        if(isset($data['comment_text'])){
            $comments->setCommentText($data['comment_text']);
        }

        if(isset($data['response_text'])){
            $comments->setResponseText($data['response_text']);
        }

        $this->_em->persist($comments);
        $this->_em->flush();

        return $comments;
    }

    public function getCommentsToday($check_in_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('c')
            ->from('App\Entities\Comments', 'c')
            ->leftJoin('c.check_in_id', 'ch')
            ->where('ch.id=:id')
            ->setParameter('id', $check_in_id)
            ->getQuery();

        return $query->getArrayResult();
    }
}

?>
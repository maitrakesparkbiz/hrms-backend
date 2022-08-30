<?php

namespace App\Repositories;

use App\Entities\Team;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;


class TeamRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\Team'));
    }

    public function prepareData($data)
    {
        return new Team($data);
    }

    public function create(Team $team)
    {
        $this->_em->persist($team);
        $this->_em->flush();

        return $team->getId();
    }

    public function teamOfId($id)
    {
        return $this->_em->getRepository('App\Entities\Team')->find([
            'id' => $id
        ]);
    }

    public function update(Team $team, $data)
    {
        if (isset($data['leader'])) {
            $team->setLeader($data['leader']);
        }

        $this->_em->persist($team);
        $this->_em->flush();

        return $team;
    }

    public function deleteTeam($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->delete()
            ->from('App\Entities\Team', 't')
            ->where("t.id = :id")
            ->setParameter("id", $id)
            ->getQuery();
        return $query->getArrayResult();
    }

    public function countAllTeams()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(t) as count')
            ->from('App\Entities\Team', 't');
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function countFilteredTeams($search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(t) as count')
            ->from('App\Entities\Team', 't')
            ->leftJoin('t.leader', 'e')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like("concat(e.firstname,' ',e.lastname)", ':filter'),
                    $query->expr()->like("concat(e.lastname,' ',e.firstname)", ':filter')
                )
            )
            ->setParameter('filter', '%' . $search . '%');
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function getAllTeams($col, $order, $search, $start, $length)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('t.id', 'e.id as emp_id', 'e.firstname', 'e.lastname', 'e.profile_image', 'count(emps) as members')
            ->from('App\Entities\Team', 't')
            ->leftJoin('t.team_employee', 'te')
            ->leftJoin('t.leader', 'e')
            ->leftJoin('te.member', 'emps')
            ->having(
                $query->expr()->orX(
                    $query->expr()->like("concat(e.firstname,' ',e.lastname)", ':filter'),
                    $query->expr()->like("concat(e.lastname,' ',e.firstname)", ':filter'),
                    $query->expr()->like("members", ':filter')
                )
            )
            ->groupBy('t.id');
        $query->orderBy(($col == 'leader' ? 'e.firstname' : ($col == 'members' ? 'members' : 't.' . $col)), strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length);

        $query->setParameters(
            ['filter' => '%' . $search . '%']
        );

        $qb = $query->getQuery();

        $data = $qb->getResult(Query::HYDRATE_ARRAY);
        return $data;
    }

    public function getTeamById($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('t', 'te', 'e', 'emps')
            ->from('App\Entities\Team', 't')
            ->leftJoin('t.team_employee', 'te')
            ->leftJoin('t.leader', 'e')
            ->leftJoin('te.member', 'emps')
            ->where('t.id=:id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getArrayResult()[0];
    }

    public function getSelfTeamData($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('t,te,emps,d')
            ->from('App\Entities\Team', 't')
            ->leftJoin('t.team_employee', 'te')
            ->leftJoin('te.member', 'emps')
            ->leftJoin('emps.department', 'd')
            ->where('t.leader=:emp_id')
            ->setParameter('emp_id', $emp_id)
            ->getQuery();

        return $query->getArrayResult()[0];
    }

    public function checkLeader($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('t')
            ->from('App\Entities\Team', 't')
            ->where('t.leader=:emp')
            ->setParameter('emp', $emp_id)
            ->getQuery();

        return $query->getArrayResult();
    }
}

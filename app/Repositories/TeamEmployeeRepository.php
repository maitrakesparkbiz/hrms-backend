<?php

namespace App\Repositories;

use App\Entities\TeamEmployee;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class TeamEmployeeRepository extends EntityRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\TeamEmployee'));
    }

    public function prepareData($data)
    {
        return new TeamEmployee($data);
    }

    public function create(TeamEmployee $teamEmployee)
    {
        $this->_em->persist($teamEmployee);
        $this->_em->flush();

        return $teamEmployee;
    }

    public function teamOfId($id)
    {
        return $this->_em->getRepository('App\Entities\TeamEmployee')->find([
            'id' => $id
        ]);
    }

    public function update(TeamEmployee $teamEmployee, $data)
    {
        if (isset($data['team_id'])) {
            $teamEmployee->setTeamId($data['team_id']);
        }

        if (isset($data['member'])) {
            $teamEmployee->setMember($data['member']);
        }

        $this->_em->persist($teamEmployee);
        $this->_em->flush();

        return $teamEmployee;
    }

    public function delete($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->delete()
            ->from('App\Entities\TeamEmployee', 't')
            ->where("t.team_id = :id")
            ->setParameter("id", $id)
            ->getQuery();
        return $query->getArrayResult();
    }

    public function getSelfTeamData($col, $order, $search, $start, $length, $emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('te,emps,d')
            ->from('App\Entities\TeamEmployee', 'te')
            ->leftJoin('te.team_id', 't')
            ->leftJoin('te.member', 'emps')
            ->leftJoin('emps.department', 'd')
            ->where('t.leader=:emp_id')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like("concat(emps.firstname,' ',emps.lastname)", ':filter'),
                    $query->expr()->like("concat(emps.lastname,' ',emps.firstname)", ':filter'),
                    $query->expr()->like("d.name", ':filter')
                )
            )
            ->orderBy(($col == 'firstname' ? 'emps.' : 'd.') . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(['emp_id' => $emp_id, 'filter' => '%' . $search . '%']);
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function countSelfTeam($emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(te) as count')
            ->from('App\Entities\TeamEmployee', 'te')
            ->leftJoin('te.team_id', 't')
            ->where('t.leader=:emp_id')
            ->setParameters(['emp_id' => $emp_id]);
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function countFilteredRows($search, $emp_id)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(te) as count')
            ->from('App\Entities\TeamEmployee', 'te')
            ->leftJoin('te.team_id', 't')
            ->leftJoin('te.member', 'emps')
            ->leftJoin('emps.department', 'd')
            ->where('t.leader=:emp_id')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like("concat(emps.firstname,' ',emps.lastname)", ':filter'),
                    $query->expr()->like("concat(emps.lastname,' ',emps.firstname)", ':filter'),
                    $query->expr()->like("d.name", ':filter')
                )
            )
            ->setParameters(['emp_id' => $emp_id, 'filter' => '%' . $search . '%']);
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }
}

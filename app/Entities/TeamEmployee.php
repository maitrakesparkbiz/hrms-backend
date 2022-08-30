<?php
namespace App\Entities;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="team_employee")
 */

class TeamEmployee{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     */
    protected $team_id;


    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="member", referencedColumnName="id")
     */
    protected $member;

    public function __construct($data)
    {
        $this->team_id = isset($data["team_id"]) ? $data["team_id"] : NULL;
        $this->member = isset($data["member"]) ? $data["member"] : NULL;
    }




    /**
     * @return mixed
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * @param mixed $member
     */
    public function setMember($member): void
    {
        $this->member = $member;
    }

    /**
     * @return mixed
     */
    public function getTeamId()
    {
        return $this->team_id;
    }

    /**
     * @param mixed $team_id
     */
    public function setTeamId($team_id): void
    {
        $this->team_id = $team_id;
    }

}

?>


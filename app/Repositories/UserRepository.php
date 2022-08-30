<?php

namespace App\Repositories;

use App\Entities\User;
use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class UserRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('App\Entities\User'));
    }

    function getUser($id)
    {
        return $this->_em->getRepository('App\Entities\User')->find($id);
    }

    public function UserOfId($id)
    {
        return $this->_em->getRepository('App\Entities\User')->findOneBy([
            'id' => $id
        ]);
    }

    public function prepareData($data)
    {
        return new User($data);
    }

    public function create(User $user)
    {

        $this->_em->persist($user);
        $this->_em->flush();
        return $user->getId();
    }

    function saveUser(User $user)
    {
        $this->_em->persist($user);
        $this->_em->flush($user);
        return $user;
    }

    function getProfile($id)
    {
        $query = $this->createQueryBuilder('u')
            ->select('partial u.{id,name,email}', 'r', 'p')
            ->leftJoin('u.roles', 'r')
            ->leftJoin('r.permissions', 'p')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery();
        return $query->getArrayResult();
    }

    function getUsers()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('u.id', 'u.firstname', 'u.lastname', 'u.employee_id', 'u.profile_image', 'dep.name as departmentname', 'u.on_training', 'u.probation_end_date', 'u.joining_date', 'l.name as location')
            ->from('App\Entities\User', 'u')
            ->leftjoin('u.department', 'dep')
            ->leftJoin('u.location', 'l')
            ->where('u.user_exit_status=:status')
            ->setParameter('status', '1')
            ->orderBy('u.created_at', 'DESC')
            ->getQuery();
        return $query->getArrayResult();
    }

    function getAllUsersSelf()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('u.id', 'u.firstname', 'u.lastname', 'u.profile_image')
            ->from('App\Entities\User', 'u')
            ->where('u.user_exit_status=:status')
            ->setParameter('status', '1')
            ->orderBy('u.created_at', 'DESC')
            ->getQuery();
        return $query->getArrayResult();
    }

    function getEmployeeDeptWise()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('u.id', 'u.firstname', 'u.lastname', 'u.employee_id', 'u.profile_image', 'dep.name as departmentname', 'dep.id as dep_id')
            ->from('App\Entities\User', 'u')
            ->leftjoin('u.department', 'dep')
            ->where('u.user_exit_status=:status')
            ->setParameter('status', '1')
            ->getQuery();
        return $query->getArrayResult();
    }

    public function countFilteredRecords($endDuration, $duration, $statusSearch, $search, $durationFlag)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('COUNT(u.id) as active_count')
            ->from('App\Entities\User', 'u')
            ->leftjoin('u.department', 'dep')
            ->leftJoin('u.location', 'l')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                    $query->expr()->like('u.employee_id', ':filter'),
                    $query->expr()->like('dep.name', ':filter'),
                    $query->expr()->like('l.name', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->like('u.user_exit_status', ':active_status')
                )
            );
        if (!$durationFlag) {
            $query->andWhere(
                $query->expr()->andX(
                    $query->expr()->lte('u.joining_date', ':endDuration')
                ),
                $query->expr()->andX(
                    $query->expr()->gte('u.joining_date', ':duration')
                )
            );
        }
        $query->setParameters([
            'filter' => '%' . $search . '%',
            'active_status' => '%' . ($statusSearch == 'all' ? '' : (int)$statusSearch) . '%'
        ]);
        if (!$durationFlag) {
            $query->setParameter('endDuration', $endDuration)
                ->setParameter('duration', $duration == 'all' ? '%%' : $duration);
        }
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function countActiveEmployee()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('COUNT(u.id) as active_count')
            ->from('App\Entities\User', 'u')
            ->getQuery();

        return $query->getArrayResult();
    }

    function getDatatableUsers($col, $order, $search, $start, $length, $duration, $statusSearch, $endDuration, $durationFlag)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('u.id', 'u.firstname', 'u.lastname', 'u.employee_id', 'u.profile_image', 'dep.name as departmentname', 'u.on_training', 'u.probation_end_date', 'u.joining_date', 'l.name as location', 'l.alt_sat')
            ->from('App\Entities\User', 'u')
            ->leftjoin('u.department', 'dep')
            ->leftJoin('u.location', 'l')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                    $query->expr()->like('u.employee_id', ':filter'),
                    $query->expr()->like('dep.name', ':filter'),
                    $query->expr()->like('l.name', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->like('u.user_exit_status', ':active_status')
                )
            );
        if (!$durationFlag) {
            $query->andWhere(
                $query->expr()->andX(
                    $query->expr()->lte('u.joining_date', ':endDuration')
                ),
                $query->expr()->andX(
                    $query->expr()->gte('u.joining_date', ':duration')
                )
            );
        }
        $query->orderBy(($col == 'name' ? 'dep.' : 'u.') . $col, strtoupper($order))
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                [
                    'filter' => '%' . $search . '%',
                    'active_status' => $statusSearch == 'all' ? '%%' : (int)$statusSearch
                ]
            );
        if (!$durationFlag) {
            $query->setParameter('endDuration', $endDuration)
                ->setParameter('duration', $duration == 'all' ? '%%' : $duration);
        }

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    function getContactUsers($search, $start, $length, $statusSearch)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('u,st,ge')
            ->from('App\Entities\User', 'u')
            ->leftjoin('u.status', 'st')
            ->leftjoin('u.gender', 'ge')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                    $query->expr()->like('u.employee_id', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->like('u.user_exit_status', ':active_status')
                )
            )
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameters(
                [
                    'filter' => '%' . $search . '%',
                    'active_status' => '%' . ($statusSearch == 'all' ? '' : (int)$statusSearch) . '%'
                ]
            );

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    function countContactUsers($search, $statusSearch)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('count(u) as active_count')
            ->from('App\Entities\User', 'u')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter'),
                    $query->expr()->like('u.employee_id', ':filter')
                ),
                $query->expr()->andX(
                    $query->expr()->like('u.user_exit_status', ':active_status')
                )
            )
            ->setParameters(
                [
                    'filter' => '%' . $search . '%',
                    'active_status' => '%' . ($statusSearch == 'all' ? '' : (int)$statusSearch) . '%'
                ]
            );

        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    function getManageUsers()
    {
        $query = $this->createQueryBuilder('u')
            ->select('u.id', 'u.firstname', 'u.lastname', 'u.employee_id')
            ->where('u.is_manager = :is_manager')
            ->andWhere('u.user_exit_status=:status')
            ->setParameter('is_manager', 1)
            ->setParameter('status', '1')
            ->getQuery();
        return $query->getArrayResult();
    }


    function update(User $user, $data)
    {
        if (isset($data['password'])) {
            $user->setPassword($data['password']);
            $user->setPassword($data['password']);
        }
        $this->_em->persist($user);
        $this->_em->flush();

        return $user;
    }

    function getUserLocation($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('l.id,l.working_days,l.alt_sat')
            ->from('App\Entities\User', 'u')
            ->leftJoin('u.location', 'l')
            ->where('u.id=:e_id')
            ->setParameter('e_id', $emp_id)
            ->getQuery();

        return $query->getArrayResult()[0];
    }

    function getEmployeeByIdSelf($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('u,st,ge,user_work,user_qualification,edt')
            ->from('App\Entities\User', 'u')
            ->leftjoin('u.status', 'st')
            ->leftjoin('u.gender', 'ge')
            ->leftjoin('u.user_work', 'user_work')
            ->leftjoin('u.user_qualification', 'user_qualification')
            ->leftjoin('user_qualification.education_type', 'edt')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery();
        $data = $query->getResult(Query::HYDRATE_ARRAY)[0];
        return $data;
    }

    function getEmployeeByID($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('u,r,st,ge,dep,des,lo,re,pracc,user_account,user_work,user_qualification,act,edt,ul')
            ->from('App\Entities\User', 'u')
            ->leftjoin('u.roles', 'r')
            ->leftjoin('u.status', 'st')
            ->leftjoin('u.gender', 'ge')
            ->leftjoin('u.department', 'dep')
            ->leftjoin('u.designation', 'des')
            ->leftjoin('u.location', 'lo')
            ->leftjoin('u.report_to', 're')
            ->leftjoin('u.primary_account', 'pracc')
            ->leftjoin('u.user_work', 'user_work')
            ->leftjoin('u.user_qualification', 'user_qualification')
            ->leftjoin('user_qualification.education_type', 'edt')
            ->leftjoin('u.user_account', 'user_account')
            ->leftjoin('user_account.account_type', 'act')
            ->leftJoin('u.user_leaves', 'ul')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery();
        $data = $query->getResult(Query::HYDRATE_ARRAY)[0];
        return $data;
    }

    function checkEmployeeId($emp_id)
    {
        $query = $this->createQueryBuilder('u')
            ->select('u.employee_id')
            ->where('u.employee_id = :employee_id')
            ->setParameter('employee_id', $emp_id)
            ->getQuery();
        return $query->getArrayResult();
    }

    function checkEmployeeIdUpdate($emp_id, $id)
    {


        $query = $this->_em->createQueryBuilder();
        $qb = $query->select("u.employee_id")
            ->from("App\Entities\User", "u")
            ->where("u.id != :id")
            ->andWhere("u.employee_id = :employee_id")
            ->setParameter("id", $id)
            ->setParameter("employee_id", $emp_id)
            ->getQuery();
        return $qb->getArrayResult();
    }


    public function updateEmployee(User $user, $data)
    {
        if (isset($data['firstname'])) {
            $user->setFirstname($data['firstname']);
        }
        if (isset($data['lastname'])) {
            $user->setLastname($data['lastname']);
        }
        if (isset($data['employee_id'])) {
            $user->setEmployeeId($data['employee_id']);
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['company_email'])) {
            $user->setCompanyEmail($data['company_email']);
        }
        if (isset($data['about'])) {
            $user->setAbout($data['about']);
        }
        if (isset($data['company_name'])) {
            $user->setCompanyName($data['company_name']);
        }
        if (isset($data['password'])) {
            $user->setPassword($data['password']);
        }
        if (isset($data['status'])) {
            $user->setStatus($data['status']);
        }
        if (isset($data['gender'])) {
            $user->setGender($data['gender']);
        }
        if (isset($data['department'])) {
            $user->setDepartment($data['department']);
        }
        if (isset($data['designation'])) {
            $user->setDesignation($data['designation']);
        }
        if (isset($data['location'])) {
            $user->setLocation($data['location']);
        }
        if (isset($data['roles'])) {
            $user->setRoles($data['roles']);
        }
        if (isset($data['address'])) {
            $user->setAddress($data['address']);
        }
        if (isset($data['per_address'])) {
            $user->setPerAddress($data['per_address']);
        }
        if (isset($data['exit_date'])) {
            $user->setExitDate($data['exit_date']);
        }
        if (isset($data['user_exit_status'])) {
            $user->setUserExitStatus($data['user_exit_status']);
        }
        if (isset($data['profile_image']) || ($data['profile_image'] == null)) {
            $user->setProfileImage($data['profile_image']);
        }
        if (isset($data['allowed_login'])) {
            $user->setAllowedLogin($data['allowed_login']);
        }

        if (array_key_exists('on_training', $data)) {
            $user->setOnTraining($data['on_training']);
        }
        if (isset($data['is_manager'])) {
            $user->setIsManager($data['is_manager']);
        }
        if (isset($data['marital_status'])) {
            $user->setMaritalStatus($data['marital_status']);
        }
        if (isset($data['probation_end_date'])) {
            $user->setProbationEndDate($data['probation_end_date']);
        }
        if (isset($data['joining_date'])) {
            $user->setJoiningDate($data['joining_date']);
        }
        if (isset($data['increment_date'])) {
            $user->setIncrementDate($data['increment_date']);
        }
        if (isset($data['date_of_birth'])) {
            $user->setDateOfBirth($data['date_of_birth']);
        }
        if (isset($data['marriage_anniversary_date']) || ($data['marriage_anniversary_date'] == null)) {
            $user->setMarriageAnniversaryDate($data['marriage_anniversary_date']);
        }
        if (isset($data['contact_no']) || ($data['contact_no'] == null)) {
            $user->setContactNo($data['contact_no']);
        }
        if (isset($data['emergency_contact_no']) || ($data['emergency_contact_no'] == null)) {
            $user->setEmergencyContactNo($data['emergency_contact_no']);
        }
        if (isset($data['emergency_contact_person']) || ($data['emergency_contact_person'] == null)) {
            $user->setEmergencyContactPerson($data['emergency_contact_person']);
        }
        if (isset($data['driving_license_number']) || ($data['driving_license_number'] == null)) {
            $user->setDrivingLicenseNumber($data['driving_license_number']);
        }
        if (isset($data['pan_number']) || ($data['pan_number'] == null)) {
            $user->setPanNumber($data['pan_number']);
        }
        if (isset($data['aadhar_number']) || ($data['aadhar_number'] == null)) {
            $user->setAadharNumber($data['aadhar_number']);
        }
        if (isset($data['voter_id_number']) || ($data['voter_id_number'] == null)) {
            $user->setVoterIdNumber($data['voter_id_number']);
        }
        if (isset($data['uan_number']) || ($data['uan_number'] == null)) {
            $user->setUanNumber($data['uan_number']);
        }
        if (isset($data['pf_number']) || ($data['pf_number'] == null)) {
            $user->setPfNumber($data['pf_number']);
        }
        if (isset($data['esic_number']) || ($data['esic_number'] == null)) {
            $user->setEsicNumber($data['esic_number']);
        }
        if (isset($data['driving_license_image']) || ($data['driving_license_image'] == null)) {
            $user->setDrivingLicenseImage($data['driving_license_image']);
        }
        if (isset($data['other_work_experirnce']) || ($data['other_work_experirnce'] == null)) {
            $user->setOtherWorkExperirnce($data['other_work_experirnce']);
        }

        if (isset($data['pan_id_image']) || ($data['pan_id_image'] == null)) {
            $user->setPanIdImage($data['pan_id_image']);
        }
        if (isset($data['aadhar_id_image']) || ($data['aadhar_id_image'] == null)) {
            $user->setAadharIdImage($data['aadhar_id_image']);
        }
        if (isset($data['voter_id_image']) || ($data['voter_id_image'] == null)) {
            $user->setVoterIdImage($data['voter_id_image']);
        }
        if (isset($data['offer_later_file']) || ($data['offer_later_file'] == null)) {
            $user->setOfferLaterFile($data['offer_later_file']);
        }
        if (isset($data['joining_letter_file']) || ($data['joining_letter_file'] == null)) {
            $user->setJoiningLetterFile($data['joining_letter_file']);
        }
        if (isset($data['contract_file']) || ($data['contract_file'] == null)) {
            $user->setContractFile($data['contract_file']);
        }
        if (isset($data['resume_file']) || ($data['resume_file'] == null)) {
            $user->setResumeFile($data['resume_file']);
        }


        if (isset($data['appointment_letter']) || ($data['appointment_letter'] == null)) {
            $user->setAppointmentLetter($data['appointment_letter']);
        }
        if (isset($data['increment_letter']) || ($data['increment_letter'] == null)) {
            $user->setIncrementLetter($data['increment_letter']);
        }
        if (isset($data['promotion_letter']) || ($data['promotion_letter'] == null)) {
            $user->setPromotionLetter($data['promotion_letter']);
        }
        if (isset($data['relieving_letter']) || ($data['relieving_letter'] == null)) {
            $user->setRelievingLetter($data['relieving_letter']);
        }
        if (isset($data['exp_letter']) || ($data['exp_letter'] == null)) {
            $user->setExpLetter($data['exp_letter']);
        }
        if (isset($data['appreciation_letter']) || ($data['appreciation_letter'] == null)) {
            $user->setAppreciationLetter($data['appreciation_letter']);
        }
        if (isset($data['document_returns_letter']) || ($data['document_returns_letter'] == null)) {
            $user->setDocumentReturnsLetter($data['document_returns_letter']);
        }
        if (isset($data['no_due_certificate']) || ($data['no_due_certificate'] == null)) {
            $user->setNoDueCertificate($data['no_due_certificate']);
        }
        if (isset($data['acknowledgement_letter']) || ($data['acknowledgement_letter'] == null)) {
            $user->setAcknowledgementLetter($data['acknowledgement_letter']);
        }
        if (isset($data['warning_letter']) || ($data['warning_letter'] == null)) {
            $user->setWarningLetter($data['warning_letter']);
        }

        if (isset($data['lc']) || ($data['lc'] == null)) {
            $user->setLc($data['lc']);
        }
        if (isset($data['marksheet']) || ($data['marksheet'] == null)) {
            $user->setMarksheet($data['marksheet']);
        }

        if (isset($data['primary_account']) || (array_key_exists('primary_account', $data))) {
            $user->setPrimaryAccount($data['primary_account']);
        }
        if (isset($data['passport_number'])) {
            $user->setPassportNumber($data['passport_number']);
        }
        if (isset($data['passport_issue_date'])) {
            $user->setPassportIssueDate($data['passport_issue_date']);
        }
        if (isset($data['passport_expiry_date'])) {
            $user->setPassportExpiryDate($data['passport_expiry_date']);
        }
        if (isset($data['passport_image']) || ($data['passport_image'] == null)) {
            $user->setPassportImage($data['passport_image']);
        }
        if (isset($data['visa_number'])) {
            $user->setVisaNumber($data['visa_number']);
        }
        if (isset($data['visa_issue_date'])) {
            $user->setVisaIssueDate($data['visa_issue_date']);
        }
        if (isset($data['visa_expiry_date'])) {
            $user->setVisaExpiryDate($data['visa_expiry_date']);
        }
        if (isset($data['visa_image']) || ($data['visa_image'] == null)) {
            $user->setVisaImage($data['visa_image']);
        }
        if (isset($data['slack_username'])) {
            $user->setSlackUsername($data['slack_username']);
        }
        if (isset($data['linkdin_username'])) {
            $user->setLinkdinUsername($data['linkdin_username']);
        }
        if (isset($data['facebook_username'])) {
            $user->setFacebookUsername($data['facebook_username']);
        }
        if (isset($data['twitter_username'])) {
            $user->setTwitterUsername($data['twitter_username']);
        }
        if (isset($data['certifications_courses'])) {
            $user->setCertificationsCourses($data['certifications_courses']);
        }
        if (isset($data['report_to'])) {
            $user->setReportTo($data['report_to']);
        }

        $this->_em->persist($user);
        $this->_em->flush();

        return $user;
    }

    public function delete(User $employee)
    {
        $this->_em->remove($employee);
        $this->_em->flush();
    }

    public function getCronUserData($today)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('u.id', 'u.joining_date as start_date', 'u.probation_end_date as end_date')
            ->from('App\Entities\User', 'u')
            //            ->where('u.id IN (:id)')
            //            ->setParameters(['id'=>[42]])
            ->where('u.probation_end_date=:today')
            ->setParameter('today', $today->format('Y-m-d'))
            ->getQuery();
        return $query->getArrayResult();
    }

    //    public function getAllUsersAttendance($start_date, $end_date)
    //    {
    //        $query = $this->_em->createQueryBuilder();
    //        $query->select('u', 'c', 'b', 'cm')
    //            ->from('App\Entities\User', 'u')
    //            ->leftJoin('u.user_check_in', 'c', Query\Expr\Join::WITH, $query->expr()->andX(
    //                $query->expr()->gte('c.check_in_time', ':start_date'),
    //                $query->expr()->lte('c.check_in_time', ':end_date')
    //            ))
    //            ->leftJoin('c.breaks', 'b')
    //            ->leftJoin('c.comments', 'cm')
    //            ->where('u.user_exit_status=:status')
    //            ->setParameters([
    //                'start_date' => $start_date,
    //                'end_date' => $end_date,
    //                'status' => 1
    //            ]);
    //        $res = $query->getQuery();
    //        return $res->getArrayResult();
    //    }


    public function getGeneralDataAttn($start_date, $end_date)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('u.id as user_id', 'c.id as check_in_id', 'c.is_late')
            ->from('App\Entities\User', 'u')
            ->leftJoin('u.user_check_in', 'c', Query\Expr\Join::WITH, $query->expr()->andX(
                $query->expr()->gte('c.check_in_time', ':start_date'),
                $query->expr()->lte('c.check_in_time', ':end_date')
            ))
            ->where('u.user_exit_status=:status')
            ->setParameters([
                'start_date' => $start_date,
                'end_date' => $end_date,
                'status' => 1
            ]);
        $res = $query->getQuery();
        return $res->getArrayResult();
    }

    public function getAllUsersAttendance($col, $order, $search, $start, $length, $start_date, $end_date)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('u', 'c', 'b', 'cm')
            ->from('App\Entities\User', 'u')
            ->leftJoin('u.user_check_in', 'c', Query\Expr\Join::WITH, $query->expr()->andX(
                $query->expr()->gte('c.check_in_time', ':start_date'),
                $query->expr()->lte('c.check_in_time', ':end_date')
            ))
            ->leftJoin('c.breaks', 'b')
            ->leftJoin('c.comments', 'cm')
            ->where('u.user_exit_status=:status')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter')
                )
            )
            ->orderBy(($col == 'firstname' ? 'u.' : 'c.') . $col, strtoupper($order))
            ->setParameters([
                'filter' => '%' . $search . '%',
                'start_date' => $start_date,
                'end_date' => $end_date,
                'status' => 1
            ]);

        $res = $query->getQuery();
        $data = $res->getResult(Query::HYDRATE_ARRAY);
        return array_slice($data, $start, $length);
    }

    public function countAttnEmployee()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('COUNT(u.id) as active_count')
            ->from('App\Entities\User', 'u')
            ->where('u.user_exit_status=:status')
            ->setParameters(['status' => 1]);
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function countFilteredRowsAttn($search)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('COUNT(u.id) as active_count')
            ->from('App\Entities\User', 'u')
            ->where('u.user_exit_status=:status')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->like("concat(u.firstname,' ',u.lastname)", ':filter'),
                    $query->expr()->like("concat(u.lastname,' ',u.firstname)", ':filter')
                )
            )
            ->setParameters(['status' => 1, 'filter' => '%' . $search . '%']);
        $qb = $query->getQuery();

        return $qb->getArrayResult();
    }

    public function getAttendanceDashboard($start_date, $end_date)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('u.id,u.firstname,u.contact_no,u.lastname,u.profile_image,des.name as designation,lo.name as batch,c.id as check_in_id')
            ->from('App\Entities\User', 'u')
            ->leftJoin('u.user_check_in', 'c', Query\Expr\Join::WITH, $query->expr()->andX(
                $query->expr()->gte('c.check_in_time', ':start_date'),
                $query->expr()->lte('c.check_in_time', ':end_date')
            ))
            ->leftjoin('u.designation', 'des')
            ->leftjoin('u.location', 'lo')
            ->where('u.user_exit_status=:status')
            ->setParameters([
                'start_date' => $start_date,
                'end_date' => $end_date,
                'status' => 1
            ]);
        $res = $query->getQuery();
        return $res->getArrayResult();
    }

    public function getUpcomingBdays()
    {
        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $emConfig->addCustomDatetimeFunction('DAY', 'DoctrineExtensions\Query\Mysql\Day');

        $now = Carbon::now();
        $query = $this->_em->createQueryBuilder()
            ->select('u.id,u.firstname,u.lastname,u.profile_image,u.date_of_birth')
            ->from('App\Entities\User', 'u')
            ->where('MONTH(u.date_of_birth)=:month')
            ->andWhere('u.user_exit_status=:status')
            ->orderBy('DAY(u.date_of_birth)', 'ASC')
            ->setParameter('month', $now->month)
            ->setParameter('status', 1)
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getActiveEmployees()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('COUNT(u.id) as active_count')
            ->from('App\Entities\User', 'u')
            ->where('u.user_exit_status=:status')
            ->setParameter('status', 1)
            ->getQuery();

        return $query->getArrayResult();
    }


    public function getOnProbationEmployees()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('u.id,u.firstname,u.lastname,u.probation_end_date, u.profile_image')
            ->from('App\Entities\User', 'u')
            ->where('u.probation_end_date>:today')
            ->setParameter('today', Carbon::today())
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getCurrentMonthIncrementEmp()
    {
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');

        $query = $this->_em->createQueryBuilder()
            ->select('u.id, u.firstname, u.lastname, u.joining_date,u.increment_date, u.profile_image')
            ->from('App\Entities\User', 'u')
            ->where('u.user_exit_status=:status')
//            ->andWhere('MONTH(u.probation_end_date)=:currentMonth')
//            ->andWhere('YEAR(u.probation_end_date)<:year')
            ->andWhere('MONTH(u.increment_date)=:currentMonth')
            ->andWhere('YEAR(u.increment_date)=:year')
            ->setParameters(['status' => 1, 'currentMonth' => Carbon::now()->month, 'year' => Carbon::now()->year])
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getCurrentMonthIncrementExpEmp()
    {
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');

        $query = $this->_em->createQueryBuilder()
            ->select('u.id, u.firstname, u.lastname, u.joining_date, u.profile_image')
            ->from('App\Entities\User', 'u')
            ->where('u.user_exit_status=:status')
            ->andWhere('MONTH(u.joining_date)=:currentMonth')
            ->andWhere('YEAR(u.joining_date)<:year')
            ->setParameters(['status' => 1, 'currentMonth' => Carbon::now()->month, 'year' => Carbon::now()->year])
            ->getQuery();

        return $query->getArrayResult();
    }

    function getAllUsersExceptHR()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('u.id', 'u.firstname', 'u.lastname', 'u.profile_image')
            ->from('App\Entities\User', 'u')
            ->leftJoin('u.roles', 'r')
            ->where('u.user_exit_status=:status')
            ->andWhere('LOWER(r.name) NOT IN (:roleNames)')
            ->andWhere('r.id!=1')
            ->setParameters(['status' => '1', 'roleNames' => ['hr', 'admin']])
            ->orderBy('u.created_at', 'DESC')
            ->getQuery();
        return $query->getArrayResult();
    }
    public function getAllJBaSelfBA($id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('u.id', 'u.firstname', 'u.lastname', 'u.profile_image')
            ->from('App\Entities\User', 'u')
            ->leftJoin('u.roles', 'r')
            ->where('u.user_exit_status=:status')
            ->andWhere('r.name=:name')
            ->andWhere('u.id!=:id')
            ->setParameters(['status' => 1, 'name' => 'BA','id' =>$id ])
            ->orderBy('u.created_at', 'DESC')
            ->getQuery();
        return $query->getArrayResult();
    }

    public function getTodayStaffingData()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('u', 'dep', 'batch', 'c', 'b', 'cm')
            ->from('App\Entities\User', 'u')
            ->leftJoin('u.user_check_in', 'c', Query\Expr\Join::WITH, $query->expr()->andX(
                $query->expr()->gte('c.check_in_time', ':start_date')
            ))
            ->leftJoin('u.department', 'dep')
            ->leftJoin('u.location', 'batch')
            ->leftJoin('c.breaks', 'b')
            ->leftJoin('c.comments', 'cm')
            ->where('u.user_exit_status=:status')
            ->setParameters([
                'start_date' => Carbon::today(),
                'status' => 1
            ]);

        $res = $query->getQuery();
        $data = $res->getResult(Query::HYDRATE_ARRAY);
        return $data;
    }
	
	public function getStaffingDataByDate($date){
	$query = $this->_em->createQueryBuilder();
        $query->select('u', 'dep', 'batch', 'c', 'b', 'cm')
            ->from('App\Entities\User', 'u')
            ->leftJoin('u.user_check_in', 'c', Query\Expr\Join::WITH, $query->expr()->andX(
                $query->expr()->gte('c.check_in_time', ':start_date')
            ))
            ->leftJoin('u.department', 'dep')
            ->leftJoin('u.location', 'batch')
            ->leftJoin('c.breaks', 'b')
            ->leftJoin('c.comments', 'cm')
            ->where('u.user_exit_status=:status')
            ->setParameters([
                'start_date' => $date,
                'status' => 1
            ]);

        $res = $query->getQuery();
        $data = $res->getResult(Query::HYDRATE_ARRAY);
        return $data;
	}

    public function getUserLetterData($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select("u.id as EMPLOYEE_ID,
                    concat(u.firstname,' ',u.lastname) as EMPLOYEE_NAME,
                    u.per_address as EMPLOYEE_ADDRESS,
                    u.joining_date as EMPLOYEE_JOINING_DATE,
                    u.probation_end_date as EMPLOYEE_PROBATION_END_DATE,
                    u.exit_date as EMPLOYEE_EXIT_DATE,
                    u.date_of_birth as EMPLOYEE_DOB,
                    dep.name as EMPLOYEE_DEPARTMENT,
                    des.name as EMPLOYEE_DESIGNATION,
                    loc.name as EMPLOYEE_LOCATION,
                    loc.office_start_time as OFFICE_START_TIME,
                    loc.office_end_time as OFFICE_END_TIME
                    ")
            ->from('App\Entities\User', 'u')
            ->leftJoin('u.department', 'dep')
            ->leftJoin('u.designation', 'des')
            ->leftJoin('u.location', 'loc')
            ->where('u.id=:id')
            ->setParameter('id', $emp_id)
            ->getQuery();

        return $query->getArrayResult();
    }

    public function getSignatoryData($emp_id)
    {
        $query = $this->_em->createQueryBuilder()
            ->select("concat(u.firstname,' ',u.lastname) as SIGNATORY,
                    dep.name as SIGNATORY_DEPARTMENT,
                    des.name as SIGNATORY_DESIGNATION
                    ")
            ->from('App\Entities\User', 'u')
            ->leftJoin('u.department', 'dep')
            ->leftJoin('u.designation', 'des')
            ->where('u.id=:id')
            ->setParameter('id', $emp_id)
            ->getQuery();

        return $query->getArrayResult();
    }
    public function getAttendanceReportDataOfAllUser($startDate,$endDate)
    {
        $params = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'active_status' => 1
        ];

        $query = $this->_em->createQueryBuilder()
            ->select('u,l,ch,lo,lt')
            ->from('App\Entities\User', 'u')
            ->leftJoin('u.approved_leave', 'l',"WITH", "l.leave_date BETWEEN :startDate AND :endDate")
            ->leftJoin('l.leave_type', 'lt')
            ->leftJoin('u.user_check_in', 'ch',"WITH", "ch.check_in_time BETWEEN :startDate AND :endDate")
            ->leftJoin('u.location', 'lo')
            ->where('u.user_exit_status=:active_status')
            ->setParameters($params)
            ->getQuery();

        return $query->getArrayResult();
    }
}

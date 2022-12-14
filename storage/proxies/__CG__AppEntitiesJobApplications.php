<?php

namespace DoctrineProxies\__CG__\App\Entities;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class JobApplications extends \App\Entities\JobApplications implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = [];



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return ['__isInitialized__', 'id', 'job_id', 'applicant_name', 'location', 'contact_email', 'phone_number1', 'phone_number2', 'source', 'current_company', 'current_ctc', 'expected_ctc', 'degree', 'stage', 'reject_reason', 'resume', 'assoc_emp_id', 'job_int', 'created_at', 'updated_at', 'deletedAt'];
        }

        return ['__isInitialized__', 'id', 'job_id', 'applicant_name', 'location', 'contact_email', 'phone_number1', 'phone_number2', 'source', 'current_company', 'current_ctc', 'expected_ctc', 'degree', 'stage', 'reject_reason', 'resume', 'assoc_emp_id', 'job_int', 'created_at', 'updated_at', 'deletedAt'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (JobApplications $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', []);
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', []);
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function getAssocEmpId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAssocEmpId', []);

        return parent::getAssocEmpId();
    }

    /**
     * {@inheritDoc}
     */
    public function setAssocEmpId($assoc_emp_id): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAssocEmpId', [$assoc_emp_id]);

        parent::setAssocEmpId($assoc_emp_id);
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', []);

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function setJobId($job_id): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setJobId', [$job_id]);

        parent::setJobId($job_id);
    }

    /**
     * {@inheritDoc}
     */
    public function getApplicantName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getApplicantName', []);

        return parent::getApplicantName();
    }

    /**
     * {@inheritDoc}
     */
    public function setApplicantName($applicant_name): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setApplicantName', [$applicant_name]);

        parent::setApplicantName($applicant_name);
    }

    /**
     * {@inheritDoc}
     */
    public function getLocation()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLocation', []);

        return parent::getLocation();
    }

    /**
     * {@inheritDoc}
     */
    public function setLocation($location): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLocation', [$location]);

        parent::setLocation($location);
    }

    /**
     * {@inheritDoc}
     */
    public function getContactEmail()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getContactEmail', []);

        return parent::getContactEmail();
    }

    /**
     * {@inheritDoc}
     */
    public function setContactEmail($contact_email): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setContactEmail', [$contact_email]);

        parent::setContactEmail($contact_email);
    }

    /**
     * {@inheritDoc}
     */
    public function getPhoneNumber1()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPhoneNumber1', []);

        return parent::getPhoneNumber1();
    }

    /**
     * {@inheritDoc}
     */
    public function setPhoneNumber1($phone_number1): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPhoneNumber1', [$phone_number1]);

        parent::setPhoneNumber1($phone_number1);
    }

    /**
     * {@inheritDoc}
     */
    public function getPhoneNumber2()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPhoneNumber2', []);

        return parent::getPhoneNumber2();
    }

    /**
     * {@inheritDoc}
     */
    public function setPhoneNumber2($phone_number2): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPhoneNumber2', [$phone_number2]);

        parent::setPhoneNumber2($phone_number2);
    }

    /**
     * {@inheritDoc}
     */
    public function getSource()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSource', []);

        return parent::getSource();
    }

    /**
     * {@inheritDoc}
     */
    public function setSource($source): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSource', [$source]);

        parent::setSource($source);
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentCompany()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCurrentCompany', []);

        return parent::getCurrentCompany();
    }

    /**
     * {@inheritDoc}
     */
    public function setCurrentCompany($current_company): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCurrentCompany', [$current_company]);

        parent::setCurrentCompany($current_company);
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentCtc()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCurrentCtc', []);

        return parent::getCurrentCtc();
    }

    /**
     * {@inheritDoc}
     */
    public function setCurrentCtc($current_ctc): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCurrentCtc', [$current_ctc]);

        parent::setCurrentCtc($current_ctc);
    }

    /**
     * {@inheritDoc}
     */
    public function getExpectedCtc()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getExpectedCtc', []);

        return parent::getExpectedCtc();
    }

    /**
     * {@inheritDoc}
     */
    public function setExpectedCtc($expected_ctc): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setExpectedCtc', [$expected_ctc]);

        parent::setExpectedCtc($expected_ctc);
    }

    /**
     * {@inheritDoc}
     */
    public function getDegree()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDegree', []);

        return parent::getDegree();
    }

    /**
     * {@inheritDoc}
     */
    public function setDegree($degree): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDegree', [$degree]);

        parent::setDegree($degree);
    }

    /**
     * {@inheritDoc}
     */
    public function getStage()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStage', []);

        return parent::getStage();
    }

    /**
     * {@inheritDoc}
     */
    public function setStage($stage): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setStage', [$stage]);

        parent::setStage($stage);
    }

    /**
     * {@inheritDoc}
     */
    public function getResume()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getResume', []);

        return parent::getResume();
    }

    /**
     * {@inheritDoc}
     */
    public function setResume($resume): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setResume', [$resume]);

        parent::setResume($resume);
    }

    /**
     * {@inheritDoc}
     */
    public function getRejectReason()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRejectReason', []);

        return parent::getRejectReason();
    }

    /**
     * {@inheritDoc}
     */
    public function setRejectReason($reject_reason): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setRejectReason', [$reject_reason]);

        parent::setRejectReason($reject_reason);
    }

}

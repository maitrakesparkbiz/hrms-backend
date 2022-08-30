<?php

namespace DoctrineProxies\__CG__\App\Entities;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Leave_type extends \App\Entities\Leave_type implements \Doctrine\ORM\Proxy\Proxy
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
            return ['__isInitialized__', 'id', 'leavetype', 'gender', 'status', 'count_type', 'count', 'max_leave_month', 'max_consecutive_leave_month', 'probation', 'half_day', 'intervening_holiday', 'over_utilization', 'unused_leave', 'created_at', 'updated_at', 'deletedAt'];
        }

        return ['__isInitialized__', 'id', 'leavetype', 'gender', 'status', 'count_type', 'count', 'max_leave_month', 'max_consecutive_leave_month', 'probation', 'half_day', 'intervening_holiday', 'over_utilization', 'unused_leave', 'created_at', 'updated_at', 'deletedAt'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Leave_type $proxy) {
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
    public function getLeavetype()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLeavetype', []);

        return parent::getLeavetype();
    }

    /**
     * {@inheritDoc}
     */
    public function getGender()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getGender', []);

        return parent::getGender();
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStatus', []);

        return parent::getStatus();
    }

    /**
     * {@inheritDoc}
     */
    public function getCount_type()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCount_type', []);

        return parent::getCount_type();
    }

    /**
     * {@inheritDoc}
     */
    public function getCount()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCount', []);

        return parent::getCount();
    }

    /**
     * {@inheritDoc}
     */
    public function getMax_leave_month()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMax_leave_month', []);

        return parent::getMax_leave_month();
    }

    /**
     * {@inheritDoc}
     */
    public function getMax_consecutive_leave_month()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMax_consecutive_leave_month', []);

        return parent::getMax_consecutive_leave_month();
    }

    /**
     * {@inheritDoc}
     */
    public function getProbation()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getProbation', []);

        return parent::getProbation();
    }

    /**
     * {@inheritDoc}
     */
    public function getHalf_day()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getHalf_day', []);

        return parent::getHalf_day();
    }

    /**
     * {@inheritDoc}
     */
    public function getIntervening_holiday()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIntervening_holiday', []);

        return parent::getIntervening_holiday();
    }

    /**
     * {@inheritDoc}
     */
    public function getOver_utilization()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOver_utilization', []);

        return parent::getOver_utilization();
    }

    /**
     * {@inheritDoc}
     */
    public function getUnused_leave()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUnused_leave', []);

        return parent::getUnused_leave();
    }

    /**
     * {@inheritDoc}
     */
    public function setLeavetype($leavetype)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLeavetype', [$leavetype]);

        return parent::setLeavetype($leavetype);
    }

    /**
     * {@inheritDoc}
     */
    public function setGender($gender)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setGender', [$gender]);

        return parent::setGender($gender);
    }

    /**
     * {@inheritDoc}
     */
    public function setStatus($status)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setStatus', [$status]);

        return parent::setStatus($status);
    }

    /**
     * {@inheritDoc}
     */
    public function setCount_type($count_type)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCount_type', [$count_type]);

        return parent::setCount_type($count_type);
    }

    /**
     * {@inheritDoc}
     */
    public function setCount($count)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCount', [$count]);

        return parent::setCount($count);
    }

    /**
     * {@inheritDoc}
     */
    public function setMax_leave_month($max_leave_month)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setMax_leave_month', [$max_leave_month]);

        return parent::setMax_leave_month($max_leave_month);
    }

    /**
     * {@inheritDoc}
     */
    public function setMax_consecutive_leave_month($max_consecutive_leave_month)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setMax_consecutive_leave_month', [$max_consecutive_leave_month]);

        return parent::setMax_consecutive_leave_month($max_consecutive_leave_month);
    }

    /**
     * {@inheritDoc}
     */
    public function setProbation($probation)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setProbation', [$probation]);

        return parent::setProbation($probation);
    }

    /**
     * {@inheritDoc}
     */
    public function setHalf_day($half_day)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setHalf_day', [$half_day]);

        return parent::setHalf_day($half_day);
    }

    /**
     * {@inheritDoc}
     */
    public function setIntervening_holiday($intervening_holiday)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIntervening_holiday', [$intervening_holiday]);

        return parent::setIntervening_holiday($intervening_holiday);
    }

    /**
     * {@inheritDoc}
     */
    public function setOver_utilization($over_utilization)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOver_utilization', [$over_utilization]);

        return parent::setOver_utilization($over_utilization);
    }

    /**
     * {@inheritDoc}
     */
    public function setUnused_leave($unused_leave)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUnused_leave', [$unused_leave]);

        return parent::setUnused_leave($unused_leave);
    }

}

<?php


namespace Itbizon\Tourism\TravelPoint;


class Country
{
    protected $id;
    protected $name;
    protected $departmentId;
    protected $region;
    protected $cities;

    /**
     * Country constructor.
     * @param int $id
     * @param string $name
     * @param int $departmentId
     */
    public function __construct($id, $name, $departmentId)
    {
        $this->id           = intval($id);
        $this->name         = strval($name);
        $this->departmentId = intval($departmentId);
        $this->region       = null;
        $this->cities       = [];
    }

    /**
     * @param City $city
     */
    public function addCity(City $city)
    {
        $city->setCountry($this);
        $this->cities[$city->getId()] = $city;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getDepartmentId()
    {
        return $this->departmentId;
    }

    /**
     * @return Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @return array
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * @param Region $region
     */
    public function setRegion(Region $region)
    {
        $this->region = $region;
    }
}
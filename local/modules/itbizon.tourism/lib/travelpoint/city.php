<?php


namespace Itbizon\Tourism\TravelPoint;


class City
{
    protected $id;
    protected $name;
    protected $departmentId;
    protected $country;

    /**
     * City constructor.
     * @param int $id
     * @param string $name
     * @param int $departmentId
     */
    public function __construct($id, $name, $departmentId)
    {
        $this->id           = intval($id);
        $this->name         = strval($name);
        $this->departmentId = intval($departmentId);
        $this->country      = null;
        $this->cities       = [];
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
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param Country $country
     */
    public function setCountry(Country $country)
    {
        $this->country = $country;
    }
}
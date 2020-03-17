<?php


namespace Itbizon\Tourism\TravelPoint;


class Region
{
    protected $id;
    protected $name;
    protected $departmentId;
    protected $countries;

    /**
     * Region constructor.
     * @param $id
     * @param $name
     * @param $departmentId
     */
    public function __construct($id, $name, $departmentId)
    {
        $this->id           = intval($id);
        $this->name         = strval($name);
        $this->departmentId = intval($departmentId);
        $this->countries    = [];
    }

    /**
     * @param Country $country
     */
    public function addCountry(Country $country)
    {
        $country->setRegion($this);
        $this->countries[$country->getId()] = $country;
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
     * @return array
     */
    public function getCountries()
    {
        return $this->countries;
    }

    /**
     * @return array
     */
    public function getCities()
    {
        $items = [];
        /** @var Country $country */
        foreach($this->countries as $country)
        {
            $items = array_merge($items, $country->getCities());
        }
        return $items;
    }
}
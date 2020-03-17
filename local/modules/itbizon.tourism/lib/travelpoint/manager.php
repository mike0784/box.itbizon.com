<?php


namespace Itbizon\Tourism\TravelPoint;


class Manager
{
    private static $object = null;
    protected $regions;
    protected $countries;
    protected $cities;

    /**
     * Manager constructor.
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function __construct()
    {
        //Load regions
        $this->regions = [];
        $result = Model\RegionTable::getList([
            'order' => ['NAME' => 'ASC']
        ]);
        while($row = $result->fetch())
        {
            $regionId = $row['ID'];
            $this->regions[$regionId] = new Region($row['ID'], $row['NAME'], $row['DEPARTMENT_ID']);
        }

        //Load countries
        $this->countries = [];
        $result = Model\CountryTable::getList([
            'order' => ['NAME' => 'ASC']
        ]);
        while($row = $result->fetch())
        {
            $regionId  = $row['REGION_ID'];
            $countryId = $row['ID'];
            $country = new Country($countryId, $row['NAME'], $row['DEPARTMENT_ID']);
            if(isset($this->regions[$regionId]))
                $this->regions[$regionId]->addCountry($country);
            $this->countries[$countryId] = $country;
        }

        //Load cities
        $this->cities = [];
        $result = Model\CityTable::getList([
            'order' => ['NAME' => 'ASC']
        ]);
        while($row = $result->fetch())
        {
            $countryId = $row['COUNTRY_ID'];
            $cityId    = $row['ID'];
            $city = new City($row['ID'], $row['NAME'], $row['DEPARTMENT_ID']);
            if(isset($this->countries[$countryId]))
                $this->countries[$countryId]->addCity($city);
            $this->cities[$cityId] = $city;
        }
    }

    /**
     * @return Manager|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getInstance()
    {
        if(!self::$object)
            self::$object = new self();
        return self::$object;
    }

    /**
     * @return array
     */
    public function getRegions()
    {
        return $this->regions;
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
        return $this->cities;
    }
}
<?php

namespace Itbizon\Kalinin;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Itbizon\Kalinin\Exceptions\ShipExceptions\ShipCreateException;
use Itbizon\Kalinin\Exceptions\ShipExceptions\StationCreateException;
use Itbizon\Kalinin\Logger\Logger;
use Itbizon\Kalinin\Model\ShipTable;
use Itbizon\Kalinin\Model\StationTable;
use Itbizon\Kalinin\Ship;
use Itbizon\Kalinin\Station;

class Manager
{
    /**
     * @param array $params
     * @return \Station
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws StationCreateException
     * @throws SystemException
     */
    public static function createStation(array $params)
    {
        global $USER;

        if (!isset($params['NAME']) || empty($params['NAME']))
            throw new StationCreateException("Отсутствует параметр имени 'NAME'");

        if(isset($USER) && (!isset($params['CREATOR_ID']) || is_null($params['CREATOR_ID'])))
            $params['CREATOR_ID'] = $USER->GetID();

        $station = StationTable::getByPrimary(StationTable::add($params)->getId())->fetchObject();

        return $station;
    }


    /**
     * @param int $station_id
     * @param array $params
     * @return \Bitrix\Main\ORM\Objectify\EntityObject
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public static function updateStation(int $station_id, array $params)
    {
        try {
            $station = StationTable::update($station_id, $params)->getObject();
        } catch (\Exception $e) {
            Logger::LogError($e->getMessage());
        }

        $station->save();

        return $station;
    }


    /**
     * @param array|string[] $select
     * @return array
     */
    public static function getStationsList(array $select=array('*'))
    {
        try {
            return StationTable::getList(['select' => $select])->fetchAll();
        } catch (ObjectPropertyException $e) {
            Logger::LogError("Не удалось получть список станций: " . $e->getMessage());
        } catch (ArgumentException $e) {
            Logger::LogError("Не удалось получть список станций: " . $e->getMessage());
        } catch (SystemException $e) {
            Logger::LogError("Не удалось получть список станций: " . $e->getMessage());
        }
    }

    /**
     * @param array $params
     * @return \Ship|null
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function createShip(array $params)
    {
        global $USER;

        if (!isset($params['NAME']) || empty($params['NAME']))
            throw new ShipCreateException("Отсутствует параметр имени 'NAME'");

        if (!isset($params['STATION_ID']) || empty($params['STATION_ID']))
            throw new ShipCreateException("Отсутствует параметр id станции 'STATION_ID'");

        if (!isset($params['VALUE']) || empty($params['VALUE']))
            throw new ShipCreateException("Отсутствует параметр стоимости корабля 'VALUE'");

        if (!isset($params['MATERIALS']) || empty($params['MATERIALS']))
            throw new ShipCreateException("Отсутствует параметр материалы корабля 'MATERIALS'");

        if(isset($USER) && (!isset($params['CREATOR_ID']) || is_null($params['CREATOR_ID'])))
            $params['CREATOR_ID'] = $USER->GetID();

        $ship = ShipTable::createObject();

        $ship->set('NAME', $params['NAME']);
        $ship->set('STATION_ID', $params['STATION_ID']);
        $ship->set('CREATOR_ID', $params['CREATOR_ID']);
        $ship->set('VALUE', $params['VALUE']);
        $ship->set('MATERIALS', $params['MATERIALS']);
        $ship->set('IS_RELEASED', (isset($params['IS_RELEASED']) ? $params['IS_RELEASED'] : 'N'));
        $ship->set('COMMENT', (isset($params['COMMENT']) ? $params['COMMENT'] : ""));

//        $ship = ShipTable::getByPrimary($shipAddResult->getId())->fetchObject();

        $station = StationTable::getByPrimary($params['STATION_ID'])->fetchObject();
        $station->setAmount($station->getAmount() + intval($params['VALUE']));
        $station->setCount($station->getCount() + 1);
        $station->save();

        $ship->save();

        return $ship;
    }

    /**
     * @param int $ship_id
     * @param array $params
     * @return \Bitrix\Main\ORM\Objectify\EntityObject|\Ship
     * @throws \Exception
     */
    public static function updateShip(int $ship_id, array $params)
    {
        $ship = ShipTable::getByPrimary($ship_id)->fetchObject();

        $station = StationTable::getByPrimary($params['STATION_ID'])->fetchObject();



        if (isset($params['NAME']) && !empty($params['NAME']))
            $ship->set('NAME', $params['NAME']);

        if (isset($params['VALUE']) && !empty($params['VALUE']))
            $station->setAmount($station->getAmount() - intval($ship->getValue()));
            $station->setAmount($station->getAmount() + intval($params['VALUE']));
            $ship->set('VALUE', $params['VALUE']);

        if (isset($params['MATERIALS']) && !empty($params['MATERIALS']))


        if (isset($params['MATERIALS']) && !empty($params['MATERIALS']))
            throw new ShipCreateException("Отсутствует параметр материалы корабля 'MATERIALS'");

        $station->save();
        $ship->save();

        return $ship;
    }


    /**
     * Delete ship.
     *
     * @param $id
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function recycleShip($id)
    {
        $ship = ShipTable::getByPrimary($id)->fetchObject();

        $station = StationTable::getByPrimary($ship->getStationId())->fetchObject();

        $station->setAmount($station->getAmount() - intval($ship->getValue()));
        $station->setCount($station->getCount() - 1);

        $ship->delete();
    }

    public static function recycleStation($id)
    {
        $ownedShips = ShipTable::getList([
            'filter' => ['=STATION_ID' => $id]
        ]);

        while ($ship = $ownedShips->fetchObject())
        {
            $ship->delete();
        }

        StationTable::delete($id);
    }

    public static function getStationAndShips($station_id)
    {
        $data = [];

        $data['station'] = StationTable::getById($station_id)->fetch();
        $data['ships'] = ShipTable::getList([
            'select' => ['*'],
            'filter' => ['=STATION_ID' => $station_id]
        ])->fetchAll();

        return $data;
    }
}

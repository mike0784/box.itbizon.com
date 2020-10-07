<?php

namespace Itbizon\Kalinin\Lib\Model;

use Itbizon\Kalinin\Lib\Log\Logger;

class Manager
{
    /**
     * @param string $name
     * @param int|null $creator_id
     * @param int|null $amount
     * @param int|null $count
     * @param string|null $comment
     * @return EO_Station|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public function createStation(string $name, int $creator_id=null, int $amount=null, int $count=null, string $comment=null)
    {
        global $USER;

        if(isset($USER) && is_null($creator_id))
            $creator_id = $USER->GetID();

        $station = StationTable::createObject();

        $station->setName($name);

        if ($amount)
            $station->setAmount($amount);

        if ($count)
            $station->setCount($count);

        if ($comment)
            $station->setComment($comment);

        $station->setCreatorId($creator_id);

        $station->save();

        return $station;
    }

    /**
     * @param string $name
     * @param string $materials
     * @param int $value
     * @param int $station_id
     * @param int|null $creator_id
     * @param bool $is_released
     * @param string|null $comment
     * @return EO_Ship|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public function createShip(string $name, string $materials, int $value,
                               int $station_id, int $creator_id=null,
                               bool $is_released=false, string $comment=null)
    {
        global $USER;

        if(isset($USER) && is_null($creator_id))
            $creator_id = $USER->GetID();

        $ship = ShipTable::createObject();
        $station = StationTable::getByPrimary($station_id)->fetchObject();


        $ship->setName($name);
        $ship->setMaterials($materials);
        $ship->setValue($value);
        $ship->setStationId($station_id);
        $ship->setCreatorId($creator_id);
        $ship->setIsReleased($is_released);

        if ($comment)
            $ship->setComment($comment);

        $station->setAmount($station->getAmount()+$value);
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
    public function recycleShip($id)
    {
        ShipTable::getByPrimary($id)
            ->fetchObject()
            ->delete();
    }

    public function recycleStation($id)
    {
        StationTable::getByPrimary($id)
            ->fetchObject()
            ->delete();

        $ownedShips = ShipTable::getList([
            'filter' => ['=STATION_ID' => $id]
        ]);

        Logger::LogInfo($ownedShips);

        while ($ship = $ownedShips->fetchObject())
        {
            $ship->delete();
        }
    }

    public function getStationAndShips($station_id)
    {
        $data = [];

        Logger::LogInfo("---------| Getting Station and Ships |---------");

        $data['station'] = StationTable::getByPrimary($station_id)->fetchObject();

        $ownedShips = ShipTable::getList([
            'filter' => ['=STATION_ID' => $station_id]
        ]);

        Logger::LogInfo($ownedShips);

        $data['ships'] = [];

        while ($ship = $ownedShips->fetchObject())
        {
            array_push($data['ships'], $ship);
        }

        Logger::LogInfo("======| Getting Done |======");

        return $data;
    }
}

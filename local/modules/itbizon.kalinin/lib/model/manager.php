<?php

namespace Itbizon\Kalinin\Model;

use Itbizon\Kalinin\Log\Logger;
use Itbizon\Kalinin\Model\ShipTable;
use Itbizon\Kalinin\Model\StationTable;
use Itbizon\Kalinin\Ship;
use Itbizon\Kalinin\Station;

class Manager
{
    /**
     * @param string $name
     * @param int|null $creator_id
     * @param int|null $amount
     * @param int|null $count
     * @param string|null $comment
     * @return \Itbizon\Kalinin\Station|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public static function createStation(string $name, int $creator_id=null, int $amount=null, int $count=null, string $comment=null)
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
     * @return \Itbizon\Kalinin\Ship|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function createShip(string $name, string $materials, int $value,
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
        $station->setCount($station->getCount()+1);
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
        ShipTable::getByPrimary($id)
            ->fetchObject()
            ->delete();
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

        StationTable::getByPrimary($id)
            ->fetchObject()
            ->delete();
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

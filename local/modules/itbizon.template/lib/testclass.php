<?php


namespace Itbizon\Template;


class TestClass
{
    /**
     *
     */
    public static function test()
    {
        echo __CLASS__;
    }

    public static function addNewRow()
    {
//        var_dump(UserTable::getByPrimary(1)->fetchObject());
        $result = SystemFines\Model\FinesTable::add([
            'TITLE' => 'new title',
            'VALUE' => 200,
            'TARGET_ID' => 1,
            'CREATOR_ID' => 2
        ]);
        if (!$result->isSuccess()) {
            var_dump($result->getErrorMessages());
            return 'error';
        } else {
            $data = SystemFines\Model\FinesTable::getList([
                'order' => ['TITLE' => 'ASC']
            ]);
            $data->fetch();
            return 'Success';
        }
    }
}
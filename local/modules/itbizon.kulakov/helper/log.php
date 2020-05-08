<?php

namespace TestModule;

use \Bitrix\Main\Diag\Debug;

class Log
{
    const path = '/local/modules/itbizon.kulakov/log/';
    static public $filename = 'log_';

    public static function write($obj)
    {
        $time = time();
        $date = date("d-F-Y", $time);

        $pathToFolder = self::path;
        $pathToFile = $pathToFolder . '/' . self::$filename . $date . '.txt';

        if(!file_exists($pathToFolder))
            mkdir($pathToFolder);

        $data = date("[G:i:s] ", $time) . print_r($obj,true);
        Debug::writeToFile($data, "", $pathToFile);

    }
}
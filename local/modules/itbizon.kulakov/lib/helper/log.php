<?php

namespace Itbizon\Kulakov\Helper;

use \Bitrix\Main\Diag\Debug;

class Log
{
    const path = '/local/modules/itbizon.kulakov/logs/';
    static public $filename = 'log_';

    public static function write($obj)
    {

        $time = time();
        $date = date("d-F-Y", $time);

        $pathToFolder = $_SERVER['DOCUMENT_ROOT'] . self::path;
        $pathToFile = $pathToFolder . self::$filename . $date . '.txt';

        if(!file_exists($pathToFolder)) {
            mkdir($pathToFolder, 0777, true);
            chmod($pathToFolder, 0777);
        }

        $data = date("[G:i:s] ", $time) . print_r($obj,true) . "\n";
        file_put_contents($pathToFile, $data, FILE_APPEND);

    }
}
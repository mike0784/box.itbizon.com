<?php

namespace Itbizon\Kalinin\Logger;

class Logger
{
    const ERROR = "Error: ";
    const INFO = "Info: ";
    const WARNING = "Warning: ";

    const LOGPATH = "/local/modules/itbizon.kalinin/logs/";

    /**
     * Logging info
     *
     * @param $obj
     */
    public static function LogInfo($obj)
    {
        $time = time();
        $date = date("d-F-Y", $time);

        $pathToFolder = $_SERVER['DOCUMENT_ROOT'] . self::LOGPATH;
        $pathToFile = $pathToFolder . $date . '_log.txt';

        if(!file_exists($pathToFolder)) {
            mkdir($pathToFolder, 0777, true);
            chmod($pathToFolder, 0777);
        }

        $data = self::INFO . date("[G:i:s] ", $time) . print_r($obj,true) . "\n";
        file_put_contents($pathToFile, $data, FILE_APPEND);
    }

    /**
     * Logging errors
     *
     * @param $obj
     */
    public static function LogError($obj)
    {
        $time = time();
        $date = date("d-F-Y", $time);

        $pathToFolder = $_SERVER['DOCUMENT_ROOT'] . self::LOGPATH;
        $pathToFile = $pathToFolder . $date . '_log.txt';

        if(!file_exists($pathToFolder)) {
            mkdir($pathToFolder, 0777, true);
            chmod($pathToFolder, 0777);
        }

        $data = self::ERROR . date("[G:i:s] ", $time) . print_r($obj,true) . "\n";
        file_put_contents($pathToFile, $data, FILE_APPEND);
    }

    /**
     * Logging warnings
     *
     * @param $obj
     */
    public static function LogWarning($obj)
    {
        $time = time();
        $date = date("d-F-Y", $time);

        $pathToFolder = $_SERVER['DOCUMENT_ROOT'] . self::LOGPATH;
        $pathToFile = $pathToFolder . $date . '_log.txt';

        if(!file_exists($pathToFolder)) {
            mkdir($pathToFolder, 0777, true);
            chmod($pathToFolder, 0777);
        }

        $data = self::WARNING . date("[G:i:s] ", $time) . print_r($obj,true) . "\n";
        file_put_contents($pathToFile, $data, FILE_APPEND);
    }
}
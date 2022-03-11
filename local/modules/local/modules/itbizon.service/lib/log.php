<?php


namespace Itbizon\Service;

use Bitrix\Main\Localization\Loc;
use DateTime;
use Exception;
use ZipArchive;

define('ITB_SERVICE_LOG_PATH', $_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.service/log');
Loc::loadMessages(__FILE__);

/**
 * Class Log
 * @package Itbizon\Service
 */
class Log
{
    const LOG_EXT = 'log';
    const LOG_PATH = ITB_SERVICE_LOG_PATH;

    const LOG_FILE = 0x1;
    const LOG_PRINT = 0x2;
    const LOG_STAT = 0x4;
    const LOG_ALL = (self::LOG_FILE | self::LOG_PRINT | self::LOG_STAT);

    const LEVEL_INFO = 0;
    const LEVEL_WARN = 1;
    const LEVEL_ERROR = 2;
    const LEVEL_OK = 3;

    const FORMAT_DATE = 'Y.m.d';
    const FORMAT_TIME = 'H:i:s';

    const LOG_LEVEL_INFO = [
        self::LEVEL_INFO => [
            'ID' => 'INFO',
        ],
        self::LEVEL_WARN => [
            'ID' => 'WARN',
        ],
        self::LEVEL_ERROR => [
            'ID' => 'ERROR',
        ],
        self::LEVEL_OK => [
            'ID' => 'OK',
        ],
    ];

    protected $filepath;
    protected $mode;
    protected static $defaultLog = null;

    /**
     * @param string $filename
     * @param int $mode
     */
    public function __construct(string $filename, int $mode = self::LOG_FILE)
    {
        $this->mode = $mode;
        $filename = date(self::FORMAT_DATE) . '_' . $filename;
        $this->filepath = self::LOG_PATH . DIRECTORY_SEPARATOR . $filename . '.' . self::LOG_EXT;
    }

    /**
     * @param mixed $message
     * @param int $level
     * @param bool $sendStatistic
     */
    public function add($message, int $level = self::LEVEL_INFO)
    {
        if (!is_string($message))
            $message = print_r($message, true);
        $message = '[' . date(self::FORMAT_TIME) . '][' . self::getLevelId($level) . '] ' . $message;
        if ($this->mode & self::LOG_FILE)
            file_put_contents($this->filepath, $message . PHP_EOL, FILE_APPEND);
        if ($this->mode & self::LOG_PRINT)
            print '<p>' . htmlspecialchars($message) . '</p>';
        if ($this->mode & self::LOG_STAT)
            Statistic::getInstance()->send(Statistic::CMD_ERROR, ['message' => $message]);
    }

    /**
     * @param $message
     * @param int $level
     */
    public static function addDef($message, int $level = self::LEVEL_INFO)
    {
        if (!self::$defaultLog)
            self::$defaultLog = new Log('default');
        self::$defaultLog->add($message, $level);
    }

    /**
     * @param int $level
     * @return string
     */
    protected static function getLevelId(int $level)
    {
        if (array_key_exists($level, self::LOG_LEVEL_INFO))
            return self::LOG_LEVEL_INFO[$level]['ID'];
        else
            return 'UNKNOWN';
    }

    /**
     * @return array|bool
     */
    public static function getList()
    {
        $list = [];
        $dirPath = self::LOG_PATH;
        if ($dirHandler = opendir($dirPath)) {
            while (($fileName = readdir($dirHandler)) !== false) {
                $filePath = $dirPath . DIRECTORY_SEPARATOR . $fileName;
                $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
                if (!is_dir($filePath) && $fileExt == self::LOG_EXT) {
                    $relPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $filePath);
                    $items = explode('_', $fileName);
                    $logDate = isset($items[0]) ? DateTime::createFromFormat(self::FORMAT_DATE, $items[0]) : false;
                    $name = $items[1];
                    if (count($items) > 2) {
                        unset($items[0]);
                        $name = implode('_', $items);
                    }
                    if ($logDate) {
                        $list[] = [
                            'NAME' => str_replace('.' . self::LOG_EXT, '', $name),
                            'FILENAME' => $fileName,
                            'PATH' => $filePath,
                            'RELPATH' => $relPath,
                            'DATE' => $logDate,
                            'SIZE' => filesize($filePath)];
                    }
                }
            }
            closedir($dirHandler);
        } else
            return false;
        usort($list, function ($a, $b) {
            if ($a['DATE'] > $b['DATE'])
                return -1;
            if ($a['DATE'] < $b['DATE'])
                return 1;
            return 0;
        });
        return $list;
    }

    /**
     * @param string $rotateBegin
     * @return bool
     */
    public static function rotate(string $rotateBegin)
    {
        $rotateBegin = (new DateTime())->setTimestamp(strtotime($rotateBegin))->modify('first day of this month')->setTime(0, 0, 0);
        $rotateEnd = (clone $rotateBegin)->modify('last day of this month')->setTime(23, 59, 59);

        $zipName = $rotateBegin->format('Y.m') . '.log.zip';
        $zipPath = self::LOG_PATH . DIRECTORY_SEPARATOR . $zipName;
        $zip = new ZipArchive();
        $result = $zip->Open($zipPath, ZipArchive::CREATE);
        if ($result !== true)
            return false;
        $removeList = [];

        $logList = self::getList();
        if (!$logList)
            return false;
        foreach ($logList as $logFile) {
            if ($logFile['DATE'] >= $rotateBegin && $logFile['DATE'] <= $rotateEnd) {
                if ($zip->addFile($logFile['PATH'], $logFile['FILENAME'])) {
                    $removeList[] = $logFile['PATH'];
                }
            }
        }
        if ($zip->close()) {
            if (count($removeList)) {
                foreach ($removeList as $file)
                    unlink($file);
            }
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public static function agent()
    {
        try {
            $date = new DateTime();
            if ($date->format('j') == 1) {
                $date->modify('-1 day');
                if (!self::rotate($date->format('d.m.Y')))
                    throw new Exception(Loc::getMessage('ITB_SERV_LOG_ROTATION_ERROR'));
            }
        } catch (Exception $e) {
            Log::addDef($e->getMessage(), Log::LEVEL_ERROR);
        }
        return '\\' . __METHOD__ . '();';
    }

    /**
     * @param Processor $processor
     * @throws \Exception
     */
    public function getDataLogFile(Processor $processor): void
    {
        $request = $_REQUEST;
        $pathToFile = $request['pathToFile'];
        if (!file_exists($pathToFile)) {
            throw new \Exception(Loc::getMessage('ITB_SERVICE.LOG.AJAX.HANDLER.ERROR.FILE.NOTFOUND'));
        }
        $file = file_get_contents($pathToFile);
        $processor->send(true, '', ['content' => nl2br(htmlspecialchars($file))]);
    }

    /**
     * @param Processor $processor
     * @throws \Exception
     */
    public function downloadLog(Processor $processor): void
    {
        $request = $_REQUEST;
        $pathToFile = $request['pathToFile'];
        if (!file_exists($pathToFile)) {
            throw new \Exception(Loc::getMessage('ITB_SERVICE.LOG.AJAX.HANDLER.ERROR.FILE.NOTFOUND'));
        }
        $fileName = trim(pathinfo($pathToFile, PATHINFO_BASENAME));
        $file = base64_encode(file_get_contents($pathToFile));
        $processor->send(true, '', ['content' => $file, 'fileName' => $fileName]);
    }
}
<?php
/**
 * Класс логгирования событий
 * Created by PhpStorm.
 * User: Komyagin Pavel
 * Date: 17.12.2018
 * Time: 20:13
 */

namespace Bizon\Main;

use DateTime;
use ZipArchive;

define('BIZON_LOG_PATH', $_SERVER['DOCUMENT_ROOT'].'/local/modules/bizon.main/log');

class Log
{
    const LOG_EXT = 'log';
    const LOG_PATH = BIZON_LOG_PATH;

    const LOG_FILE  = 0x1;
    const LOG_PRINT = 0x2;
    const LOG_ALL   = (self::LOG_FILE | self::LOG_PRINT);

    const LEVEL_INFO  = 0;
    const LEVEL_WARN  = 1;
    const LEVEL_ERROR = 2;
    const LEVEL_OK    = 3;

    const FORMAT_DATE = 'Y.m.d';
    const FORMAT_TIME = 'H:i:s';

    const LOG_LEVEL_INFO = [
        self::LEVEL_INFO => [
            'ID'    => 'INFO',
            'TITLE' => 'Информация',
        ],
        self::LEVEL_WARN => [
            'ID'    => 'WARN',
            'TITLE' => 'Предупреждение',
        ],
        self::LEVEL_ERROR => [
            'ID'    => 'ERROR',
            'TITLE' => 'Ошибка',
        ],
        self::LEVEL_OK => [
            'ID'    => 'OK',
            'TITLE' => 'Успех',
        ],
    ];

    protected $filepath;
    protected $mode;
    protected static $default_log = null;

    /**
     * Конструктор
     * @param string $filename
     * @param int $mode
     */
    public function __construct($filename, $mode = self::LOG_FILE)
    {
        $this->mode = $mode;
        $filename = date(self::FORMAT_DATE).'_'.$filename;
        $this->filepath = self::LOG_PATH.DIRECTORY_SEPARATOR.$filename.'.'.self::LOG_EXT;
    }

    /**
     * Добавляет запись в лог
     * @param mixed $message
     * @param int $level
     */
    public function Add($message, $level = self::LEVEL_INFO)
    {
        if(!is_string($message))
            $message = print_r($message, true);
        $message = '['.date(self::FORMAT_TIME).']['.self::getLevelId($level).'] '.$message;
        if($this->mode & self::LOG_FILE)
            file_put_contents($this->filepath, $message.PHP_EOL, FILE_APPEND);
        if($this->mode & self::LOG_PRINT)
            print '<p>'.htmlspecialchars($message).'</p>';
    }

    /**
     * Реализует запись в лог по умолчанию
     * @param $message
     * @param int $level
     */
    public static function AddDef($message, $level = self::LEVEL_INFO)
    {
        if(!self::$default_log)
            self::$default_log = new Log('default');
        self::$default_log->Add($message, $level);
    }

    /**
     * Возвращает текстовый идентификатор уровня записи
     * @param int $level
     * @return string
     */
    protected static function getLevelId($level)
    {
        if(array_key_exists($level, self::LOG_LEVEL_INFO))
            return self::LOG_LEVEL_INFO[$level]['ID'];
        else
            return 'UNKNOWN';
    }

    /**
     * @param $rotate_begin
     * @return bool
     * @throws \Exception
     */
    public static function Rotate($rotate_begin)
    {
        $dir_path = self::LOG_PATH;
        //Начало и конец периода ротации
        $rotate_begin = (new \DateTime())->setTimestamp(strtotime($rotate_begin))->modify('first day of this month')->setTime(0, 0, 0);
        $rotate_end   = (clone $rotate_begin)->modify('last day of this month')->setTime(23, 59, 59);

        //echo '<p>'.$rotate_begin->format('d.m.Y H:i:s').' - '.$rotate_end->format('d.m.Y H:i:s').'</p>';

        //Файл архива
        $zip_name = $rotate_begin->format('Y.m').'.log.zip';
        $zip_path = self::LOG_PATH.DIRECTORY_SEPARATOR.$zip_name;
        $zip = new \ZipArchive();
        $result = $zip->Open($zip_path, ZipArchive::CREATE);
        if($result !== true)
            return false;
        $remove_list = [];

        //Обходим папку с логами
        $log_list = self::getList();
        //echo '<pre>'.print_r($log_list, true).'</pre>';
        if(!$log_list)
            return false;
        foreach($log_list as $log_file)
        {
            //Попадает в период ротации
            if($log_file['DATE'] >= $rotate_begin && $log_file['DATE'] <= $rotate_end)
            {
                //echo '<p>Add to zip '.$log_file['PATH'].' '.$log_file['FILENAME'].'</p>';
                if($zip->addFile($log_file['PATH'], $log_file['FILENAME']))
                {
                    $remove_list[] = $log_file['PATH'];
                }
            }
        }
        //Сохраняем архив
        if($zip->close())
        {
            if(count($remove_list))
            {
                //Удаляем заархивированные логи
                foreach($remove_list as $file)
                    unlink($file);
            }
            return true;
        }
        return false;
    }

    /**
     * @return array|bool
     */
    public static function getList()
    {
        $list = [];
        $dir_path = self::LOG_PATH;
        if($dir_handler = opendir($dir_path))
        {
            while(($file_name = readdir($dir_handler)) !== false)
            {
                $file_path = $dir_path.DIRECTORY_SEPARATOR.$file_name;
                $file_ext  = pathinfo($file_name, PATHINFO_EXTENSION);
                //Файл имеет верное расширение
                if(!is_dir($file_path) && $file_ext == self::LOG_EXT)
                {
                    $rel_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $file_path);
                    $items = explode('_', $file_name);
                    $log_date = isset($items[0]) ? DateTime::createFromFormat(self::FORMAT_DATE, $items[0]) : false;
                    //В имени файла распознана дата
                    if($log_date)
                    {
                        $list[] = [
                            'NAME' => str_replace('.'.self::LOG_EXT, '', $items[1]),
                            'FILENAME' => $file_name,
                            'PATH' => $file_path,
                            'RELPATH' => $rel_path,
                            'DATE' => $log_date,
                            'SIZE' => filesize($file_path)];
                    }
                }
            }
            closedir($dir_handler);
        }
        else
            return false;
        usort($list, function($a, $b){
            if($a['DATE'] > $b['DATE'])
                return -1;
            if($a['DATE'] < $b['DATE'])
                return 1;
            return 0;
        });
        return $list;
    }

    /**
     * @return string
     */
    public static function agent()
    {
        $return = '\\'.__METHOD__.'();';
        try
        {
            $date = new \DateTime();
            $end_date = (clone $date)->modify('last day of this month');
            if($date->format('d.m.Y') == $end_date->format('d.m.Y'))
            {
                //Rotate date
                if(!self::Rotate($date->format('d.m.Y')))
                    throw new \Exception('Ошибка ротации логов');
            }
        }
        catch(\Exception $e)
        {
            Log::AddDef($e->getMessage(), Log::LEVEL_ERROR);
        }
        return $return;
    }
}

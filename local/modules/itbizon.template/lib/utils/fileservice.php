<?php


namespace Itbizon\Template\Utils;


use Bitrix\Main\Application;
use Bitrix\Main\DB\Exception;
use ZipArchive;

class FileService
{
    const PATH_TO_FILES = 'local/upload';
    const PATH_TO_DOWNLOADS = self::PATH_TO_FILES . '/' . 'downloads';
    const PATH_TO_ARCHIVES = self::PATH_TO_FILES . '/' . 'archives';

    /**
     * php delete function that deals with directories recursively
     */
    public static function deleteFiles($target): void
    {
        if (is_dir($target)) {
            $files = glob($target . '*', GLOB_MARK); //GLOB_MARK adds a slash to directories returned

            foreach ($files as $file) {
                self::deleteFiles($file);
            }

            rmdir($target);
        } elseif (is_file($target)) {
            unlink($target);
        }
    }

    /**
     * @param $file
     * @param string $filePath
     * @return string
     * @throws \Exception
     */
    public static function uploadFile($file, string $filePath): ?string
    {
        $folderPath = Application::getDocumentRoot() . '/' . $filePath;
        // Создаем папку если такой не существует
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777);
        }

        $pathParts = pathinfo($file["name"]);
        $extension = $pathParts['extension'];
        $fileName = uniqid() . '.' . $extension;
        $uploadFilePath = $folderPath . '/' . $fileName;

        if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
            return $uploadFilePath;
        }
        throw new \Exception('Фаил не был загружен');
    }

    /**
     * @param string $fileName
     * @param string $pathToFile
     * @param string $folderName
     * @throws Exception
     */
    public static function addFileToFolder(string $fileName, string $pathToFile, string $folderName)
    {
        try {
            $extension = pathinfo(parse_url($pathToFile, PHP_URL_PATH), PATHINFO_EXTENSION);
            $file = file_get_contents($pathToFile);
            $folder = Application::getDocumentRoot() . '/' . self::PATH_TO_DOWNLOADS;
            $archiveFolder = $folder . '/' . $folderName;

            if (!is_dir($folder)) {
                mkdir($folder, 0777);
            }

            if (!is_dir($archiveFolder)) {
                mkdir($archiveFolder, 0777);
            }
            file_put_contents($archiveFolder . '/' . $fileName . '_' . uniqid() . '.' . $extension, $file);

        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param string $folderName
     * @throws Exception
     */
    public static function zipFolder(string $folderName)
    {
        $archivesFolder = Application::getDocumentRoot() . '/' . self::PATH_TO_ARCHIVES;
        $downloadFolder = Application::getDocumentRoot() . '/' . self::PATH_TO_DOWNLOADS;

        $zip = new ZipArchive;
        if (!is_dir($archivesFolder)) {
            mkdir($archivesFolder, 0777);
        }
        $zipFileName = $archivesFolder . '/' . $folderName . '.zip';
        $folder = $downloadFolder . '/' . $folderName;

        if (!is_dir($folder)) {
            throw new Exception('Данные для архивации не найдены');
        }

        if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
            $files = array_diff(scandir($folder), ['.', '..']);

            foreach ($files as $file) {
                $pathToFile = $folder . '/' . $file;
                if (is_file($pathToFile)) {
                    $zip->addFile($pathToFile, 'archive/' . $file);
                }
            }
            $zip->close();

            self::deleteFiles($folder);
            return;
        }
        throw new Exception('Фаил не загрузился');
    }

}
<?php

use Bitrix\Main\Application;
use Bitrix\Main\UI\Extension;
use \Bitrix\Main\Loader;
use Itbizon\Template\Utils\ExcelService;
use Itbizon\Template\Utils\FileService;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class ParsingExcelClass extends \CBitrixComponent
{
    public function executeComponent()
    {
        $error = null;
        $path = $this->GetPath() . '/templates/.default/ajax.php';
        try {
            if (!Loader::includeModule('itbizon.template')) {
                throw new Exception('Модуль не подулючен');
            }

            Extension::load('ui.bootstrap4');

            $folders = [];
            $downloadsFolder = Application::getDocumentRoot() . '/' . FileService::PATH_TO_DOWNLOADS;

            foreach (array_diff(scandir($downloadsFolder), ['.', '..']) as $dir) {
                if (is_dir($downloadsFolder . '/' . $dir)) {
                    $folders[] = $dir;
                }
            }
            $zipFiles = [];
            $archiveFolder = Application::getDocumentRoot() . '/' . FileService::PATH_TO_ARCHIVES;

            foreach (array_diff(scandir($archiveFolder), ['.', '..']) as $file) {
                if (end(explode('.', $file)) === 'zip') {
                    $zipFiles[] = $file;
                }
            }

            $this->arResult = [
                "CELLS" => ExcelService::charsExcelCell('ABC'),
                "PATH" => $path,
                "FOLDERS" => $folders,
                "ARCHIVES" => $zipFiles,
                "PATH_DOWNLOAD_ARCHIVE" => FileService::PATH_TO_ARCHIVES
            ];

        } catch (\Exception $e) {
            $this->arResult = [
                'ERROR' => $e->getMessage(),
            ];
        }

        $this->IncludeComponentTemplate();
        return true;
    }

}

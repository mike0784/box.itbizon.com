<?php

use Bitrix\Main\UI\Extension;
use Bitrix\Main\UserTable;
use \Itbizon\Template\SystemFines\Entities\Fines;
use \Itbizon\Template\SystemFines\EntityManager;
use \Bitrix\Main\Loader;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class ParsingExcelClass extends \CBitrixComponent
{
    public function executeComponent()
    {
        $error = null;

        try {
            if (!Loader::includeModule('itbizon.template')) {
                throw new Exception('Модуль не подулючен');
            }

            Extension::load('ui.bootstrap4');

            if ($_POST) {
                $errors = [];
                if (empty($_POST['cellName'])) {
                    $errors[] = "Название ячейки имени не может быть пустым \n";
                }
                if (empty($_POST['cellLink'])) {
                    $errors[] = "Название ячейки ссылки не может быть пустым \n";
                }
                if ($_FILES['excelFile']['name'] == "") {
                    $errors[] = "Выбирите фаил \n";
                }
                if (!empty($errors)) {
                    throw new Exception(implode(" | ", $errors));
                }

                if(file_exists($_SERVER["DOCUMENT_ROOT"] . '/local/upload/archive/archive.zip')){
                    $this->deleteFiles($_SERVER["DOCUMENT_ROOT"] . '/local/upload/archive/');
                }

//            move_uploaded_file
                $fileUploadPath = $_SERVER["DOCUMENT_ROOT"] . '/local/upload';
                $cellName = $_POST['cellName'];
                $cellLink = $_POST['cellLink'];

                //TODO Надо не забывать удаялять фаил
                if (move_uploaded_file($_FILES['excelFile']['tmp_name'], $fileUploadPath . '/' . $_FILES['excelFile']['name'])) {
                    $filePath = $_SERVER["DOCUMENT_ROOT"] . '/local/upload/' . $_FILES['excelFile']['name'];

                    $xls = new \Itbizon\Template\Utils\SimpleXLSX($filePath);
                    if ($xls->success()) {
                        $countRows = count($xls->rows());
                        for ($i = 1; $i <= $countRows; $i++) {
                            $name = !empty($xls->getCell(0, $cellName . $i)) ? $xls->getCell(0, $cellName . $i) : 'default';
                            $link = $xls->getCell(0, $cellLink . $i);
                            if (filter_var($link, FILTER_VALIDATE_URL)) {
                                $fileExtension = pathinfo(parse_url($link, PHP_URL_PATH), PATHINFO_EXTENSION);
                                $fileData = file_get_contents($link);

                                if (!is_dir($fileUploadPath . '/downloads')) {
                                    mkdir($fileUploadPath . '/downloads', 0777);
                                }
                                file_put_contents($fileUploadPath . '/downloads/' . $name . '.' . $fileExtension, $fileData);
                            }
                        }

                        $zipCreated = $fileUploadPath . "/archive";
                        $filename = $zipCreated . '/archive.zip';
                        if (!is_dir($zipCreated)) {
                            mkdir($zipCreated, 0777);
                        }
                        $zip = new ZipArchive;
                        if ($zip->open($filename, ZipArchive::CREATE) === TRUE) {
                            $files = array_diff(scandir($fileUploadPath . '/downloads'), ['.', '..']);

                            foreach ($files as $file) {
                                $pathToFile = $fileUploadPath . '/downloads/' . $file;
                                if (is_file($pathToFile)) {
                                    $zip->addFile($pathToFile, 'archive/' . $file);
                                }
                            }
                            $zip->close();
                        }
                        unlink($filePath);

                        if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/local/upload/archive/archive.zip')) {
                            $this->deleteFiles($fileUploadPath . '/downloads/');
                            header('Location: /local/upload/archive/archive.zip');
                            die;
                        }

                    } else {
                        echo 'xls error: ' . $xls->error();
                    }
                }
            }

        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        $this->arResult = [
            "CELLS" => $this->charsExcelCell('ABC'),
            'ERROR' => $error
        ];

        $this->IncludeComponentTemplate();
        return true;
    }

    /*
    * php delete function that deals with directories recursively
    */
    public function deleteFiles($target)
    {
        if (is_dir($target)) {
            $files = glob($target . '*', GLOB_MARK); //GLOB_MARK adds a slash to directories returned

            foreach ($files as $file) {
                $this->deleteFiles($file);
            }

            rmdir($target);
        } elseif (is_file($target)) {
            unlink($target);
        }
    }

    public function charsExcelCell($end_column = '', $first_letters = '')
    {
        $columns = [];
        $length = strlen($end_column);
        $letters = range('A', 'Z');

        // Iterate over 26 letters.
        foreach ($letters as $letter) {
            // Paste the $first_letters before the next.
            $column = $first_letters . $letter;
            // Add the column to the final array.
            $columns[] = $column;
            // If it was the end column that was added, return the columns.
            if ($column == $end_column)
                return $columns;
        }

        // Add the column children.
        foreach ($columns as $column) {
            // Don't itterate if the $end_column was already set in a previous itteration.
            // Stop iterating if you've reached the maximum character length.
            if (!in_array($end_column, $columns) && strlen($column) < $length) {
                $new_columns = $this->charsExcelCell($end_column, $column);
                // Merge the new columns which were created with the final columns array.
                $columns = array_merge($columns, $new_columns);
            }
        }

        return $columns;
    }
}

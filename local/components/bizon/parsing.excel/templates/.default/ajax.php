<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Itbizon\Template\Utils\ExcelService;
use Itbizon\Template\Utils\FileService;

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

header('Content-Type: application/json');

function answer(string $message, $data = null, int $code = 200)
{
    http_response_code($code);
    echo json_encode(['message' => $message, 'data' => $data]);
    die();
}

try {
    if (!Loader::includeModule('itbizon.template')) {
        throw new Exception('error load module itbizon.template');
    }

    $request = $_REQUEST;
    $cmd = strval($request['cmd']);

    if ($_SERVER['REQUEST_METHOD'] === "POST" && $cmd == 'get-data') {

        $file = $_FILES['excelFile'];
        $cellName = $request['cellName'];
        $cellLink = $request['cellLink'];

        if (empty($cellName)) {
            $errors[] = "Название ячейки имени не может быть пустым";
        }
        if (empty($cellLink)) {
            $errors[] = "Название ячейки ссылки не может быть пустым";
        }
        if ($file['name'] == "") {
            $errors[] = "Выбирите фаил";
        }
        if (!empty($errors)) {
            answer(implode(' , ', $errors), [], 400);
        }

        $filePath = FileService::uploadFile($file, FileService::PATH_TO_FILES);
        $serviceExcel = new ExcelService($filePath);
        $dataTable = $serviceExcel->getDataTable($cellName, $cellLink);
        $fileName = explode('.', $file['name'])[0] . '_' . uniqid();

        ob_start();
        require(__DIR__ . '/include/table-data.php');
        $html = ob_get_clean();
        unlink($filePath);

        answer('Success', $html);
    }

    if ($_SERVER['REQUEST_METHOD'] === "POST" && $cmd == 'add-file') {

        FileService::addFileToFolder($request['name'], $request['link'], $request['archiveName']);

        answer('');
    }

    if ($_SERVER['REQUEST_METHOD'] === "POST" && $cmd == 'archive-folder') {

        FileService::zipFolder($request['folderName']);

        answer('');
    }

    if ($_SERVER['REQUEST_METHOD'] === "GET" && $cmd == 'remove-archive') {

        unlink(Application::getDocumentRoot() . '/' . FileService::PATH_TO_ARCHIVES . '/' . trim(strval($request['name'])));
        answer('');
    }

    if ($_SERVER['REQUEST_METHOD'] === "GET" && $cmd == 'remove-folder') {
        $pathToFolder = Application::getDocumentRoot() . '/' . FileService::PATH_TO_DOWNLOADS . '/' . trim(strval($request['name']));

        FileService::deleteFiles($pathToFolder);

        answer('');
    }
    if ($_SERVER['REQUEST_METHOD'] === "GET" && $cmd == 'clear-all') {
        $pathToFolder = Application::getDocumentRoot() . '/' . FileService::PATH_TO_FILES;

        FileService::deleteFiles($pathToFolder);

        answer('');
    }
    answer('Команда не найдена', [], 400);

} catch (Exception $e) {
    answer($e->getMessage(), null, 500);
}
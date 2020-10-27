<?php

use Bizon\Main\Utils\ExcelService;
use Bizon\Main\Utils\FileService;
use Bizon\Main\Utils\SimpleXLSX;

define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC", "Y");
define("NOT_CHECK_PERMISSIONS", true);
define("DisableEventsCheck", true);
define("NO_AGENT_CHECK", true);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

header('Content-Type: application/json');

function answer(string $message, array $data = [], int $code = 200)
{
    http_response_code($code);
    echo json_encode(['message' => $message, 'data' => $data]);
    die();
}

try {
    $cmd = strval($_REQUEST['cmd']);
    $request = $_REQUEST;
    //TODO: Подгружаем модуль где лежит наш фаил для работы с xlsx
    if (!\Bitrix\Main\Loader::includeModule('bizon.main')) {
        throw new Exception('Ошибка загрузки модуля bizon.main');
    }

    if ($cmd == 'generate-table') {
        $file = $_FILES['document'];
        $filename = $file['name'];
        if (!$filename) {
            throw new \Exception('Выбирите файл');
        }
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if ($ext != 'xlsx') {
            throw new \Exception('Разрешенный формат файла (xlsx), зугружаемый файл ' . $ext);
        }
        $filePath = FileService::uploadFile($file, FileService::PATH_TO_FILES);
        $archiveName = 'test';

        ob_start();
        require(__DIR__ . '/include/table.php');
        $html = ob_get_clean();

        answer('', ['html' => $html]);
    }

    if ($_SERVER['REQUEST_METHOD'] === "POST" && $cmd == 'add-file') {

        FileService::createFile($request);

        answer('');
    }

    if ($_SERVER['REQUEST_METHOD'] === "POST" && $cmd == 'archive-folder') {

        FileService::zipFolder($request['folderName']);

        answer('');
    }
    throw new Exception('ajax error');
} catch (Exception $e) {
    answer($e->getMessage(), [], 500);
}
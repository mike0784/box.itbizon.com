<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\UserTable;
use \Itbizon\Template\SystemFines\Model\FinesTable;

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
header('Content-Type: application/json');

global $APPLICATION;

function answer($message, $data = null, int $code = 200 )
{
    http_response_code($code);
    echo json_encode(['message' => $message, 'data' => $data]);
    die();
}

try {

    if(!Loader::includeModule('itbizon.kulakov'))
        answer('Ошибка подключения модуля itbizon.kulakov', null, 500);

    if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_REQUEST['remove'])) {

        $id = $_REQUEST['remove'];
        if($id !== 0)
            $t = Itbizon\Kulakov\Orm\Manager::removeProduct($id);

        answer('Success', $id);
    }

    if ($_SERVER['REQUEST_METHOD'] === "GET") {

        $path = $APPLICATION->GetCurDir() . 'ajax.php';
        $invoiceID = $_REQUEST['invoiceID'];
        $productID = 0;

//        if($_REQUEST['invoiceID'] !== 0)
//            $products = Itbizon\Kulakov\Orm\Manager::getProductList(intval($_REQUEST['invoiceID']));

        ob_start();
        require(__DIR__ . '/include/productPopup.php');
        $html = ob_get_clean();
        answer('Success', $html);

    }

    if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_REQUEST['invoice'])) {
        if(!isset($_REQUEST['title']))
            answer('Title empty', null, 400);

        $id = intval($_REQUEST['invoice']);

        if($id === 0) {
            $invoice = Itbizon\Kulakov\Orm\Manager::addInvoice(
                $_REQUEST['title'],
                (isset($_REQUEST['comment']) ? $_REQUEST['comment'] : "")
            );
            $id = $invoice->get("ID");
        } else {
            Itbizon\Kulakov\Orm\Manager::editInvoice(
                $id,
                $_REQUEST['title'],
                (isset($_REQUEST['comment']) ? $_REQUEST['comment'] : "")
            );
        }

        answer('Success', $id, 201);
    }

    if ($_SERVER['REQUEST_METHOD'] === "POST") {

        $id = intval($_REQUEST['editProduct']);
        $invalid = [];
        $product = null;

        if(empty($_REQUEST['product_title'])) $invalid['product_title'] = "Заполните поле";

        if(empty($_REQUEST['product_value'])) $invalid['product_value'] = "Заполните поле";
        if(!is_numeric($_REQUEST['product_value'])) $invalid['product_value'] = "Введите число";

        if(empty($_REQUEST['product_count'])) $invalid['product_count'] = "Заполните поле";
        if(!is_numeric($_REQUEST['product_count'])) $invalid['product_count'] = "Введите число";

        if(!empty($invalid))
            answer('Success', $invalid, 400);

        if($id === 0) {
            $product = Itbizon\Kulakov\Orm\Manager::addProduct([
                'invoice_id'    => $_REQUEST['invoiceID'],
                'title'         => $_REQUEST['product_title'],
                'value'         => $_REQUEST['product_value'],
                'count'         => $_REQUEST['product_count'],
                'comment'       => $_REQUEST['product_comment'],
            ]);

        }

        answer('Success', $product);
    }

} catch (Exception $e) {
    answer($e->getMessage(), null, 500);
}
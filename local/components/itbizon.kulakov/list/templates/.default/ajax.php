<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\UserTable;
use \Itbizon\Template\SystemFines\Model\FinesTable;

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
header('Content-Type: application/json');

global $APPLICATION;

function answer($message, $data = null, int $code = 200)
{
    http_response_code($code);
    echo json_encode(['message' => $message, 'data' => $data]);
    die();
}

try {

    if(!Loader::includeModule('itbizon.kulakov'))
        answer('Ошибка подключения модуля itbizon.kulakov', null, 500);

//    if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_REQUEST['editInvoiceForm'])) {
//        $path = $APPLICATION->GetCurDir() . 'ajax.php';
//        $invoiceID = $_REQUEST['invoiceID'];
//
//        $products = [];
//        if($_REQUEST['invoiceID'] !== 0)
//            $products = Itbizon\Kulakov\Orm\Manager::getProductList(intval($_REQUEST['invoiceID']));
//
//        ob_start();
//        require(__DIR__ . '/include/invoicePopup.php');
//        $html = ob_get_clean();
//        answer('Success', $html);
//    }

    if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_REQUEST['remove'])) {

        $id = $_REQUEST['remove'];
        if($id !== 0)
            $t = Itbizon\Kulakov\Orm\Manager::removeInvoice($id);

        answer('Success', $t);
    }
//
//    if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_REQUEST['editProductForm'])) {
//
//        $path = $APPLICATION->GetCurDir() . 'ajax.php';
//        $invoiceID = $_REQUEST['invoiceID'];
//        $productID = 0;
//
////        if($_REQUEST['invoiceID'] !== 0)
////            $products = Itbizon\Kulakov\Orm\Manager::getProductList(intval($_REQUEST['invoiceID']));
//
//        ob_start();
//        require(__DIR__ . '/include/productPopup.php');
//        $html = ob_get_clean();
//        answer('Success', $html);
//
//    }
//
//    if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_REQUEST['editProduct'])) {
//
//        $id = intval($_REQUEST['editProduct']);
//        $product = null;
//        if($id === 0) {
//            $product = Itbizon\Kulakov\Orm\Manager::addProduct([
//                'invoice_id'    => $_REQUEST['invoiceID'],
//                'title'         => $_REQUEST['title'],
//                'value'         => $_REQUEST['value'],
//                'count'         => $_REQUEST['count'],
//                'comment'       => $_REQUEST['comment'],
//            ]);
//
//        }
//
//        answer('Success', $product);
//    }
//
//    if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_REQUEST['createInvoice'])) {
//        if(!isset($_REQUEST['title']))
//            answer('Title empty', null, 400);
//
//        Itbizon\Kulakov\Orm\Manager::addInvoice(
//            $_REQUEST['title'],
//            (isset($_REQUEST['comment']) ? $_REQUEST['comment'] : "")
//        );
//
//        answer('Success', null, 201);
//    }

} catch (Exception $e) {
    answer($e->getMessage(), null, 500);
}
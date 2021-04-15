<?php

define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC", "Y");
define("NOT_CHECK_PERMISSIONS", true);
define("DisableEventsCheck", true);
define("NO_AGENT_CHECK", true);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

header('Content-Type: application/json');

use Bitrix\Main\Engine\CurrentUser;
use Itbizon\Finance\Helper;
use Itbizon\Finance\Permission;
use Itbizon\Finance\Utils\Money;
use Itbizon\Finance\Utils\SimpleXLSXGen;
use Itbizon\Finance\Model\RequestTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Grid;

Loc::loadMessages(__FILE__);

function answer($state, $message, $data = null)
{
    echo json_encode(['status' => $state, 'message' => $message, 'data' => $data]);
    die();
}

try {
    if (!Loader::includeModule('itbizon.finance'))
        throw new Exception(Loc::getMessage("ITB_FIN.REQUEST.ERROR.MODULE.FINANCE"));

    $request = $_REQUEST;
    $cmd = strval($request['cmd']);
    $userId = $request['userId'];
    $requestId = $request['requestId'];

    $gridOptions = new Grid\Options('request_template_list');
    $sort = $gridOptions->GetSorting(['sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
    $order = $sort['sort'];

    if ($cmd == 'decline') {
        $req = RequestTable::getById($requestId)->fetchObject();
        $result = $req->cancel($userId);

        if ($result->isSuccess())
            answer(true, 'Success', $result);
        answer(false, implode(', ', $result->getErrorMessages()), $result);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $cmd == 'import') {
        $filter = strval($_REQUEST['filter']);
        $data = json_decode($filter, true);
        $logicFilter = [];
        $date = \Bitrix\Main\UI\Filter\Options::fetchDateFieldValue('DATE_CREATE', $data);
        if (isset($date['DATE_CREATE_from']) && !empty($date['DATE_CREATE_from'])) {
            $logicFilter['>=DATE_CREATE'] = $date['DATE_CREATE_from'];
        }
        if (isset($date['DATE_CREATE_to']) && !empty($date['DATE_CREATE_to'])) {
            $logicFilter['<=DATE_CREATE'] = $date['DATE_CREATE_to'];
        }
        if (isset($data['CATEGORY_ID']) && !empty($data['CATEGORY_ID'])) {
            $logicFilter['=CATEGORY_ID'] = $data['CATEGORY_ID'];
        }
        if (isset($data['NAME']) && !empty($data['NAME'])) {
            $logicFilter['%NAME'] = $data['NAME'];
        }
        if (isset($data['STATUS']) && !empty($data['STATUS'])) {
            $logicFilter['=STATUS'] = $data['STATUS'];
        }
        $objList = RequestTable::getList([
            'filter' => $logicFilter,
            'order' => $order,
            'select' => [
                '*',
                'LEAD.TITLE',
                'DEAL.TITLE',
                'CONTACT.NAME',
                'CONTACT.LAST_NAME',
                'COMPANY.TITLE',
                'AUTHOR.NAME',
                'AUTHOR.LAST_NAME',
                'CATEGORY.NAME',
                'APPROVER.NAME',
                'APPROVER.LAST_NAME',
            ]
        ]);
        $downloadData = [];
        $totalAmount = 0;
        $rowsTitle = [
            'ID', 'Статус', 'На что тратим', 'Дата создания', 'Дата согласования', 'Создал заявку', 'Категория',
            'Сумма заявки', 'Ситуация', 'Данные (контрагент)', 'Утвердил/отклонил заявку', 'Комментарий', 'Сущность CRM',
        ];
        $downloadData[] = $rowsTitle;
        while ($obj = $objList->fetchObject()) {
            if(!Permission::getInstance()->isAllowRequestView($obj))
                continue;

            $dateCreate = ($obj->getDateCreate() instanceof \Bitrix\Main\Type\DateTime) ? $obj->getDateCreate()->format('d.m.Y H:i:s') : '';
            $dateApprove = ($obj->getDateApprove() instanceof \Bitrix\Main\Type\DateTime) ? $obj->getDateApprove()->format('d.m.Y H:i:s') : '';
            $fullNameAuthor = $obj->getAuthor() ? $obj->getAuthor()->getLastName() . ' ' . $obj->getAuthor()->getName() : '';
            $categoryName = $obj->getCategory() ? $obj->getCategory()->getName() : '';
            $fullNameApprove = $obj->getApprover() ? $obj->getApprover()->getLastName() . ' ' . $obj->getApprover()->getName() : '';

            $downloadData[] = [
                $obj->getId(), $obj->getStatusName(), $obj->getName(), $dateCreate, $dateApprove, $fullNameAuthor, $categoryName,
                Money::formatfromBase($obj->getAmount(), Money::XLSX), $obj->getCommentSituation(), $obj->getCommentData(), $fullNameApprove, $obj->getApproverComment(),
                Helper::getEntityList()[$obj->getEntityType()] . ': ' . $obj->getEntityName()
            ];
            $totalAmount += $obj->getAmount();
        }
        $downloadData[] = ['', 'Итого:', '', '', '', '', '', Money::formatFromBase($totalAmount, Money::XLSX)];
        $pathToFile = 'https://' . $_SERVER['HTTP_HOST'] . '/local/uploads';
        $uploadDir = $_SERVER["DOCUMENT_ROOT"] . '/local/uploads';
        $xlsx = SimpleXLSXGen::fromArray($downloadData);
        $fileName = gmdate('YmdHi') . ".xlsx";
        $downloadFile = $pathToFile . "/" . $fileName;

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
            throw new \Exception(Loc::getMessage('ITB_FIN.REQUEST.ERROR.MODULE.FINANCE.SYSTEM_ERROR'));
        }
        $xlsx->saveAs($uploadDir . '/' . $fileName);

        answer(true, '', ['pathToFile' => $downloadFile]);
    }
    throw new Exception('ajax error');
} catch (Exception $e) {
    answer(false, $e->getMessage());
}
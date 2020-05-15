<?php

use \Bitrix\Main\Loader;
use Itbizon\Kulakov\Orm\ItbInvoiceTable;

class EditClass extends \CBitrixComponent
{
    public function executeComponent()
    {
        try {
            if(!Loader::includeModule('itbizon.kulakov'))
                throw new Exception('Ошибка подключения модуля itbizon.kulakov');

            $path = $this->GetPath() . '/templates/.default/ajax.php';
            $pathToHome = "/local/test_component/";

            $usersList = Bitrix\Main\UserTable::getList([
                "select"=>["ID","NAME"],
            ])->fetchAll();
            $usersListID = array_column($usersList, 'ID');
            $usersListName = array_column($usersList, 'NAME');
            $users = array_combine($usersListID, $usersListName);

            $invoiceID = intval($this->arParams['ID']);
            $result = [];

            if($invoiceID === 0) {
                $result = [
                    'invoice' => '',
                    'products' => '',
                ];
            } else {
                $result = Itbizon\Kulakov\Orm\Manager::getInvoice($invoiceID);
            }

            $this->arResult = [
                'result'    => $result,
                'invoiceID' => $invoiceID,
                'path'      => $path,
                'pathToHome'=> $pathToHome,
                'users'     => $users,
            ];

            $this->IncludeComponentTemplate();
            return true;

        } catch(Exception $e) {
            ShowMessage($e->getMessage());
        }

        return false;
    }
}
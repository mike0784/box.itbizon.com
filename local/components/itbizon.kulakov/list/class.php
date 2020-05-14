<?php

use \Bitrix\Main\Loader;

class IndexClass extends \CBitrixComponent
{
    public function executeComponent()
    {
        try {
            if(!Loader::includeModule('itbizon.kulakov'))
                throw new Exception('Ошибка подключения модуля itbizon.kulakov');

            $invoicesList = Itbizon\Kulakov\Orm\Manager::getInvoiceList();
            $usersList = Bitrix\Main\UserTable::getList(Array(
                "select"=>Array("ID","NAME"),
            ))->fetchAll();
            $usersListID = array_column($usersList, 'ID');
            $usersListName = array_column($usersList, 'NAME');
            $users = array_combine($usersListID, $usersListName);

            $path = $this->GetPath() . '/templates/.default/ajax.php';
            $invoices = [];

            foreach ($invoicesList as $invoice)
            {
                $invoices[] = Itbizon\Kulakov\Orm\Manager::getInvoice($invoice['ID']);
            }

            $this->arResult = [
                'invoices'  => $invoices,
                'path'      => $path,
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
<?php

use Bitrix\Crm\CompanyTable;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Itbizon\Finance\AccessRight;
use Itbizon\Finance\Model\AccessRightTable;
use Itbizon\Finance\Model\OperationCategoryTable;
use Itbizon\Finance\Model\VaultTable;
use Itbizon\Finance\Operation;

Loc::loadMessages(__FILE__);

/**
 * Class CITBFinanceIncome
 */
class CITBFinanceIncome extends CBitrixComponent
{
    private $arrVaults;
    private $arrCategories;
    private $error;

    /**
     * @return bool|mixed
     */
    public function executeComponent()
    {
        try {
            if(!Loader::includeModule('itbizon.finance'))
                throw new Exception(Loc::getMessage('ITB_FIN.INCOME.ERROR.INCLUDE_FINANCE'));
            if(!Loader::includeModule('crm'))
                throw new Exception(Loc::getMessage('ITB_FIN.INCOME.ERROR.INCLUDE_CRM'));

            $listVault = VaultTable::getList();
            $currentUser = CurrentUser::get();
            $currentUserId = $currentUser->getId();

            while ($objVault = $listVault->fetchObject()) {
                $status = AccessRight::checkPermission(
                        $currentUserId,
                        AccessRightTable::ENTITY_VAULT,
                        $objVault->getId(),
                        AccessRightTable::ACTION_REQUEST_INCOME
                    ) ||
                    AccessRight::checkPermission(
                        $currentUserId,
                        AccessRightTable::ENTITY_VAULT,
                        AccessRightTable::ENTITY_ID_ALL,
                        AccessRightTable::ACTION_REQUEST_INCOME
                    ) ||
                    $currentUser->isAdmin();

                if($status && !$objVault->isVirtual() && !$objVault->isStock()) {
                    $this->arrVaults[$objVault->getId()] = [
                        'NAME' => $objVault->getName(),
                    ];
                }
            }

            $listOperationCategories = OperationCategoryTable::getIncomeList();
            while ($objOperationCategory = $listOperationCategories->fetchObject()) {
                $this->arrCategories[$objOperationCategory->getId()] = [
                    'NAME' => $objOperationCategory->getName(),
                ];
            }

            $request = Application::getInstance()->getContext()->getRequest();
            if(isset($_POST['DATA']) && $request->isAjaxRequest()) {
                try {
                    $amount = round($_POST['DATA']['AMOUNT'] * 100);
                    if($amount < 1)
                        throw new ArgumentException(Loc::getMessage("ITB_FIN.INCOME.ERROR.INVALID_AMOUNT"));

                    $objOperation = Operation::createIncome(array_merge($_POST['DATA'], [
                        'ENTITY_TYPE_ID' => CCrmOwnerType::Company,
                        'RESPONSIBLE_ID' => $currentUserId,
                        'AMOUNT' => $amount,
                    ]));

                    self::ajaxAnswer(Loc::getMessage("ITB_FIN.INCOME.MESSAGE_SUCCESS"), [
                        $objOperation->getId()
                    ], 200);
                } catch (Exception $e) {
                    self::ajaxAnswer($e->getMessage(), null, 500);
                }
            }

        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }

        //Include template
        $this->IncludeComponentTemplate();
        return true;
    }

    /**
     * @param $message
     * @param int $code
     */
    public static function ajaxAnswer($message, $data = null, int $code = 200)
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode(['message' => $message, 'data' => $data]);
        die();
    }

    /**
     * @return array
     */
    public function getCompanies(): array
    {
        $arrCompanies = [];
        $listCompanies = CompanyTable::getList([
            'select' => [
                'ID', 'TITLE'
            ]
        ]);
        while ($objCompany = $listCompanies->fetchObject()) {
            $arrCompanies[$objCompany->getId()] = $objCompany->getTitle();
        }

        return $arrCompanies;
    }

    /**
     * @return array
     */
    public function getCategories(): array
    {
        return is_array($this->arrCategories) ? $this->arrCategories : [];
    }

    /**
     * @return array
     */
    public function getVaults(): array
    {
        return is_array($this->arrVaults) ? $this->arrVaults : [];
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }
}

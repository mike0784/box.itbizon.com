<?php

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Itbizon\Finance;

Loc::loadMessages(__FILE__);

/**
 * Class CITBFinanceOperationAdd
 */
class CITBFinanceOperationAdd extends CBitrixComponent
{
    protected $error;

    /**
     * @return bool|mixed
     */
    public function executeComponent()
    {
        try {
            CJSCore::RegisterExt(
                'landInit',
                [
                    "lang" => $this->GetPath() . '/templates/.default/script.js.php',
                ]
            );
            CJSCore::Init(["landInit"]);

            if (!Loader::includeModule('itbizon.finance'))
                throw new Exception(Loc::getMessage('ITB_FIN.OPERATION_ADD.ERROR.INCLUDE_FIN'));

            if (!Loader::IncludeModule('crm'))
                throw new Exception(Loc::getMessage('ITB_FIN.OPERATION_ADD.ERROR.INCLUDE_CRM'));

            if (!Finance\Permission::getInstance()->isAllowOperationAdd())
                throw new Exception(Loc::getMessage('ITB_FIN.OPERATION_ADD.ERROR.ACCESS_DENY'));

            if (isset($_REQUEST['DATA'])) {

                if(isset($_FILES['FILE']) && is_array($_FILES['FILE']) && !empty($_FILES['FILE']) && $_FILES['FILE']['tmp_name'] && $_FILES['FILE']['error'] == 0) {
                    $file = $_FILES['FILE'];
                    $file['MODULE_ID'] = 'itbizon.finance';
                    $file['old_file'] = '';
                    $file['description'] = '';
                    $file['del'] = '';
                    $fileId = CFile::SaveFile($file, 'itbizon.finance');
                    if(!$fileId) {
                        throw new Exception('Ошибка загрузки файла');
                    }
                    $_REQUEST['DATA']['FILE_ID'] = $fileId;
                }

                // Переводится в копейки
                $_REQUEST['DATA']['AMOUNT'] = round($_REQUEST['DATA']['AMOUNT'] * 100, 0);

                switch (intval($_REQUEST['DATA']['TYPE'])) {
                    case Finance\Model\OperationTable::TYPE_INCOME:
                        $val = Itbizon\Finance\Operation::createIncome($_REQUEST['DATA']);
                        break;
                    case Finance\Model\OperationTable::TYPE_OUTGO:
                        $val = Itbizon\Finance\Operation::createOutgo($_REQUEST['DATA']);
                        break;
                    case Finance\Model\OperationTable::TYPE_TRANSFER:
                        $val = Itbizon\Finance\Operation::createTransfer($_REQUEST['DATA']);
                        break;
                    default:
                        throw new Exception(Loc::getMessage('ITB_FIN.OPERATION_ADD.ERROR.INVALID_TYPE'));
                }

                if (isset($val) && !empty($val->getId())) {
                    $userField = [];
                    foreach ($this->arResult['userFields'] as $key => $noUsed) {
                        $userField[$key] = $_REQUEST[$key];
                    }
                    $manager = new \CUserTypeManager();
                    if (isset($userField)) {
                        $manager->Update(Finance\Model\OperationTable::getUfId(), $val->getId(), $userField);
                    }
                    LocalRedirect($this->arParams['FOLDER']);
                    die();
                } else {
                    throw new Exception(Loc::getMessage('ITB_FIN.OPERATION_ADD.ERROR.CREATE_FAILED'));
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
     * @return mixed
     */
    public function getUserId()
    {
        return CurrentUser::get()->getId();
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return array
     */
    public function getCrmList()
    {
        return [
            CCrmOwnerType::Lead => CCrmOwnerType::GetDescription(CCrmOwnerType::Lead),
            CCrmOwnerType::Deal => CCrmOwnerType::GetDescription(CCrmOwnerType::Deal),
            CCrmOwnerType::Company => CCrmOwnerType::GetDescription(CCrmOwnerType::Company),
            CCrmOwnerType::Contact => CCrmOwnerType::GetDescription(CCrmOwnerType::Contact),
        ];
    }

    /**
     * @throws SystemException
     */
    public function getUserFields()
    {
        static $fields = null;
        if (!$fields) {
            $fields = (new \CUserTypeManager())->GetUserFields(Finance\Model\OperationTable::getUfId(), 0, Bitrix\Main\Application::getInstance()->getContext()->getLanguage());
        }
        return $fields;
    }

    /**
     * @return string
     */
    public function getPathToAjax()
    {
        return $this->GetPath() . '/templates/.default/ajax.php';
    }
}

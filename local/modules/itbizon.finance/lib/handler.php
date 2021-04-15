<?php


namespace Itbizon\Finance;

use Bitrix\Bizproc\WorkflowInstanceTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Localization\Loc;
use Exception;
use Itbizon\Finance\Model\OperationTable;
use Itbizon\Finance\Utils\Money;

Loc::loadMessages(__FILE__);

/**
 * Class Handler
 * @package Itbizon\Finance
 */
class Handler
{
    const COMPANY_INCOME_FIELD = 'UF_CRM_1610629103';
    const COMPANY_OUTGO_FIELD = 'UF_CRM_1610629143';

    /**
     * @param $module
     * @param $tag
     * @param $value
     * @param $arNotify
     * @return bool
     */
    public static function onBeforeConfirmNotify($module, $tag, $value, $arNotify)
    {
        try {
            $result = true;
            if ($module === 'itbizon.finance') {
                if (!Loader::includeModule('im'))
                    throw new Exception(Loc::getMessage('ITB_FIN.HANDLER.ERROR.LOAD_MODULE_IM'));
                $user = CurrentUser::get();
                list($module, $entityType, $entityId) = explode('|', $tag);
                if ($entityType == 'OPERATION') {
                    $operation = Model\OperationTable::getByPrimary($entityId)->fetchObject();

                    $srcRespUserId = 0;
                    $dstRespUserId = 0;

                    if ($operation->getSrcVault())
                        $srcRespUserId = $operation->getSrcVault()->getResponsibleId();

                    if ($operation->getDstVault())
                        $dstRespUserId = $operation->getDstVault()->getResponsibleId();

                    if ($value == 'Y') {
                        $result = $operation->confirm($user->getId());
                        if ($dstRespUserId == $srcRespUserId)
                            $result = $operation->confirm($user->getId());
                    } else {
                        $result = $operation->decline($user->getId());
                        if ($dstRespUserId == $srcRespUserId)
                            $result = $operation->decline($user->getId());
                    }

                    \CIMNotify::DeleteByTag($tag);
                }
            }
        } catch (Exception $e) {
            $result = false;
        }
        return $result;
    }

    /**
     * @param \Bitrix\Main\Event $event
     * @return bool
     */
    public static function onAfterOperationCommit(\Bitrix\Main\Event $event): bool
    {
        try {
            if(!Loader::includeModule('crm'))
                throw new Exception('Error load module crm');
            if(!Loader::includeModule('bizproc'))
                throw new Exception('Error load module bizproc');

            $data = array_values($event->getParameters());
            $operationId = isset($data[0]) ? intval($data[0]) : 0;

            if ($operationId) {
                $operation = OperationTable::getList([
                    'filter' => [
                        '=ID' => $operationId,
                    ],
                    'limit' => 1
                ])->fetchObject();
                if($operation) {
                    if($operation->getEntityTypeId() === \CCrmOwnerType::Company) {
                        $fields = [];

                        self::updateIncomeOutgoFieldsCompany($operation, $fields);

                        if(!empty($fields)) {
                            $company = new \CCrmCompany(false);
                            $company->Update($operation->getEntityId(), $fields);

                            $errors = [];
                            \CBPDocument::AutoStartWorkflows(
                                ['crm', 'CCrmDocumentCompany', 'COMPANY'],
                                \CBPDocumentEventType::Edit,
                                ['crm', 'CCrmDocumentCompany', 'COMPANY_'.$operation->getEntityId()],
                                [],
                                $errors);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            //file_put_contents($_SERVER['DOCUMENT_ROOT'].'/local/test/'.date('Y.m.d').'.log', $e->getMessage().PHP_EOL, FILE_APPEND);
            return false;
        }
        return true;
    }

    /**
     * @param \Bitrix\Main\Event $event
     * @return bool
     */
    public static function onAfterOperationCancel(\Bitrix\Main\Event $event): bool
    {
        try {
            if(!Loader::includeModule('crm'))
                throw new Exception('Error load module crm');
            if(!Loader::includeModule('bizproc'))
                throw new Exception('Error load module bizproc');

            $data = array_values($event->getParameters());
            $operationId = isset($data[0]) ? intval($data[0]) : 0;
            if ($operationId) {
                $operation = OperationTable::getList([
                    'filter' => [
                        '=ID' => $operationId,
                    ],
                    'limit' => 1
                ])->fetchObject();
                if($operation) {
                    if($operation->getEntityTypeId() === \CCrmOwnerType::Company) {
                        $fields = [];

                        self::updateIncomeOutgoFieldsCompany($operation, $fields);

                        if(!empty($fields)) {
                            $company = new \CCrmCompany(false);
                            $company->Update($operation->getEntityId(), $fields);

                            $errors = [];
                            \CBPDocument::AutoStartWorkflows(
                                ['crm', 'CCrmDocumentCompany', 'COMPANY'],
                                \CBPDocumentEventType::Edit,
                                ['crm', 'CCrmDocumentCompany', 'COMPANY_'.$operation->getEntityId()],
                                [],
                                $errors);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            //file_put_contents($_SERVER['DOCUMENT_ROOT'].'/local/test/'.date('Y.m.d').'.log', $e->getMessage().PHP_EOL, FILE_APPEND);
            return false;
        }
        return true;
    }

    /**
     * @param Operation $operation
     * @param array $fields
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function updateIncomeOutgoFieldsCompany(Operation $operation, array &$fields): void
    {
        if ($operation &&
            $operation->getEntityTypeId() === \CCrmOwnerType::Company &&
            in_array($operation->getType(), [OperationTable::TYPE_INCOME, OperationTable::TYPE_OUTGO]))
        {
            $totalAmount = 0;
            $result = OperationTable::getList([
                'filter' => [
                    '=TYPE' => $operation->getType(),
                    '=ENTITY_ID' => $operation->getEntityId(),
                    '=ENTITY_TYPE_ID' => \CCrmOwnerType::Company,
                    '=STATUS' => OperationTable::STATUS_COMMIT
                ],
            ]);
            while ($curOperation = $result->fetchObject()) {
                $totalAmount += $curOperation->getAmount();
            }
            $ufFieldUpdate = ($operation->getType() === OperationTable::TYPE_INCOME) ? self::COMPANY_INCOME_FIELD : self::COMPANY_OUTGO_FIELD;
            $fields[$ufFieldUpdate] = Money::fromBase($totalAmount) . '|RUB';
        }
    }
}
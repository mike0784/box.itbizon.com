<?php

namespace Bizon\Main\Agent;

use Bitrix\Crm\DealTable;
use Bitrix\Bizproc\Workflow\Type\Entity\GlobalConstTable;
use Bitrix\Main\Loader;

class AgentHandler {

    const FIELD_DBCMP           = "UF_CRM_1590046933"; // Дата начала СМР
    const FIELD_STAGE           = "UF_CRM_1590046866"; // Стадия исполнения
    const FIELD_TYPEDEAL        = "UF_CRM_1590046909"; // Тип сделки
    const VAL_STAGE_USED        = "38";                // Стадия исполнения - Передан на исполнение
    const VAL_TYPEDEAL_FLAT     = "40";                // Тип сделки - Плоская
    const VAL_TYPEDEAL_PITCHED  = "41";                // Тип сделки - Скатная

    const BP_ID                 = "28";                // ID БП

    /**
     * Агент для постановки задач руководителям
     * отдела исполнения
     */
    static public function taskForExec() {

        $retVal = __METHOD__ . "();";

        if (!Loader::includeModule('bizproc'))
            return $retVal;

        $constants = GlobalConstTable::getList([
            'filter' => [
                'ID' => [
                    'UF_DSCMP',
                    'UF_DSUMM_FLAT',
                    'UF_DSUMM_PITCHED',
                ]
            ]
        ])->fetchAll();

        // Для удобства обращения по ключам
        $constants = array_combine(
            array_column($constants, "ID"),
            array_column($constants, "PROPERTY_VALUE")
        );

        if(empty($constants["UF_DSCMP"])) return $retVal;

        $dealList = DealTable::getList([
            'filter' => [
                'LOGIC' => 'OR',
                [
                    '>=OPPORTUNITY'                 => $constants["UF_DSUMM_FLAT"],
                    '='.self::FIELD_STAGE           => self::VAL_STAGE_USED,
                    '='.self::FIELD_TYPEDEAL        => self::VAL_TYPEDEAL_FLAT,
                    "!".self::FIELD_DBCMP           => "",
                ],
                [
                    '>=OPPORTUNITY'                 => $constants["UF_DSUMM_PITCHED"],
                    '='.self::FIELD_STAGE           => self::VAL_STAGE_USED,
                    '='.self::FIELD_TYPEDEAL        => self::VAL_TYPEDEAL_PITCHED,
                    "!".self::FIELD_DBCMP           => "",
                ]
            ],
            'select' => [
                '*',
                'UF_*',
            ]
        ]);


        $templateType = [
            'crm',
            'CCrmDeal',
            ''
        ];

        $timestamp       = new \DateTime();
        $timestampNow    = new \DateTime();

        while ($dealItem = $dealList->fetch()) {
            $timestamp->setTimestamp(strtotime($dealItem[self::FIELD_DBCMP]->toString()));
            $intervalDays = $timestampNow->diff($timestamp)->days;

            if(($intervalDays % $constants["UF_DSCMP"]) === 0 && $intervalDays) {
                $arError = [];
                $templateType[2] = "DEAL_" . $dealItem["ID"];

                $workflowID = \CBPDocument::startWorkflow(
                    self::BP_ID,
                    $templateType,
                    [],
                    $arError
                );
//                var_dump($workflowID);
            }
        }

        return $retVal;
    }
}
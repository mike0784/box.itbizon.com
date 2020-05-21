<?php

namespace Bizon\Main\Agent;

use Bitrix\Crm\DealTable;
//use Bitrix\Crm\UserField;
use Bitrix\Disk\Uf\UserFieldManager;
use Bitrix\Bizproc\Workflow\Type\Entity\GlobalConstTable;
use Bitrix\Main\Loader;

class AgentHandler {
    /**
     * Агент для постановки задач руководителям
     * отдела исполнения
     */
    static public function taskForExec() {

        $retVal = "\Bizon\Main\Agent\AgentHandler::taskForExec();";

        if (!Loader::includeModule('bizproc'))
        {
            return $retVal;
        }

        $template = \CBPWorkflowTemplateLoader::GetList(
            ["ID" => "DESC"],
            ["NAME" => "[BizON] Еженедельная задача РОИ"]
        )->Fetch();

        $constants = GlobalConstTable::getList()->fetchAll();

        // Для удобства обращения по ключам
        $constants = array_combine(
            array_column($constants, "ID"),
            array_column($constants, "PROPERTY_VALUE")
        );

//        Дата начала СМР	                        - UF_DBCMP              - Строка
//        Дата начала СМР	                        - UF_DSCMP	            - Целое число
//        Сумма сделки (Плоская)	                - UF_DSUMM_FLAT	        - Число
//        Сумма сделки (Скатная)	                - UF_DSUMM_PITCHED	    - Число
//        Стадия исполнения	                        - UF_STAGE	            - Строка
//        Стадия исполнения - Передан на исполнение	- VAL_STAGE_USED	    - Строка
//        Тип сделки	                            - UF_TYPEDEAL	        - Строка
//        Тип сделки - Плоская	                    - VAL_TYPEDEAL_FLAT	    - Строка
//        Тип сделки - Скатная	                    - VAL_TYPEDEAL_PITCHED	- Строка

        $dealList = DealTable::getList([
            'filter' => [
                'LOGIC' => 'OR',
                [
                    '>=OPPORTUNITY'                 => $constants["UF_DSUMM_FLAT"],
                    '='.$constants["UF_STAGE"]      => $constants["VAL_STAGE_USED"],
                    '='.$constants["UF_TYPEDEAL"]   => $constants["VAL_TYPEDEAL_FLAT"],
                    "!".$constants["UF_DBCMP"]      => "",
                ],
                [
                    '>=OPPORTUNITY'                 => $constants["UF_DSUMM_PITCHED"],
                    '='.$constants["UF_STAGE"]      => $constants["VAL_STAGE_USED"],
                    '='.$constants["UF_TYPEDEAL"]   => $constants["VAL_TYPEDEAL_PITCHED"],
                    "!".$constants["UF_DBCMP"]      => "",
                ]
            ],
            'select' => [
                '*',
                'UF_*',
            ]
        ]);

        while ($dealItem = $dealList->fetch()) {
            $timestamp      = new \DateTime();
            $timestamp->setTimestamp(strtotime($dealItem[$constants["UF_DBCMP"]]->toString()));

            $timestampNow    = new \DateTime();
            $intervalDays = $timestampNow->diff($timestamp)->days;

            if(($intervalDays % $constants["UF_DSCMP"]) === 0 && $intervalDays) {
                $arError = [];
                $template["DOCUMENT_TYPE"][2] = "DEAL_" . $dealItem["ID"];

                $workflowID = \CBPDocument::startWorkflow(
                    $template["ID"],
                    $template["DOCUMENT_TYPE"],
                    [],
                    $arError
                );
//                var_dump($workflowID);
            }
        }

        return $retVal;
    }
}
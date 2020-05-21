<?php

namespace Bizon\Main\Agent;

use Bitrix\Crm\DealTable;
//use Bitrix\Crm\UserField;
use Bitrix\Disk\Uf\UserFieldManager;
use \Bitrix\Bizproc\Workflow\Type\Entity\GlobalConstTable;
use Bitrix\Main\Loader;

class AgentHandler {
    /**
     * Агент для постановки задач руководителям
     * отдела исполнения
     */
    static public function taskForExec() {

        if (!Loader::includeModule('bizproc'))
        {
            return;
        }

        $template = \CBPWorkflowTemplateLoader::GetList(
            ["ID" => "DESC"],
            ["NAME" => "[BizON] Еженедельная задача РОИ"]
        )->Fetch();

        $constants = GlobalConstTable::getList()->fetchAll();

        // Для удобства обращения по ключам
        $constants = array_combine(
            array_column($constants, "ID"),
            array_values($constants)
        );

        $dsummFlat      = $constants["UF_DSUMM_FLAT"]["PROPERTY_VALUE"];
        $dsummPitched   = $constants["UF_DSUMM_PITCHED"]["PROPERTY_VALUE"];
        $dscmp          = $constants["UF_DSCMP"]["PROPERTY_VALUE"];

//        121	CRM_DEAL	UF_DNUM	    Строка	100
//        120	CRM_DEAL	UF_DSUMM	Деньги	100
//        119	CRM_DEAL	UF_DSCMP	Дата	100
//        118	CRM_DEAL	UF_TYPEDEAL	Список	100
//        117	CRM_DEAL	UF_STADY	Список	100
//        116	CRM_DEAL	UF_FOREMAN	Привязка к сотруднику

//        if (!Loader::includeModule('crm'))
//        {
//            ShowError(GetMessage('CRM_MODULE_NOT_INSTALLED'));
//            return;
//        }

        $manager = new \CUserTypeManager();
        $CCrmFields = $manager->GetUserFields("CRM_DEAL", 0, "ru");

        $CCrmFields = array_combine(
            array_column($CCrmFields, "EDIT_FORM_LABEL"),
            array_values($CCrmFields)
        );

        $listFileds = [
            "Тип сделки"            => $CCrmFields["Тип сделки"]['ID'],
            "Стадия исполнения"     => $CCrmFields["Стадия исполнения"]['ID'],
        ];

        $listFiledsVal = [];

        $obEnum = new \CUserFieldEnum;
        foreach ($listFileds as $UF_KEY => $listFiled) {
            $itEnum = $obEnum->GetList([], ["USER_FIELD_ID" => $listFiled]);
            $listFiledsVal[$UF_KEY] = [];
            while ($arEnum = $itEnum->Fetch()) {
                $listFiledsVal[$UF_KEY][$arEnum["VALUE"]] = $arEnum["ID"];
            }
        }

        $fieldStady = $CCrmFields["Стадия исполнения"]["FIELD_NAME"];
        $fieldType = $CCrmFields["Тип сделки"]["FIELD_NAME"];
        $fieldSumm = $CCrmFields["Сумма сделки"]["FIELD_NAME"];
        $fieldCMP = $CCrmFields["Дата начала СМР"]["FIELD_NAME"];

        $dealList = DealTable::getList([
            'filter' => [
                'LOGIC' => 'OR',
                [
                    '='.$fieldStady         => $listFiledsVal["Стадия исполнения"]["Передан на исполнение"],
                    '='.$fieldType          => $listFiledsVal["Тип сделки"]["Плоская"],
                    '>='.$fieldSumm         => $dsummFlat,
                    "!".$fieldCMP           => "",
                ],
                [
                    '='.$fieldStady         => $listFiledsVal["Стадия исполнения"]["Передан на исполнение"],
                    '='.$fieldType          => $listFiledsVal["Тип сделки"]["Скатная"],
                    '>='.$fieldSumm         => $dsummPitched,
                    "!".$fieldCMP           => "",
                ]
            ],
            'select' => [
                '*',
                'UF_*',
            ]
        ])->fetchAll();

        foreach ($dealList as $dealItem) {
            $timestamp      = new \DateTime();
            $timestamp->setTimestamp(strtotime($dealItem[$fieldCMP]->toString()));

            $timestampNow    = new \DateTime();
            $intervalDays = $timestampNow->diff($timestamp)->days;

            if(($intervalDays % $dscmp) === 0 && $intervalDays) {
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

        return 1;
    }
}
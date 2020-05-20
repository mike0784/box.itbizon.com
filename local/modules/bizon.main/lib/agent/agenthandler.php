<?php

namespace Bizon\Main\Agent;

use Bitrix\Crm\DealTable;
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

        $documentTypeID = $template["DOCUMENT_TYPE"][2] . "_";

        $dsummFlat      = $template["CONSTANTS"]["UF_DSUMM_FLAT"]["Default"];
        $dsummPitched   = $template["CONSTANTS"]["UF_DSUMM_PITCHED"]["Default"];
        $dscmp          = $template["CONSTANTS"]["UF_DSCMP"]["Default"];


//        121	CRM_DEAL	UF_DNUM	    Строка	100
//        120	CRM_DEAL	UF_DSUMM	Деньги	100
//        119	CRM_DEAL	UF_DSCMP	Дата	100
//        118	CRM_DEAL	UF_TYPEDEAL	Список	100
//        117	CRM_DEAL	UF_STADY	Список	100
//        116	CRM_DEAL	UF_FOREMAN	Привязка к сотруднику

        $userFields = \CCrmDeal::GetListEx();

        $listFileds = [
            "UF_TYPEDEAL"   => $userFields->arUserFields["UF_TYPEDEAL"]['ID'],
            "UF_STADY"      => $userFields->arUserFields["UF_STADY"]['ID'],
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


        $dealList = DealTable::getList([
            'filter' => [

                'LOGIC' => 'OR',
                [
                    '=UF_STADY' => $listFiledsVal["UF_STADY"]["Передан на исполнение"],
                    '=UF_TYPEDEAL'  => $listFiledsVal["UF_TYPEDEAL"]["Плоская"],
                    '>=UF_DSUMM'     => $dsummFlat,
                    "!UF_DSCMP" => "",
                ],
                [
                    '=UF_STADY' => $listFiledsVal["UF_STADY"]["Передан на исполнение"],
                    '=UF_TYPEDEAL' => $listFiledsVal["UF_TYPEDEAL"]["Скатная"],
                    '>=UF_DSUMM'     => $dsummPitched,
                    "!UF_DSCMP" => "",
                ]
            ],
            'select' => [
                '*',
                'UF_*',
            ]
        ])->fetchAll();

        foreach ($dealList as $dealItem) {
            $timestamp      = new \DateTime();
            $timestamp->setTimestamp(strtotime($dealItem["UF_DSCMP"]->toString()));

            $timestampNow    = new \DateTime();
            $intervalDays = $timestampNow->diff($timestamp)->days;

            var_dump($intervalDays % $dscmp);
            if(($intervalDays % $dscmp) === 0 && $intervalDays) {
                $arError = [];
                $template["DOCUMENT_TYPE"][2] = $documentTypeID . $dealItem["ID"];

                $workflow = \CBPDocument::startWorkflow(
                    $template["ID"],
                    $template["DOCUMENT_TYPE"],
                    [],
                    $arError
                );
            }
        }

        return 1;
    }
}
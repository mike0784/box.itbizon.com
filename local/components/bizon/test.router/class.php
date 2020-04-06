<?php

use Bitrix\Main\UserTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class ComponentClass extends \CBitrixComponent
{
    public function executeComponent()
    {
        if (!\Bitrix\Main\Loader::includeModule('itbizon.template')) {
            return false;
        }

        $arDefaultUrlTemplates404 = [
            'index' => 'index',
            'edit' => '#ID#/edit/',
            'delete' => '#ID#/delete/',
        ];

        $arDefaultVariableAliases404 = [];
        $arDefaultVariableAliases = [];
        $arComponentVariables = [];
        $SEF_FOLDER = '';
        $arUrlTemplates = [];

        if ($this->arParams['SEF_MODE'] == 'Y') {

            $arVariables = [];

            $arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates(
                $arDefaultUrlTemplates404,
                $this->arParams['SEF_URL_TEMPLATES']
            );

            $arVariableAliases = CComponentEngine::MakeComponentVariableAliases(
                $arDefaultVariableAliases404,
                $this->arParams['VARIABLE_ALIASES']
            );

            $componentPage = CComponentEngine::ParseComponentPath(
                $this->arParams['SEF_FOLDER'],
                $arUrlTemplates,
                $arVariables
            );

            if (strlen($componentPage) <= 0) {
                $componentPage = 'index';
            }

            CComponentEngine::InitComponentVariables(
                $componentPage,
                $arComponentVariables,
                $arVariableAliases,
                $arVariables);

            $SEF_FOLDER = $this->arParams['SEF_FOLDER'];

        } else {
            $arVariables = [];

            $arVariableAliases = CComponentEngine::MakeComponentVariableAliases(
                $arDefaultVariableAliases,
                $this->arParams['VARIABLE_ALIASES']
            );

            CComponentEngine::InitComponentVariables(
                false,
                $arComponentVariables,
                $arVariableAliases,
                $arVariables
            );

            $componentPage = '';
            if (intval($arVariables['ELEMENT_ID']) > 0) {
                $componentPage = 'element';
            } else {
                $componentPage = 'index';
            }
        }

        $this->arResult = [
            'FOLDER' => $SEF_FOLDER,
            'URL_TEMPLATES' => $arUrlTemplates,
            'VARIABLES' => $arVariables,
            'ALIASES' => $arVariableAliases,
        ];

        $this->IncludeComponentTemplate($componentPage);
        return true;
    }
}

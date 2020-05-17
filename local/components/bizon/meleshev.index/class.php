<?php

use Bitrix\Main\Loader;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class RouteClass extends \CBitrixComponent
{
    public function executeComponent()
    {
        try {
            if(!Loader::includeModule('itbizon.meleshev'))
                throw new Exception('Модуль itbizon.meleshev должен быть установлен');

            $arDefaultUrlTemplates404 = [
                'list'  => 'list/',
                'edit'  => 'edit/#ID#/',
            ];

            $arDefaultVariableAliases404 = [];
            $arComponentVariables = [];

            if ($this->arParams['SEF_MODE'] == 'Y') {
                $arVariables = [];

                $arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates(
                    $arDefaultUrlTemplates404,
                    ""
                );

                $arVariableAliases = CComponentEngine::MakeComponentVariableAliases(
                    $arDefaultVariableAliases404,
                    ""
                );

                $componentPage = CComponentEngine::ParseComponentPath(
                    $this->arParams['SEF_FOLDER'],
                    $arUrlTemplates,
                    $arVariables
                );

                if (strlen($componentPage) <= 0) {
                    $componentPage = 'list';
                }

                CComponentEngine::InitComponentVariables(
                    $componentPage,
                    $arComponentVariables,
                    $arVariableAliases,
                    $arVariables);

                $SEF_FOLDER = $this->arParams['SEF_FOLDER'];

                $this->arResult = [
                    'FOLDER' => $SEF_FOLDER,
                    'URL_TEMPLATES' => $arUrlTemplates,
                    'VARIABLES' => $arVariables,
                    'ALIASES' => $arVariableAliases,
                ];

                $this->IncludeComponentTemplate($componentPage);
                return true;

            } else {
                throw new Exception('Режим ЧПУ должен быть включен');
            }

        } catch(Exception $e) {
            ShowMessage($e->getMessage());
            return false;
        }
    }
}

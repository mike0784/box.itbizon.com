<?php

use \Bitrix\Main\Loader;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class RouteClass extends CBitrixComponent
{
    public function executeComponent()
    {
        try {
            if(!Loader::includeModule('itbizon.kulakov'))
                throw new Exception('Ошибка подключения модуля itbizon.kulakov');

            $arDefaultUrlTemplates404 = [
                'list' => 'index.php',
                'edit' => 'edit/#ID#/',
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
                    $componentPage = 'index';
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
                throw new SystemException('Режим ЧПУ должен быть включен');
            }

            return false;

        } catch(Exception $e) {
            ShowMessage($e->getMessage());
            return false;
        }

    }
}

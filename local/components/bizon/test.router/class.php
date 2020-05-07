<?php

use Bitrix\Main\Loader;
use \Bitrix\Main\SystemException;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class ComponentClass extends \CBitrixComponent
{
    public function executeComponent()
    {
        try {
            if (!Loader::includeModule('itbizon.template')) {
                throw new SystemException('Модуль itbizon.template должен быть установлен');
            }

            $arDefaultUrlTemplates404 = [
                'index' => 'index',
                'edit' => '#ID#/edit/',
            ];

            $arDefaultVariableAliases404 = [];
            $arComponentVariables = [];

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
        } catch (Exception $e) {
           ShowMessage($e->getMessage());
        }
        return false;
    }
}

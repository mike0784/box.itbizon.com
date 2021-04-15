<?php

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class CITBFinanceVaultRouter
 */
class CITBFinanceVaultRouter extends CBitrixComponent
{
    /**
     * @return mixed|void|null
     * @throws LoaderException
     * @throws Exception
     */
    public function executeComponent()
    {
        if (!Loader::includeModule('itbizon.finance'))
            throw new Exception(Loc::getMessage('ITB_FIN.VAULT_ROUTER.ERROR.INCLUDE_FIN'));

        if (!$this->arParams['SEF_MODE'] == 'Y')
            throw new Exception(Loc::getMessage('ITB_FIN.VAULT_ROUTER.ERROR.SEF_MODE'));

        //Init variables
        $arDefaultUrlTemplates404 = [
            'list' => '/',
            'add' => 'add/',
            'groupadd' => 'groupadd/',
            'edit' => 'edit/#ID#/',
            'groupedit' => 'groupedit/#ID#/',
        ];
        $arDefaultVariableAliases404 = [];
        $arComponentVariables = ['ID'];
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
            $componentPage = 'list';
        }

        CComponentEngine::InitComponentVariables(
            $componentPage,
            $arComponentVariables,
            $arVariableAliases,
            $arVariables
        );

        //Modify variables
        $arVariables['ACTION'] = $componentPage;

        //Result
        $this->arResult = [
            'FOLDER' => $this->arParams['SEF_FOLDER'],
            'URL_TEMPLATES' => $arUrlTemplates,
            'VARIABLES' => $arVariables,
        ];

        //Include template
        $this->IncludeComponentTemplate();
    }
}

<?php

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class CITBScratchBoxRouter
 */
class CITBScratchBoxRouter extends CBitrixComponent
{
	public function executeComponent()
	{
		if (!Loader::includeModule('itbizon.scratch'))
			throw new Exception(Loc::getMessage('ITB_SCRATCH.BOX_ROUTER.ERROR.INCLUDE'));
		//throw new Exception("Module not loaded");

		if (!$this->arParams['SEF_MODE'] == 'Y')
			throw new Exception(Loc::getMessage('ITB_SCRATCH.BOX_ROUTER.ERROR.SEF_MODE'));
		//throw new Exception("SEF mode error");

		//Init variables
		$arDefaultUrlTemplates404 = [
			'list' => '/',
			'add' => 'add/',
			'edit' => 'edit/#ID#/',
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

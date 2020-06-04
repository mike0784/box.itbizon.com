<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Loader;

class CBPItbizonAddYandexMail extends CBPActivity
{

    public function __construct($name)
    {
        parent::__construct($name);
        $this->arProperties = array(
            "DOMAIN"    => "",
            "LOGIN"     => "",
            "PASSWORD"  => "",
            "UID"       => null,
        );

        $this->SetPropertiesTypes(
            array(
                'DOMAIN' => array(
                    'Type' => FieldType::STRING
                ),
                'LOGIN' => array(
                    'Type' => FieldType::STRING
                ),
                'PASSWORD' => array(
                    'Type' => FieldType::STRING
                ),
            ));


    }

    public function Execute()
    {
        try
        {
            if(!Loader::includeModule("bizon.yandexapi"))
                throw new Exception("Модуль не подключен");

//            $options = \Bitrix\Main\Config\Option::getForModule('bizon.yandexapi');

            $oauth = new \Bizon\Yandexapi\Auth\Auth(
                [
                    'APP_NAME'      => 'TestApp3',
                    'APP_ID'        => '1e4662e9550742b9ac25d3e5d782d74d',
                    'APP_PASS'      => '93535ee8f6d44962bef65ef5b50f66ae',
                    'APP_SCOPE'     => [\Bizon\Yandexapi\Helper::$SCOPE_DMAIL],
                    'PDD_TOKEN'     => '2G4XHFIMFSNFIZT7YQBF4GAYEBR2YQ6E5DF332UQQYRB6K5JYG6Q',
                    'PDD_LIFETIME'  => new \Bitrix\Main\Type\Date(),
                ]
            );

            $mailDomain = new \Bizon\Yandexapi\Services\MailDomain($oauth);

            $uid = $mailDomain->create(
                $this->arProperties['LOGIN'],
                $this->arProperties['PASSWORD'],
                $this->arProperties['DOMAIN']
            );

            if(!$mailDomain->isSuccess())
                $this->WriteToTrackingService($mailDomain->getErrorMessage());
            else
                $this->WriteToTrackingService("Почта создана");

            $this->UID = $uid;

        }
        catch (Exception $e)
        {
            $this->WriteToTrackingService($e->getMessage());
        }

        return CBPActivityExecutionStatus::Closed;
    }


    public static function ValidateProperties($arTestProperties = array(), CBPWorkflowTemplateUser $user = null)
    {
        $arErrors = array();

        if (!array_key_exists("DOMAIN", $arTestProperties) || empty($arTestProperties["DOMAIN"])) {
            $arErrors[] = array(
                "code" => "NotExist",
                "parameter" => "DOMAIN",
                "message" => 'Поле DOMAIN не может быть пустым'
            );
        }

        if (!array_key_exists("LOGIN", $arTestProperties) || empty($arTestProperties["LOGIN"])) {
            $arErrors[] = array(
                "code" => "NotExist",
                "parameter" => "LOGIN",
                "message" => 'Поле LOGIN не может быть пустым'
            );
        }

        if (!array_key_exists("PASSWORD", $arTestProperties) || empty($arTestProperties["PASSWORD"])) {
            $arErrors[] = array(
                "code" => "NotExist",
                "parameter" => "PASSWORD",
                "message" => 'Поле PASSWORD не может быть пустым'
            );
        }

        return array_merge($arErrors, parent::ValidateProperties($arTestProperties, $user));
    }

    public static function GetPropertiesDialog(
        $documentType,
        $activityName,
        $arWorkflowTemplate,
        $arWorkflowParameters,
        $arWorkflowVariables,
        $arCurrentValues = null,
        $formName = "",
        $form = null,
        $siteId = ""
    )
    {
        if (!is_array($arCurrentValues)) {
            $arCurrentValues = array(
                "DOMAIN"    => $_SERVER['SERVER_NAME'],
                "LOGIN"     => "",
                "PASSWORD"  => "",
            );

            $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName(
                $arWorkflowTemplate, $activityName);

            if (is_array($arCurrentActivity['Properties'])) {
                $arCurrentValues = array_merge($arCurrentValues, $arCurrentActivity['Properties']);
            }
        }

        $runtime = CBPRuntime::GetRuntime();
        return $runtime->ExecuteResourceFile(__FILE__, "properties_dialog.php",
            array(
                "arCurrentValues" => $arCurrentValues,
                "formName" => $formName
            ));
    }

    public static function GetPropertiesDialogValues(
        $documentType,
        $activityName,
        &$arWorkflowTemplate,
        &$arWorkflowParameters,
        &$arWorkflowVariables,
        $arCurrentValues,
        &$arErrors
    )
    {
        $arErrors = self::ValidateProperties(
            $arCurrentValues,
            new CBPWorkflowTemplateUser(CBPWorkflowTemplateUser::CurrentUser)
        );

        if (!empty($arErrors)) {
            return false;
        }

        $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName(
            $arWorkflowTemplate,
            $activityName);

        $property = array(
            "DOMAIN"    => $arCurrentValues['DOMAIN'],
            "LOGIN"     => $arCurrentValues['LOGIN'],
            "PASSWORD"  => $arCurrentValues['PASSWORD'],
        );

        $arCurrentActivity['Properties'] = $property;
        return true;
    }
}

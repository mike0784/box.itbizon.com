<?php

namespace Bizon\Yandexapi\Auth;

use Bizon\Yandexapi\Auth\Model;

class Auth
{
    // Данные авторизации
    private $oauth = null;

    // Используется в случае регистрации
    private $pdd = null;

    public function __construct($auth)
    {
        if(!is_array_assoc($auth))
            throw new \Exception("Invalid auth data");

        if(!array_key_exists("APP_NAME", $auth))
            throw new \Exception("Undefined APP_NAME");

        if(
            array_key_exists("APP_ID", $auth) &&
            array_key_exists("APP_PASS", $auth) &&
//            array_key_exists("APP_CALLBACK", $auth) &&
            array_key_exists("APP_SCOPE", $auth) &&
            array_key_exists("PDD_TOKEN", $auth) &&
            array_key_exists("PDD_LIFETIME", $auth)
        )
        {
            // Проверяем на повторную запись
            $currentOAuth = Model\OAuthTable::getList([
                'select' => [
                    '*',
                    'PDD',
                ],
                'filter' => [
                    '=APP_NAME' => $auth['APP_NAME'],
                ]
            ])->fetchObject();

            if($currentOAuth)
            {
                $this->oauth = $currentOAuth;
                return $this;
            }

            // Регистрация приложения yandex oauth и PDD в базе данных
            $this->pdd = Model\PDDTable::add([
                'TOKEN'     => $auth["PDD_TOKEN"],
                'LIFETIME'  => $auth["PDD_LIFETIME"],
            ]);

            if($this->pdd->isSuccess())
            {
                $oauth = Model\OAuthTable::add([
                    'APP_NAME'      => $auth["APP_NAME"],
                    'APP_ID'        => $auth["APP_ID"],
                    'APP_PASS'      => $auth["APP_PASS"],
//                    'APP_CALLBACK'  => $auth["APP_CALLBACK"],
                    'APP_SCOPE'     => $auth["APP_SCOPE"],
                    'PDD_ID'        => $this->pdd->getId(),
                ]);

                if($oauth->isSuccess())
                    $this->oauth = $oauth->getObject();
                else
                    throw new \Exception(print_r($oauth->getErrorMessages(), true));
            }
            else
                throw new \Exception(print_r($this->pdd->getErrorMessages(), true));

        }
        else
        {
            // Загружаем данные авторизации по APP_NAME
            $oauth = Model\OAuthTable::getList([
                'select' => [
                    '*',
                    'PDD',
                ],
                'filter' => [
                    '=APP_NAME' => $auth['APP_NAME'],
                ]
            ])->fetchObject()();

            if($oauth)
                $this->oauth = $oauth;
        }

    }

    public function getPddKey()
    {
        if(isset($this->pdd))
            $pddObj = $this->pdd->getObject();
        else
            $pddObj = $this->oauth->get("PDD");

        if($pddObj)
            return $pddObj->get("TOKEN");

        return false;
    }

}
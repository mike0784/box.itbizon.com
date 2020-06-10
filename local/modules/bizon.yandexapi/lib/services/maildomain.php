<?php

namespace Bizon\Yandexapi\Services;

use Bizon\Yandexapi\Auth\Auth;
use Bizon\Yandexapi\Helper;

class MailDomain
{
    private $oauth = null;
    private $err = "";

    public function __construct(Auth $oauth)
    {
        $this->oauth = $oauth;
    }

    public function create($login, $pass, $domain)
    {

        try
        {

            // Проверка требований Яндекса
            if($login === $pass)
                throw new \Exception("Пароль не должен совпадать с логином");

            if(!preg_match('/(\w|\w+@\w+.\w+)/m', $login))
                throw new \Exception("Некорректный логин");

            if(!preg_match('/[\w\`\!\@\#\$\%\^\&\*\(\)\-\_\=\+\[\]\{\}\;\:\"\\\|\,\.\<\>\/\?]+/m', $pass))
                throw new \Exception("Некорректный пароль");

            if(!preg_match('/[\w\.]+/m', $domain))
                throw new \Exception("Некорректный домен");

            $result = (Array)Helper::requestPDD(
                '/api2/admin/email/add',
                [
                    'domain'    => $domain,
                    'login'     => $login,
                    'password'  => $pass,
                ],
                [
                    'PddToken' => $this->oauth->getPddKey()
                ]
            );

            if(array_key_exists('error', $result))
                throw new \Exception('Ошибка добавления почты: ' . $result['error']);
            elseif(!array_key_exists('uid', $result))
                throw new \Exception('uid не получен');
            else
                return $result['uid'];

        }
        catch (\Exception $e)
        {
            $this->err = $e->getMessage();
            throw new \Exception($e->getMessage());
        }

        return 0;
    }

    public function isSuccess()
    {
        if(empty($this->err))
            return true;
        else
            return false;
    }

    public function getErrorMessage()
    {
        return $this->err;
    }
}
<?php

namespace Bizon\Yandexapi\Services;

use Bizon\Yandexapi\Auth\Auth;
use Bizon\Yandexapi\Helper;

class MailDomain
{
    private $oauth = null;
    private $err = "";

    public function __construct($oauth)
    {
        if(get_class($oauth) != Auth::class)
            throw new \Exception("Объект \$oauth не пренадлежит классу ".Auth::class);

        $this->oauth = $oauth;
    }

    public function create($login, $pass, $domain)
    {

        // Сбрасываем на случай если были ошибки в предыдущих запросах
        $err = "";

        // Проверка требований Яндекса
        if($login === $pass)
            $err = "Пароль не должен совпадать с логином";

        if(!preg_match('/(\w|\w+@\w+.\w+)/m', $login))
            $err = "Некорректный логин";

        if(!preg_match('/[\w\`\!\@\#\$\%\^\&\*\(\)\-\_\=\+\[\]\{\}\;\:\"\\\|\,\.\<\>\/\?]+/m', $pass))
            $err = "Некорректный пароль";

        if(!preg_match('/[\w\.]+/m', $domain))
            $err = "Некорректный домен";

        // Выходим если валидация не прошла
        if($err !== "")
        {
            $this->err = $err;
            return false;
        }

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

        if(is_array_assoc($result))
        {
            if(array_key_exists('REQUEST_ERROR', $result))
                $this->err = $result['REQUEST_ERROR'];
            elseif(array_key_exists('error', $result))
                $this->err = "Ошибка добавления почты: " . $result['error'];
            elseif(!array_key_exists('uid', $result))
                $this->err = 'uid не получен';
            else
                return $result['uid'];
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
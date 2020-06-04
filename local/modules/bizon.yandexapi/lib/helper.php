<?php

namespace Bizon\Yandexapi;

class Helper
{
    // ...
    public static $SCOPE_MAIL = 31;
    public static $SCOPE_DMAIL = 32;

    /**
     * Выполняет POST запрос
     * @param $auth
     * @param $method
     * @param $data
     * @param array $headers
     * @return mixed|string[]
     */
    public static function requestPDD($method, $data, $headers = [])
    {
        if(!is_array_assoc($headers))
            return ['REQUEST_ERROR' => "Заголовок должен быть ассоциативным массивом"];

        if(!array_key_exists("PddToken", $headers))
            return ['REQUEST_ERROR' => "Заголовки должны включать PDD токен"];

        $requestHeaders = array_map(function($key, $el) {
            return $key.": ".$el;
        }, array_keys($headers), $headers);


        $queryUrl  = 'https://pddimp.yandex.ru/' . $method;
//        $queryUrl  = 'https://gg5yhty.requestcatcher.com/test/';
        $queryData = http_build_query($data);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_POST            => 1,
            CURLOPT_HTTPHEADER      => $requestHeaders,
            CURLOPT_HEADER          => 0,
            CURLOPT_RETURNTRANSFER  => 1,
            CURLOPT_URL             => $queryUrl,
            CURLOPT_POSTFIELDS      => $queryData,
        ]);

        $result = curl_exec($curl);

        if(!$result)
            return ['REQUEST_ERROR' => "Ошибка выполнения запроса"];

        curl_close($curl);

        return \json_decode($result);
    }
}
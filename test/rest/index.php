<?php

use Bitrix\Main\Loader;
use Itbizon\Service\Log;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Тест");

class Rest {
    /**
     * @param string $url
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public static function request(string $url, array $params = [])
    {
        if (!Loader::includeModule('itbizon.service')) {
            throw new Exception('Error load module itbizon.service');
        }
        $log = new Log('rest');
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        $log->add($url);
        $answer = false;

        $curl = curl_init();
        try {
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLINFO_HEADER_OUT    => true,
            ]);

            $answer = curl_exec($curl);
            if(curl_errno($curl) !== CURLE_OK) {
                $log->add($answer, Log::LEVEL_ERROR);
                throw new Exception('Ошибка CURL: '.curl_error($curl).' ('.curl_errno($curl).')');
            }

            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($httpCode != 200) {
                $log->add($answer, Log::LEVEL_ERROR);
                throw new Exception('HTTP код ('.$httpCode.')');
            }
            $log->add($answer, Log::LEVEL_OK);
        } catch (Exception $e) {
            $log->add($e->getMessage(), Log::LEVEL_ERROR);
        } finally {
            curl_close($curl);
        }
        return json_decode($answer, true);
    }
}

try {
    $url = 'https://box.itbizon.com/rest/1/s038q2ey257uxv9p/';
    $contactId = 53;
    $mainDealId = 210;

    $activityList = [];
    $taskIds = [];
    $commentList = [];

    $answer = Rest::request(
        $url . 'crm.deal.list',
        [
            'select' => ['*', 'UF_*'],
            'filter' => [
                'CONTACT_ID' => $contactId
            ]
        ]
    );
    //echo '<pre>' . print_r($answer, true) . '</pre>';

    //echo '<pre>' . print_r(Rest::request($url . 'crm.activity.fields'), true) . '</pre>';

    foreach ($answer['result'] as $deal) {
        echo '<h4>Сделка # ' . $deal['ID'] . ' ' . $deal['TITLE'] . '</h4>';

        $answer2 = Rest::request(
            $url . 'crm.activity.list',
            [
                'select' => ['*', 'COMMUNICATIONS'],
                'filter' => [
                    'OWNER_TYPE_ID' => 2,
                    'OWNER_ID' => $deal['ID'],
                ]
            ]
        );
        echo '<b>Дела</b><br>';
        foreach ($answer2['result'] as $activity) {
            //echo '<pre>' . print_r($activity, true) . '</pre>';
            if (in_array($activity['TYPE_ID'], [1, 2, 4])) { //см CCrmActivityType::*
                $activityList[] = $activity;
            }
            if ($activity['TYPE_ID'] == 3) { //task
                $taskIds[] = $activity['ASSOCIATED_ENTITY_ID'];
            }
        }
        echo 'Дела: ' . count($activityList) . '<br>';
        echo 'Задачи: ' . count($taskIds) . '<br>';

        $answer2 = Rest::request(
            $url . 'crm.timeline.comment.list',
            [
                'select' => [
                    'ID',
                    'CREATED',
                    'AUTHOR_ID',
                    'COMMENT', 
                    'FILES',
                ],
                'filter' => [
                    'ENTITY_ID' => $deal['ID'],
                    'ENTITY_TYPE' => 'deal',
                ]
            ]
        );
        echo '<b>Комментарии</b><br>';
        foreach ($answer2['result'] as $comment) {
            //echo '<pre>' . print_r($comment, true) . '</pre>';
            $commentList[] = $comment;
        }
        echo 'Всего: ' . count($commentList) . '<br>';
    }

    if (0) {
        //Copy activity
        foreach($activityList as $activity) {
            echo '<pre>' . print_r($activity, true) . '</pre>';
            $activity['OWNER_ID'] = $mainDealId;
            if ($activity['TYPE_ID'] == 4) { //Email
                $activity['DIRECTION'] = 2;
                $activity['COMPLETED'] = 'N';
            }
            $answer = Rest::request(
                $url . 'crm.activity.add',
                [
                    'fields' => $activity
                ]
            );
            echo '<pre>' . print_r($answer, true) . '</pre>';
        }

        //Change tasks
        foreach($taskIds as $taskId) {
            $answer = Rest::request(
                $url . 'tasks.task.update',
                [
                    'taskId' => $taskId,
                    'fields' => [
                        'UF_CRM_TASK' => ['D_' . $mainDealId]
                    ]
                ]
            );
            echo '<pre>' . print_r($answer, true) . '</pre>';
        }

        //Create comments
        foreach($commentList as $comment) {
            /*$answer = Rest::request(
                $url . 'crm.timeline.comment.add',
                [
                    'fields' => [
                        'ENTITY_ID' => $mainDealId,
                        'ENTITY_TYPE' => 'deal',
                        'CREATED' => $comment['CREATED'],
                        'AUTHOR_ID' => $comment['AUTHOR_ID'],
                        'COMMENT' => $comment['COMMENT']
                    ]
                ]
            );*/
            $answer = Rest::request(
                $url . 'crm.timeline.comment.update',
                [
                    'id' => $comment['ID'],
                    'fields' => [
                        'ENTITY_ID' => $mainDealId,
                        'ENTITY_TYPE' => 'deal',
                    ]
                ]
            );
            echo '<pre>' . print_r($answer, true) . '</pre>';
        }
    }

} catch(Exception $e) {
    echo '<p>'.$e->getMessage().'</p>';
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
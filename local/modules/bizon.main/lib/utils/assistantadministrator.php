<?php

namespace Bizon\Main\Utils;

use Bitrix\Main\Loader;
use Bitrix\Tasks\Exception;
use Bizon\Main\Log;
use CTasks;

class AssistantAdministrator
{
    const CHIEF_ID = 1;
    const TASK_GROUP_IDS = [
        12
    ];
    const ASSISTANTS = [
        26
    ];

    /**
     * @param int $taskId
     * @param int $userId
     * @param int $chiefId
     */
    public static function changeTaskManager(int $taskId, int $userId, int $chiefId = self::CHIEF_ID): void
    {
        $log = new Log('change_task_manager');
        try {
            if (!Loader::includeModule('tasks')) {
                throw new Exception('module tasks not include');
            }
            $task = \Bitrix\Tasks\Internals\TaskTable::getByPrimary($taskId, [
                'select' => ['GROUP_ID', 'RESPONSIBLE_ID']
            ])->fetch();
            $taskGroupId = $task['GROUP_ID'];
            $responsibleId = $task['RESPONSIBLE_ID'];

            if (self::checkUser($userId, $taskGroupId, $responsibleId)) {
                $result = \Bitrix\Tasks\Internals\TaskTable::update($taskId, [
                    'CREATED_BY' => $chiefId,
                    'CHANGED_BY' => $chiefId,
                    'STATUS_CHANGED_BY' => $chiefId,
                ]);

                if (!$result->isSuccess()) {
                    throw new Exception('Произашла ошибка при обновление задачи ID=' . $taskId);
                }
                $arTask = array("AUDITORS" => [$userId]);
                $t = new CTasks;
                $success = $t->Update($taskId, $arTask);
                if (!$success) {
                    throw new Exception('Произашла ошибка при обновление задачи ID=' . $taskId);
                }
            }
        } catch (\Exception $e) {
            $log->Add('Ошибка переопределение постановщика ID задачи = ' . $taskId);
        }
    }

    public static function checkUser(int $userId, int $taskGroupId, int $responsibleId): bool
    {
        return $responsibleId !== self::CHIEF_ID &&
            !in_array($responsibleId, self::ASSISTANTS) &&
            in_array($userId, self::ASSISTANTS) &&
            in_array($taskGroupId, self::TASK_GROUP_IDS);
    }
}
<?php


namespace Itbizon\Basis\Utils;

use Bitrix\Socialnetwork\UserToGroupTable;
use Bitrix\Tasks\ActionFailedException;
use CTaskItem;

class Handler
{
    /**
     * @param int $id
     * @param array $data
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Exception
     */
    public static function onBeforeTaskAdd(array &$data): void
    {
        $groupId = intval($data['GROUP_ID']);
        $weekNumber = intval($data['UF_WEEK_NUMBER']);

        if (!$weekNumber) {
            $data['UF_WEEK_NUMBER'] = WeekDay::getCurrentWeek();
        }

        if ($groupId) {
            $usersIds = self::getUsersIdsByGroupId($groupId);

            if ($usersIds) {
                $data['AUDITORS'] = $usersIds;
            }
        }
    }

    public static function onBeforeTaskUpdate(int $id, array &$data)
    {
        try {
            $task = CTaskItem::getInstance($id, 1);
            $groupId = intval($task['GROUP_ID']);

            if ($groupId || isset($data['GROUP_ID'])) {
                if (isset($data['GROUP_ID']) && intval($data['GROUP_ID'])) {
                    $groupId = intval($data['GROUP_ID']);
                }
                $usersIds = self::getUsersIdsByGroupId($groupId);
                if ($data['AUDITORS']) {
                    $data['AUDITORS'] = array_unique(array_merge($data['AUDITORS'], $usersIds));
                } else {
                    $data['AUDITORS'] = $usersIds;
                }
            }

        } catch (\Exception $e) {
            throw new ActionFailedException('Ошибка: ' . $e->getMessage());
        }
    }

    /**
     * @param int $id
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getUsersIdsByGroupId(int $id): array
    {
        $result = UserToGroupTable::getList(array(
            "filter" => [
                "=GROUP_ID" => $id, '=ROLE' => ['A',/*Роль владельца группы*/ 'E' /*Роль модератора*/]
            ],
            "select" => ['USER_ID']
        ));
        $usersIds = [];
        while ($row = $result->fetch()) {
            $usersIds[] = $row['USER_ID'];
        }
        return array_unique($usersIds);
    }
}
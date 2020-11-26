<?php


namespace Itbizon\Basis\Utils;

use Bitrix\Socialnetwork\UserToGroupTable;
use Bitrix\Tasks\ActionFailedException;
use CCrmDeal;
use CCrmLead;
use COption;
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

            if (isset($data['GROUP_ID']) || $groupId) {
                if (isset($data['GROUP_ID'])) {
                    $groupId = intval($data['GROUP_ID']);
                }

                if ($groupId) {
                    $usersIds = self::getUsersIdsByGroupId($groupId);
                    if ($data['AUDITORS']) {
                        $data['AUDITORS'] = array_unique(array_merge($data['AUDITORS'], $usersIds));
                    } else {
                        $data['AUDITORS'] = $usersIds;
                    }
                }
            }

        } catch (\Exception $e) {
            throw new ActionFailedException('Ошибка: ' . $e->getMessage());
        }
    }

    /**
     * @param int $id
     * @param array $data
     * @throws \Exception
     */
    public static function onActivityAdd(int $id, array $data): void
    {
        $dealsOrLeads = $data['BINDINGS'];
        if ($dealsOrLeads) {
            $customFieldLead = COption::GetOptionString("itbizon.basis", "date_last_activity_lead");
            $customFieldDeal = COption::GetOptionString("itbizon.basis", "date_last_activity_deal");

            foreach ($dealsOrLeads as $item) {
                $dealOrLeadId = intval($item['OWNER_ID']);

                if (intval($item['OWNER_TYPE_ID']) == \CCrmOwnerType::Lead && $customFieldLead && $dealOrLeadId) {
                    self::updateLastDateLead($dealOrLeadId, $customFieldLead);
                }
                if ($item['OWNER_TYPE_ID'] == \CCrmOwnerType::Deal && $customFieldDeal && $dealOrLeadId) {
                    self::updateLastDateDeal($dealOrLeadId, $customFieldDeal);
                }

            }
        }
    }

    /**
     * @param int $id
     * @param array $data
     * @throws \Bitrix\Main\SystemException
     * @throws \Exception
     */
    public static function onAfterAddComment(): void
    {
        $application = \Bitrix\Main\Application::getInstance();
        $request = $application->getContext()->getRequest();
        $ownerTypeID = intval($request->getPost('OWNER_TYPE_ID'));
        $ownerID = intval($request->getPost('OWNER_ID'));

        $customFieldLead = COption::GetOptionString("itbizon.basis", "date_last_activity_lead");
        $customFieldDeal = COption::GetOptionString("itbizon.basis", "date_last_activity_deal");

        if ($ownerTypeID == \CCrmOwnerType::Lead && $ownerID && $customFieldLead) {
            self::updateLastDateLead($ownerID, $customFieldLead);
        }
        if ($ownerTypeID == \CCrmOwnerType::Deal && $ownerID && $customFieldDeal) {
            self::updateLastDateDeal($ownerID, $customFieldDeal);
        }
    }

    /**
     * @param int $leadId
     * @param string $customField
     * @throws \Exception
     */
    protected static function updateLastDateLead(int $leadId, string $customField): void
    {
        $lead = new CCrmLead(true);
        $fields = [$customField => (new \DateTime('now'))->format('d.m.Y H:i:s')];
        $lead->update($leadId, $fields);
    }

    /**
     * @param int $dealId
     * @param string $customField
     * @throws \Exception
     */
    protected static function updateLastDateDeal(int $dealId, string $customField): void
    {
        $lead = new CCrmDeal(true);
        $fields = [$customField => (new \DateTime('now'))->format('d.m.Y H:i:s')];
        $lead->update($dealId, $fields);
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
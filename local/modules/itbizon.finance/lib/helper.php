<?php


namespace Itbizon\Finance;


use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use CCrmOwnerType;
use Exception;

/**
 * Class Helper
 * @package Itbizon\Finance
 */
class Helper
{
    /**
     * @param array $uiFilter
     * @param array $fields
     * @return array
     */
    public static function FilterUI2D7(array $uiFilter, array $fields = [])
    {
        $filter = [];
        if (!isset($fields['search']))
            $fields['search'] = [];
        if (!isset($fields['simple']))
            $fields['simple'] = [];
        if (!isset($fields['date']))
            $fields['date'] = [];
        if (!isset($fields['number']))
            $fields['number'] = [];
        if (!isset($fields['list']))
            $fields['list'] = [];

        if (isset($uiFilter['FIND']) && !empty($uiFilter['FIND']) && !empty($fields['search'])) {
            $search_filter = ['LOGIC' => 'OR'];
            foreach ($fields['search'] as $field)
                $search_filter[$field] = '%' . $uiFilter['FIND'] . '%';
            $filter[] = $search_filter;
        } else {
            foreach ($fields['search'] as $field)
                if (isset($uiFilter[$field]))
                    $filter[$field] = '%' . $uiFilter[$field] . '%';
        }
        //Simple fields
        foreach ($fields['simple'] as $field) {
            if (isset($uiFilter[$field]))
                $filter[$field] = $uiFilter[$field];
        }
        //Date fields
        foreach ($fields['date'] as $field) {
            if (isset($uiFilter[$field . '_from']) && isset($uiFilter[$field . '_to']))
                $filter[] = [
                    '>=' . $field => $uiFilter[$field . '_from'],
                    '<=' . $field => $uiFilter[$field . '_to'],
                ];
        }
        //Number fields
        foreach ($fields['number'] as $field => $value) {
            if (isset($uiFilter[$field . '_numsel'])) {
                if ($uiFilter[$field . '_numsel'] == 'exact')
                    $filter['=' . $field] = $uiFilter[$field . '_from'] * $value;
                else if ($uiFilter[$field . '_numsel'] == 'range')
                    $filter[] = [
                        '>=' . $field => $uiFilter[$field . '_from'] * $value,
                        '<=' . $field => $uiFilter[$field . '_to'] * $value,
                    ];
                else if ($uiFilter[$field . '_numsel'] == 'more')
                    $filter['>' . $field] = $uiFilter[$field . '_from'] * $value;
                else if ($uiFilter[$field . '_numsel'] == 'less')
                    $filter['<' . $field] = $uiFilter[$field . '_to'] * $value;
            }
        }

        // List fields
        foreach ($fields['list'] as $field => $value) {
            if (isset($uiFilter[$value]) && is_array($uiFilter[$value])) {
                if (count($uiFilter[$value]) > 1) {
                    $listFilter = [];
                    $listFilter['LOGIC'] = 'OR';
                    foreach ($uiFilter[$value] as $item)
                        $listFilter[] = ['=' . $value => $item];

                    if (!empty($filter))
                        $filter = ['LOGIC' => 'AND', $filter, $listFilter];
                    else
                        $filter = $listFilter;
                } else {
                    foreach ($uiFilter[$value] as $item)
                        $filter['=' . $value] = $item;
                }
            }
        }
        return $filter;
    }

    /**
     * @param array $filter
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getUserList(array $filter = [])
    {
        $users = [];
        if (empty($filter))
            $filter = [
                'ACTIVE' => 'Y',
                'IS_REAL_USER' => 'Y'
            ];
        $result = UserTable::getList([
            'select' => ['ID', 'LAST_NAME', 'NAME'],
            'filter' => $filter,
            'order' => ['LAST_NAME', 'NAME'],
        ]);
        while ($user = $result->fetchObject())
            $users[$user->getId()] = $user->getLastName() . ' ' . $user->getName() . ' [' . $user->getId() . ']';
        return $users;
    }
    
    /**
     * @return array
     */
    public static function getEntityList()
    {
        return [
            CCrmOwnerType::Lead => CCrmOwnerType::GetDescription(CCrmOwnerType::Lead),
            CCrmOwnerType::Deal => CCrmOwnerType::GetDescription(CCrmOwnerType::Deal),
            CCrmOwnerType::Contact => CCrmOwnerType::GetDescription(CCrmOwnerType::Contact),
            CCrmOwnerType::Company => CCrmOwnerType::GetDescription(CCrmOwnerType::Company),
        ];
    }
    
    /**
     * @param $entityType
     * @return string
     */
    public static function getEntityLink($entityType)
    {
        $links = [
            CCrmOwnerType::Lead => '/crm/lead/details/',
            CCrmOwnerType::Deal => '/crm/deal/details/',
            CCrmOwnerType::Contact => '/crm/contact/details/',
            CCrmOwnerType::Company => '/crm/company/details/',
        ];
        return $links[$entityType];
    }

    /**
     * @param mixed $to
     * @param string $message
     * @param string $notifyEvent
     * @return bool
     */
    public static function sendNotify($to, string $message, string $notifyEvent = 'FINANCE'): bool
    {
        try {
            if (!is_array($to))
                $to = [$to];
            $to = array_unique($to);
            foreach ($to as $userId) {
                if (is_numeric($userId)) {
                    \CIMNotify::Add([
                        'FROM_USER_ID' => 0,
                        'TO_USER_ID' => $userId,
                        'NOTIFY_TYPE' => IM_NOTIFY_SYSTEM,
                        'NOTIFY_MESSAGE' => $message,
                        'NOTIFY_MODULE' => 'itbizon.finance',
                        'NOTIFY_EVENT' => $notifyEvent
                    ]);
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @return bool
     */
    public static function isStockEnabled() {
        return (defined('ITB_FIN_STOCK_LOGIC_ON') && ITB_FIN_STOCK_LOGIC_ON === true);
    }
}
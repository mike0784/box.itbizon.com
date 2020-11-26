<?php

namespace Itbizon\Basis\Utils;

class HelperActivityReport
{
    /**
     * @param array $filterData
     * @return array
     */
    public static function prepareFilterUsers(array $filterData): array
    {
        $filter = ['=ACTIVE' => 'Y'];
        if (isset($filterData['USER_ID']) && !empty($filterData['USER_ID'])) {
            $filterUsersIds = [];
            foreach ($filterData['USER_ID'] as $id) {
                $filterUsersIds[] = intval(str_replace('U', '', $id));
            }
            $filter = array_merge($filter, ['=ID' => $filterUsersIds]);
        }
        return $filter;
    }

    /**
     * @param array $userIds
     * @param string $customFiled
     * @param array $filterData
     * @return array
     */
    public static function prepareFilterDealOrLead(array $userIds, string $customFiled, array $filterData): array
    {
        $filter = ['=ASSIGNED_BY_ID' => $userIds];
        if ($filterData) {
            $filterPeriodLead = [];
            foreach ($filterData as $key => $value) {
                $filterPeriodLead[str_replace('PERIOD', $customFiled, $key)] = $value;
            }
            $filter = array_merge($filter, $filterPeriodLead);
        }

        return $filter;
    }
}
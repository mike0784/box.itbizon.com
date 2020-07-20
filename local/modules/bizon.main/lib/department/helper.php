<?php

namespace Bizon\Main\Department;

use \Bitrix\Main\UserTable;
use \Bitrix\Main\DB\Exception;
use \Bitrix\Main\Loader;
use \Bizon\Main\Department\Model\DepartmentHistoryTable;
use \Bizon\Main\Log;
use \Bizon\Main\Utils\TimeSelector;
use \CIntranetUtils;
use \CModule;
use \COption;
use \CIBlockSection;

class Helper
{
    /**
     * @param $departmentId
     * @param $date
     * @return int
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getDepartmentByDate($departmentId, $date)
    {
        $selector = new TimeSelector();
        $list = DepartmentHistoryTable::getList([
            'filter'=>[
                '=DEPARTMENT_ID'=>$departmentId
            ],
            'select'=>[
                '*',
                'STRUCTURE_HISTORIES',
            ]
        ]);
        while($object = $list->fetchObject())
            $selector->Add($object->getChangeDate()->getTimestamp(), $object);
        
        return $selector->Get($date->getTimestamp());
    }
    
    /**
     * @param int $departmentId
     */
    public static function saveHistoryDepartment(int $departmentId)
    {
        try {
            $structure = self::getStructureDepartments();
            $department = $structure[$departmentId];
            
//            $log = new Log('department_change');
//            $log->Add($department['ID'].':');
//            $log->Add($department);
//            $log->Add('--------');
            
            if (!empty($department))
            {
                $employees = $department['EMPLOYEES'];

                $history = new DepartmentHistory();
                $history->setDepartmentId($departmentId);

                if ($department['UF_HEAD'] != 0)
                    $history->addToStructureHistories(self::getStructure($department['UF_HEAD'], 'Y'));
                foreach ($employees as $employeeId)
                    $history->addToStructureHistories(self::getStructure($employeeId));
                $history->save();
            }
            else
                throw new Exception('Департамент не был найден ID = ' . $departmentId);
        } catch (\Exception $e) {
            $log = new Log('save_history_department');
            $log->Add('Ошибка ' . $e->getMessage(), Log::LEVEL_ERROR);
        }
    }

    /**
     * @param        $employeeId
     * @param string $isChief
     * @return StructureHistory
     */
    private static function getStructure($employeeId, $isChief = 'N')
    {
        $structureHistory = new StructureHistory();
        $structureHistory->setUserId($employeeId);
        $structureHistory->setIsChief($isChief);
        return $structureHistory;
    }

    /**
     * @throws \Bitrix\Main\LoaderException
     * @throws \Exception
     */
    public static function initial()
    {
        if (!Loader::includeModule('intranet'))
            throw new \Exception('Error load module intranet');
        $data = CIntranetUtils::GetStructure()['DATA'];
        foreach ($data as $depId => $department)
            self::saveHistoryDepartment($depId);
    }

    /**
     * @param $userId
     * @param $departments
     * @param $eventName
     */
    public static function addUser($userId, $departments, $eventName)
    {
        $depList = implode(',', $departments);
        self::save($departments);
        $log = new Log('department_change');
        $log->Add($eventName . ' -> У нового сотрудника # ' . $userId . ' изменился отдел на : ' . $depList);
    }

    /**
     * @param $arFields
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function beforeUserUpdate(&$arFields)
    {
        $list = UserTable::getList([
            'filter' => ['=ID' => $arFields['ID']],
            'select' => ['UF_DEPARTMENT', 'ACTIVE'],
        ])->fetch();

        $arFields['OLD_DEPARTMENT'] = $list['UF_DEPARTMENT'];
        $arFields['OLD_ACTIVE'] = $list['ACTIVE'];

        if (isset($arFields['UF_DEPARTMENT'])) {
            $before = implode(';', $list['UF_DEPARTMENT']);
            $after = implode(';', $arFields['UF_DEPARTMENT']);

            // Если у сотрудника изменился отдел
            if ($before !== $after)
                $arFields['DEPARTMENT_IS_CHANGE'] = true;
        }
    }

    /**
     * @param $arFields
     * @throws \Bitrix\Main\LoaderException
     */
    public static function afterUserUpdate(&$arFields)
    {
        if (Loader::includeModule('bizon.main')) {
            $log = new Log('department_change');
            $depOld = implode(',', $arFields['OLD_DEPARTMENT']);
            $depNew = implode(',', $arFields['UF_DEPARTMENT']);

            // Если у сотрудника изменился отдел
            if ($arFields['DEPARTMENT_IS_CHANGE']) {
                $targetDep = array_merge(array_diff($arFields['OLD_DEPARTMENT'], $arFields['UF_DEPARTMENT']), array_diff($arFields['UF_DEPARTMENT'], $arFields['OLD_DEPARTMENT']));

                self::save($targetDep);

                $log->Add('У сотрудника ' . $arFields['ID'] . ' изменился отдел с: ' . $depOld . ' на: ' . $depNew);
                unset($arFields['DEPARTMENT_IS_CHANGE']);
            }
            // Если у сотрудника изменился ACTIVE (увольнение)
            if (isset($arFields['ACTIVE']) && $arFields['OLD_ACTIVE'] != $arFields['ACTIVE']) {
                self::save($arFields['OLD_DEPARTMENT']);

                if ($arFields['ACTIVE'] == 'Y')
                    $log->Add('Сотрудник ' . $arFields['ID'] . ' добавлен в отдел: ' . $depOld);
                else
                    $log->Add('Сотрудник ' . $arFields['ID'] . ' уволен из отдела: ' . $depOld);
            }
        }
    }
    
    /**
     * @param $departments
     * @throws \Bitrix\Main\LoaderException
     * @throws \Exception
     */
    private static function save($departments)
    {
        foreach ($departments as $department)
            self::saveHistoryDepartment($department);
    }
    
    /**
     * @return array
     */
    public static function getStructureDepartments()
    {
        global $DB;
        $departments = [];

        $ibDept = COption::GetOptionInt('intranet', 'iblock_structure', false);
        $dbRes = CIBlockSection::GetList(
            array("LEFT_MARGIN" => "ASC"),
            array('IBLOCK_ID' => $ibDept, 'ACTIVE' => 'Y'),
            false,
            array('ID', 'UF_HEAD')
        );
        while ($item = $dbRes->Fetch()) {
            $departments[$item['ID']] = [
                'ID' => $item['ID'],
                'UF_HEAD' => null,
                'EMPLOYEES' => []
            ];

            if ($item['UF_HEAD'])
                $departments[$item['ID']]['UF_HEAD'] = $item['UF_HEAD'];
        }

        $dbRes = $DB->query("
				SELECT BUF.VALUE_ID AS ID, BUF.VALUE_INT AS UF_DEPARTMENT
					FROM b_utm_user BUF
						LEFT JOIN b_user_field UF ON BUF.FIELD_ID = UF.ID
						LEFT JOIN b_user U ON BUF.VALUE_ID = U.ID
					WHERE ( U.ACTIVE = 'Y' )
						AND ( UF.FIELD_NAME = 'UF_DEPARTMENT' )
						AND ( BUF.VALUE_INT IS NOT NULL AND BUF.VALUE_INT <> 0 )
			");
        while ($item = $dbRes->Fetch()) {
            $departments[$item['UF_DEPARTMENT']]['EMPLOYEES'][] = $item['ID'];
        }


        return $departments;
    }
}
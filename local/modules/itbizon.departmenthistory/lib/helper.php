<?php


namespace Itbizon\DepartmentHistory;

use Bitrix\Main\DB\Exception;
use Bitrix\Main\Loader;
use CIntranetUtils;

class Helper
{
    public static function saveHistoryDepartment(int $departmentId)
    {
        try {
            if (!Loader::includeModule('itbizon.departmenthistory'))
                throw new \Exception('Ошибка подключения модуля itbizon.departmenthistory');
            if (!Loader::includeModule('intranet'))
                throw new \Exception('Error load module intranet');
            if (!Loader::includeModule('bizon.main'))
                throw new \Exception('Error load module intranet');

            $structure = CIntranetUtils::GetStructure();
            $department = $structure['DATA'][$departmentId];

            if (!empty($department)) {
                $employees = $department['EMPLOYEES'];
                array_push($employees, $department['UF_HEAD']);
                $employees = array_unique($employees);

                $history = new \Itbizon\DepartmentHistory\DepartmentHistory();
                $history->setDepartmentId($departmentId);

                foreach ($employees as $employeeId) {
                    $structureHistory = new \Itbizon\DepartmentHistory\StructureHistory();
                    $structureHistory->setUserId($employeeId);
                    $isChief = ($employeeId == $department['UF_HEAD']) ? 'Y' : 'N';
                    $structureHistory->setIsChief($isChief);
                    $history->addToStructureHistories($structureHistory);
                }
                $history->save();
            }
            throw new Exception('Департамент не был найден ID = ' . $departmentId);
        } catch (\Exception $e) {
            $log = new \Bizon\Main\Log('save_history_department');
            $log->Add('Ошибка ' . $e->getMessage(), \Bizon\Main\Log::LEVEL_ERROR);
        }
    }
}
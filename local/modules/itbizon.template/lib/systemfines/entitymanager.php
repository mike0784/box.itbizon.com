<?php

namespace Itbizon\Template\SystemFines;

use Bitrix\Main\DB\Exception;

class EntityManager
{
    public static function getRepository(String $className)
    {
        $repositoryClass = '\\' . str_replace("Entities", "Repositories", $className) . 'Repository';
        return new $repositoryClass();
    }

    public function findById(int $id)
    {
        $entityTable = $this->getTableClass(get_called_class());
        $entity = $this->getEntityClass(get_called_class());
        $fineTable = $entityTable::getByPrimary($id)->fetch();

        try {
            return $this->combineEntityWithData($fineTable, $entity);
        } catch (Exception $e) {
            exit($e->getMessage());
        }
    }

    public function findAll()
    {
        $objects = [];
        $entityTable = $this->getTableClass(get_called_class());
        $entity = $this->getEntityClass(get_called_class());

        $finesTable = $entityTable::getList()->fetchAll();
        try {
            foreach ($finesTable as $fine) {
                $objects[] = $this->combineEntityWithData($fine, $entity);
            }
            return $objects;
        } catch (Exception $e) {
            exit($e->getMessage());
        }
    }

    private function combineEntityWithData($dataTable, string $entityClass)
    {
        if (!empty($dataTable)) {
            $entity = new $entityClass($dataTable["ID"]);

            foreach ($dataTable as $key => $value) {

                if ($key !== 'ID') {
                    $val = strtolower($key);
                    $values = explode('_', $val);

                    if (($key = array_search('id', $values)) !== false) {
                        unset($values[$key]);
                    }
                    $values = array_map(function ($item) {
                        return ucfirst($item);
                    }, $values);

                    $setter = 'set' . implode('', $values);
                    $entity->$setter($value);
                }
            }
            return $entity;
        }
        throw new Exception("Not found data in entity manager method combineEntityWithData");
    }

    private function getTableClass(string $class)
    {
        $model = '\\' . str_replace("Repositories", "Model", $class);

        return preg_replace('/Repository/', 'Table', $model);
    }

    private function getEntityClass(string $class)
    {
        $entity = '\\' . str_replace("Repositories", "Entities", $class);

        return preg_replace('/Repository/', '', $entity);
    }
}

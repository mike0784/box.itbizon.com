<?php

use Bitrix\Main\Loader;
use Itbizon\Service\Component\Form\Field;
use Itbizon\Service\Component\Simple;

if(!Loader::includeModule('itbizon.service')) {
    throw new Exception('Ошибка подключения модуля itbizon.service');
}

/**
 * Class CITBServiceFormFieldset
 */
class CITBServiceFormFieldset extends Simple
{
    protected $fields = [];

    /**
     * @return mixed|void|null
     */
    public function executeComponent()
    {
        try {
            $fields = is_array($this->arParams['FIELDS']) ? $this->arParams['FIELDS'] : [];
            foreach ($fields as $index => $field) {
                if (is_array($field)) {
                    $this->fields[] = Field::create($field);
                } else {
                    if (is_object($field) && is_subclass_of($field, Field::class)) {
                        $this->fields[] = $field;
                    } else {
                        throw new Exception('Ошибка иницифализации поля # ' . $index . 'из параметров: ' . print_r($field, true));
                    }
                }
            }
            if (empty($this->fields)) {
                throw new Exception('На форму недобавлено ни одного поля');
            }
        } catch (Exception $e) {
            $this->addError($e->getMessage());
        }
        $this->IncludeComponentTemplate();
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}

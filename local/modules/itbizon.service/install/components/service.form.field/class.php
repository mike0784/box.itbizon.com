<?php

use Bitrix\Main\Loader;
use Itbizon\Service\Component\Form\Field;
use Itbizon\Service\Component\Simple;

if(!Loader::includeModule('itbizon.service')) {
    throw new Exception('Ошибка подключения модуля itbizon.service');
}

/**
 * Class CITBServiceFormField
 */
class CITBServiceFormField extends Simple
{
    protected $field;

    /**
     * @return mixed|void|null
     */
    public function executeComponent()
    {
        $template = '.default';
        try {
            if (is_array($this->arParams['FIELD'])) {
                $this->field = Field::create($this->arParams['FIELD']);
            } else {
                if (is_object($this->arParams['FIELD']) && is_subclass_of($this->arParams['FIELD'], Field::class)) {
                    $this->field = $this->arParams['FIELD'];
                }
            }
            if (!$this->getField()) {
                throw new Exception('Ошибка и нициализации поля из параметров ' . print_r($this->arParams['FIELD'], true));
            }
            $template = $this->getField()->getType();
        } catch (Exception $e) {
            $this->addError($e->getMessage());
        }
        $this->setTemplateName($template);
        $this->IncludeComponentTemplate();
    }

    /**
     * @return Field|null
     */
    public function getField(): ?Field
    {
        return $this->field;
    }
}

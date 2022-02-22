<?php

use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Loader;
use Itbizon\Service\Activities\Activity;
use Itbizon\Service\Activities\Field;

Loader::includeModule('itbizon.service');

class CBPItbizonTest extends Activity
{
    /**
     * @return array
     */
    protected static function getInputFields(): array
    {
        return [
            new Field(
                'param1',
                'Параметр 1',
                FieldType::INT,
                true
            ),
            new Field(
                'param2',
                'Параметр 2',
                FieldType::INT,
                true
            ),
        ];
    }

    /**
     * @return array
     */
    protected static function getOutputFields(): array
    {
        return [
            new Field(
                'param3',
                'Параметр 3',
                FieldType::INT,
                true
            ),
        ];
    }

    /**
     * @return string
     */
    protected static function getActivityPath(): string
    {
        return __FILE__;
    }

    /**
     * @return mixed
     */
    public function Execute()
    {
        try {
            $this->param3 = $this->param1 + $this->param2;
        } catch (Exception $e) {
            $this->WriteToTrackingService($e->getMessage());
        }
        return \CBPActivityExecutionStatus::Closed;
    }
}
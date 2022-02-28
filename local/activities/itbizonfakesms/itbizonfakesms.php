<?php

use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Loader;
use Itbizon\Service\Activities\Activity;
use Itbizon\Service\Activities\Field;

Loader::includeModule('itbizon.service');
Loader::includeModule('messageservice');

use Bitrix\MessageService\Internal\Entity\MessageTable;
use Bitrix\MessageService\MessageType;
use Bitrix\MessageService\MessageStatus;
use Bitrix\MessageService\Sender\SmsManager;

class CBPItbizonFakeSms extends Activity
{
    /**
     * @return array
     */
    protected static function getInputFields(): array
    {
        return [
            new Field(
                'activityProviderID',
                'ID провайдера (например, "CRM_SMS")',
                FieldType::STRING,
                true
            ),
            new Field(
                'senderId',
                'ID отправителя (например, "itbizon.smsc")',
                FieldType::STRING,
                true
            ),
            new Field(
                'messageFrom',
                'От кого (например, "bankirromsk")',
                FieldType::STRING,
                true
            ),
            new Field(
                'authorID',
                'ID автора (например, id текущего пользователя)',
                FieldType::STRING,
                true
            ),
            new Field(
                'ownerID',
                'ID сделки',
                FieldType::INT,
                true
            ),
             new Field(
                 'messageBody',
                 'Текст сообщения',
                 FieldType::STRING,
                 true
             ),
             new Field(
                  'messageTo',
                  'Кому (номер телефона)',
                  FieldType::STRING,
                  true
              ),
        ];
    }

    /**
     * @return array
     */
    protected static function getOutputFields(): array
    {
        return [];
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

            $ownerTypeID = CCrmOwnerType::Deal;
            //$authorID = CCrmSecurityHelper::GetCurrentUserID();


            $bindings = array(array(
            		'OWNER_TYPE_ID' => $ownerTypeID,
            		'OWNER_ID' => $this->ownerID
            	));

            $additionalFields = array(
            		'ACTIVITY_PROVIDER_TYPE_ID' => $this->activityProviderID,
            		'ENTITY_TYPE' => CCrmOwnerType::ResolveName($ownerTypeID),
            		'ENTITY_TYPE_ID' => $ownerTypeID,
            		'ENTITY_ID' => $this->ownerID,
            		'BINDINGS' => $bindings,
            		'ACTIVITY_AUTHOR_ID' => $this->authorID,
            		'ACTIVITY_DESCRIPTION' => $this->messageBody,
            		'MESSAGE_TO' => $this->messageTo,
            );

            //Создадим успешно отправленное сообщение
            $message = MessageTable::add(array(
                         'TYPE' => MessageType::SMS,
                         'SENDER_ID' => $this->senderId,
                         'AUTHOR_ID' => $this->authorID,
                         'MESSAGE_FROM' => $this->messageFrom,
                         'MESSAGE_TO' => $this->messageTo,
                         'MESSAGE_HEADERS' => null,
                         'MESSAGE_BODY' => $this->messageBody,
            			 'SUCCESS_EXEC' => 'Y',
            			 'STATUS_ID' => MessageStatus::SENT,
            			 'EXTERNAL_ID' => 0
            ));

            //Событие, которое на таймлайн добавит запись
            (new Bitrix\Main\Event(
            	'messageservice',
            	SmsManager::ON_MESSAGE_SUCCESSFULLY_SENT_EVENT,
            	[
            		'ID' => $message->getId(),
            		'ADDITIONAL_FIELDS' => $additionalFields,
            	]
            ))->send();
        } catch (Exception $e) {
            $this->WriteToTrackingService($e->getMessage());
        }
        return \CBPActivityExecutionStatus::Closed;
    }
}
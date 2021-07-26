<?php


namespace Itbizon\Service\Mail\Model;


use Bitrix\Main\ArgumentException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\ORM\Query\Result;
use Bitrix\Main\SystemException;
use Itbizon\Service\Mail\MailDomain;

/**
 * Class SMTPServerTable
 * @package Itbizon\Service\Mail\Model
 */
class MailDomainTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'itb_service_mail_domain';
    }

    /**
     * @return EntityObject|string
     */
    public static function getObjectClass()
    {
        return MailDomain::class;
    }

    /**
     * @return string|null
     */
    public static function getTitle()
    {
        return Loc::getMessage('ITB_SERV_MAIL_DOMAIN_ENTITY_TITLE');
    }

    /**
     * @return array
     * @throws SystemException
     */
    public static function getMap()
    {
        return [
            new Fields\IntegerField(
                'ID',
                [
                    'title' => Loc::getMessage('ITB_SERV_MAIL_DOMAIN_TABLE_ID'),
                    'primary' => true,
                    'autocomplete' => true,
                ]
            ),
            new Fields\BooleanField(
                'ACTIVE',
                [
                    'title' => Loc::getMessage('ITB_SERV_MAIL_DOMAIN_TABLE_ACTIVE'),
                    'required' => true,
                    'default_value' => 'N',
                    'values' => ['N', 'Y']
                ]
            ),
            new Fields\StringField(
                'DOMAIN',
                [
                    'title' => Loc::getMessage('ITB_SERV_MAIL_DOMAIN_TABLE_DOMAIN'),
                    'required' => true,
                    'validation' => function() {
                        return array(
                            new Fields\Validators\UniqueValidator(Loc::getMessage('ITB_SERV_MAIL_DOMAIN_TABLE_DOMAIN_ERR_NOT_UNIQUE')),
                        );
                    }
                ]
            ),
            new Fields\StringField(
                'SERVER',
                [
                    'title' => Loc::getMessage('ITB_SERV_MAIL_DOMAIN_TABLE_SERVER'),
                    'required' => true,
                ]
            ),
            new Fields\IntegerField(
                'PORT',
                [
                    'title' => Loc::getMessage('ITB_SERV_MAIL_DOMAIN_TABLE_PORT'),
                    'required' => true,
                    'validation' => function() {
                        return array(
                            new Fields\Validators\RangeValidator(1, 65535, [
                                'MIN' => Loc::getMessage('ITB_SERV_MAIL_DOMAIN_TABLE_PORT_ERR_MIN'),
                                'MAX' => Loc::getMessage('ITB_SERV_MAIL_DOMAIN_TABLE_PORT_ERR_MAX')
                            ]),
                        );
                    }
                ]
            ),
        ];
    }

    /**
     * @param string $email
     * @return string
     */
    public static function parseDomain(string $email)
    {
        $parts = explode('@', $email);
        if(isset($parts[1])) {
            return trim($parts[1]);
        } else {
            return '';
        }
    }

    /**
     * @param string $email
     * @return Result|EO_MailDomain_Result
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getByAddress(string $email)
    {
        $domain = self::parseDomain($email);
        return self::getList([
            'filter' => [
                'DOMAIN' => $domain,
                'ACTIVE' => 'Y'
            ],
            'limit' => 1
        ]);
    }
}
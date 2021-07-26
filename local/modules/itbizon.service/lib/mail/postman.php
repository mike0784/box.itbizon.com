<?php


namespace Itbizon\Service\Mail;


use Bitrix\Mail\MailboxTable;
use Bitrix\Mail\MailMessageUidTable;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Mail\Context;
use Exception;
use Itbizon\Service\Log;
use Itbizon\Service\Mail\Model\MailDomainTable;

/**
 * Class Postman
 * @package Itbizon\Service\Mail
 */
class Postman
{
    /**
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param string $additionalHeaders
     * @param string $additionalParameters
     * @param Context|null $context
     * @return bool
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function send(string $to, string $subject, string $message, string $additionalHeaders = '', string $additionalParameters = '', Context $context = null): bool
    {
        $log = new Log('postman');
        $log->add('--------------------------------------------------------------------------------------------------');
        try {
            if(!Loader::includeModule('mail'))
                throw new Exception('Error load module mail');

            //Parse and encode headers
            $parsedHeaders = self::parseHeaders($additionalHeaders);
            //$log->add('Headers: '.print_r($parsedHeaders, true));


            //Try to get from address
            $fromAddress = [];
            $toAddresses = [];
            $ccAddresses = [];
            $bccAddresses = [];
            $mailBox = null;

            //Get from address from context
            if($context) {
                $smtp = $context->getSmtp();
                if($smtp) {
                    $log->add('Host: '.$smtp->getHost());
                    $log->add('Protocol: '.$smtp->getProtocol());
                    $log->add('Port: '.$smtp->getPort());
                    $log->add('From: '.$smtp->getFrom());
                    $log->add('Login: '.$smtp->getLogin());
                    $log->add('Password: '.$smtp->getPassword());
                }
            }

            //Get from address by message id
            if(empty($fromAddress)) {
                $messageUid = strval($parsedHeaders['X-Bitrix-Mail-Message-UID']);
                if(!empty($messageUid)) {
                    $log->add('Parse address by message uid: '.$messageUid);
                    $messageUidObject = MailMessageUidTable::getList([
                        'select' => ['ID', 'MAILBOX', 'MESSAGE', 'MESSAGE_ID'],
                        'filter' => ['=ID' => $messageUid],
                        'limit' => 1,
                    ])->fetchObject();
                    if($messageUidObject) {
                        $mailBox = $messageUidObject->getMailbox();

                        $messageObject = $messageUidObject->getMessage();
                        if($messageObject) {
                            //From
                            $fromAddress = Address::parseString($messageObject->getFieldFrom());
                            $log->add('From address: '.Address::array2String($fromAddress));

                            //Get to addresses
                            $toAddresses = Address::parseString($messageObject->getFieldTo());
                            $log->add('To address: '.Address::array2String($toAddresses));

                            //Get cc addresses
                            $ccAddresses = Address::parseString($messageObject->getFieldCc());
                            $log->add('Cc address: '.Address::array2String($ccAddresses));

                            //Get bcc addresses
                            $bccAddresses = Address::parseString($messageObject->getFieldBcc());
                            $log->add('Bcc address: '.Address::array2String($bccAddresses));
                        } else {
                            $log->add('No message', Log::LEVEL_WARN);
                        }
                    }
                }
            }

            //Get from address by headers pizdec nahuy blyat / Old variant
            if(empty($fromAddress)) {
                $log->add('Parse address from headers');
                $fromAddress = strval($parsedHeaders['From']);
                if(empty($fromAddress))
                    throw new Exception('Error get email from headers');
                $fromAddress = Address::parseString($fromAddress);
                $log->add('From address: '.Address::array2String($fromAddress));

                //Get mailbox (username & password)
                $mailBox = MailBoxTable::getList([
                    'filter' => [
                        'EMAIL' => $fromAddress[0]->getAddress(),
                        'ACTIVE' => 'Y'
                    ],
                    'limit' => 1
                ])->fetchObject();
            }

            if(empty($toAddresses)) {
                $toAddresses = Address::parseString($to);
                $log->add('To address: '.Address::array2String($toAddresses));
            }

            //Cc to addresses
            if(empty($ccAddresses)) {
                if(!empty($parsedHeaders['Cc'])) {
                    $ccAddresses = Address::parseString($parsedHeaders['Cc']);
                }
            }

            //Bcc to addresses
            if(empty($bccAddresses)) {
                if(!empty($parsedHeaders['Bcc'])) {
                    $bccAddresses = Address::parseString($parsedHeaders['Bcc']);
                }
            }

            //Check mail box
            if(!$mailBox) {
                throw new Exception('Error find email settings for address '.$fromAddress[0]->getAddress());
            }
            $log->add('Send via mailbox:'.$mailBox->getId().' ('.$mailBox->getEmail().')');

            //Get domain (server & port)
            $mailDomain = MailDomainTable::getByAddress($fromAddress[0]->getAddress())->fetchObject();
            if(!$mailDomain) {
                throw new Exception('Error find domain settings for address '.$fromAddress[0]->getAddress());
            }
            $log->add('Send via '.$mailDomain->getServer().':'.$mailDomain->getPort());

            //Variant via PHP Mailer
            $mail = new MailerWrapper;
            $mail->customContentType = $parsedHeaders['Content-Type']; //Its a hack
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPDebug = 0;
            $mail->Host = 'ssl://'.$mailDomain->getServer();
            $mail->Port = $mailDomain->getPort();
            $mail->Username = $mailBox->getLogin();
            $mail->Password = $mailBox->getPassword();
            $mail->setFrom($fromAddress[0]->getAddress(), $fromAddress[0]->getName());
            foreach($toAddresses as $address) {
                $mail->addAddress($address->getAddress(), $address->getName());
            }
            foreach($ccAddresses as $address) {
                $mail->addCC($address->getAddress(), $address->getName());
            }
            foreach($bccAddresses as $address) {
                $mail->addBCC($address->getAddress(), $address->getName());
            }
            $mail->Subject = $subject;
            $mail->ContentType = MailerWrapper::CONTENT_TYPE_MULTIPART_MIXED;
            $mail->Encoding = MailerWrapper::ENCODING_8BIT;
            $mail->CharSet = MailerWrapper::CHARSET_UTF8;
            $mail->MessageDate = $parsedHeaders['Date'];
            $mail->Body = $message;

            if(!$mail->send()) {
                throw new Exception($mail->ErrorInfo);
            }
            $log->add('Send complete', Log::LEVEL_OK);
            return true;
        } catch (Exception $e) {
            $log->add($e->getMessage(), Log::LEVEL_ERROR);
            if(Option::get('itbizon.service', 'mail_send_via_default', 'Y') === 'Y') {
                $log->add('Send via default method');
                return @mail($to, $subject, $message, $additionalHeaders, $additionalParameters);
            } else {
                return false;
            }
        }
    }

    /**
     * @deprecated use Itbizon/Service/Mail/Address class
     * @param string $to
     * @return array
     */
    public static function parseAddress(string $to): array
    {
        $addresses = explode(',', $to);
        foreach ($addresses as &$address) {
            $address = trim($address);
        }
        return $addresses;
    }

    /**
     * @param string $headers
     * @param bool $encode
     * @return array
     */
    public static function parseHeaders(string $headers, bool $encode = true): array
    {
        $out = [];
        $headers = self::parseRawHeaders($headers);
        foreach($headers as $header) {
            $pos = mb_strpos($header, ':');
            if($pos > 0) {
                $key = trim(mb_substr($header, 0, $pos));
                $value = trim(mb_substr($header, $pos+1));
                if($encode) {
                    $out[$key] = iconv_mime_decode($value);
                } else {
                    $out[$key] = $value;
                }
            }
        }
        return $out;
    }

    /**
     * @param string $headers
     * @return array
     */
    public static function parseRawHeaders(string $headers): array
    {
        return explode(PHP_EOL, $headers);
    }
}
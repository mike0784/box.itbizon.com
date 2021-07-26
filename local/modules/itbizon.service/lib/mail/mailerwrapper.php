<?php


namespace Itbizon\Service\Mail;


use PHPMailer\PHPMailer\PHPMailer;

/**
 * Class MailerWrapper
 * @package Itbizon\Service\Mail
 */
class MailerWrapper extends PHPMailer
{
    public $customContentType = '';

    /**
     * Get the message MIME type headers.
     *
     * @return string
     */
    public function getMailMIME()
    {
        if(empty($this->customContentType)) {
            return parent::getMailMIME();
        } else {
            $result = '';
            $ismultipart = true;

            $result .= $this->textLine('Content-Type: ' . $this->customContentType);

            //RFC1341 part 5 says 7bit is assumed if not specified
            if (static::ENCODING_7BIT !== $this->Encoding) {
                //RFC 2045 section 6.4 says multipart MIME parts may only use 7bit, 8bit or binary CTE
                if ($ismultipart) {
                    if (static::ENCODING_8BIT === $this->Encoding) {
                        $result .= $this->headerLine('Content-Transfer-Encoding', static::ENCODING_8BIT);
                    }
                    //The only remaining alternatives are quoted-printable and base64, which are both 7bit compatible
                } else {
                    $result .= $this->headerLine('Content-Transfer-Encoding', $this->Encoding);
                }
            }
            return $result;
        }
    }
}
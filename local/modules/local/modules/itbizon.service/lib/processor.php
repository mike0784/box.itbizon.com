<?php


namespace Itbizon\Service;


use Bitrix\Main\Localization\Loc;
use Exception;

Loc::loadMessages(__FILE__);

/**
 * Class Processor
 * @package Itbizon\Service
 */
class Processor
{
    const JSON = 1;

    protected $mode;

    /**
     * Processor constructor.
     * @param int $mode
     */
    public function __construct(int $mode = self::JSON)
    {
        $this->mode = $mode;
    }

    /**
     * @param callable $process
     */
    public function run(callable $process)
    {
        try {
            if($this->mode === self::JSON) {
                header('Content-Type: application/json');
            }

            call_user_func($process, $this);
        } catch(Exception $e) {
            $this->send(false, $e->getMessage(), []);
        }
        $this->send(false, Loc::getMessage('ITB_SERV_PROCESSOR_PROCESS_ERROR'), []);
    }

    /**
     * @param bool $state
     * @param string $message
     * @param array $data
     */
    public function send(bool $state, string $message, array $data = [])
    {
        if($this->mode === Processor::JSON) {
            http_response_code($state ? 200 : 500);
            echo json_encode([
                'state' => $state,
                'message' => $message,
                'data' => $data
            ]);
        }
        die();
    }

    /**
     * @return string
     */
    public static function getAjaxPath() : string
    {
        return '/local/modules/itbizon.service/tools/ajax.php';
    }
}
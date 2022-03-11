<?php


namespace Itbizon\Service;

use Bitrix\Main\Config\Option;
use CModule;
use Exception;

/**
 * Class Statistic
 * @package Itbizon\Service
 */
final class Statistic
{
    const URL = 'https://app2.itbizon.com/webhooks/monitoring/index.php';
    const CMD_TEST = 'test';
    const CMD_MONITOR = 'monitor';
    const CMD_ERROR = 'error';

    protected static $instance;
    protected $active;
    protected $key;
    protected $moduleVersion;
    protected $serverName;
    protected $lastError;
    protected $lastAnswer;

    /**
     * Statistic constructor.
     */
    private function __construct()
    {
        $this->active = (Option::get('itbizon.service', 'general_send_statistic') === 'Y');
        $this->key = Option::get('itbizon.service', 'general_send_statistic_key');
        $this->moduleVersion = (CModule::CreateModuleObject('itbizon.service'))->MODULE_VERSION;
        $this->serverName = Option::get('main', 'server_name');
        $this->lastError = '';
        $this->lastAnswer = '';
    }

    /**
     * @return Statistic
     */
    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param string $command
     * @param array $data
     * @return bool
     */
    public function send(string $command, array $data): bool
    {
        try {
            if ($this->active) {
                $curl = curl_init();
                $url = self::URL;
                $data = http_build_query([
                    'head' => [
                        'command' => $command,
                        'date' => date('c'),
                        'key' => $this->key,
                        'version' => $this->moduleVersion,
                        'domain' => $this->serverName
                    ],
                    'data' => $data
                ]);
                curl_setopt_array($curl, [
                    CURLOPT_POST => 1,
                    CURLOPT_HEADER => 0,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $url,
                    CURLOPT_POSTFIELDS => $data,
                ]);
                $this->lastAnswer = curl_exec($curl);
                if (curl_errno($curl) != CURLE_OK)
                    throw new Exception('CURL Error: ' . curl_error($curl));

                $this->lastAnswer = json_decode($this->lastAnswer, true);
                if (json_last_error() !== JSON_ERROR_NONE)
                    throw new Exception('JSON Error: ' . json_last_error_msg());

                return true;
            } else {
                $this->lastError = 'Statistic send off';
            }
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
        }
        return false;
    }

    /**
     * @return string
     */
    public function test()
    {
        if ($this->send(self::CMD_TEST, ['message' => 'Hello!']))
            return $this->lastAnswer;
        else
            return $this->lastError;
    }

    /**
     * @return string
     */
    public function getLastError(): string
    {
        return $this->lastError;
    }

    /**
     * @return mixed|string
     */
    public function getServerName()
    {
        return $this->serverName;
    }
}
<?php


namespace Itbizon\Finance\Utils;

/**
 * Class Money
 * @package Itbizon\Finance\Utils
 */
class Money
{
    const VIEW = 1;
    const XLSX = 2;

    /**
     * @param int $value
     * @return float
     */
    public static function fromBase(int $value): float
    {
        return round($value/100, 2);
    }

    /**
     * @param float $value
     * @return int
     */
    public static function toBase(float $value): int
    {
        return round($value*100);
    }

    /**
     * @param float $value
     * @param int $format
     * @return string
     */
    public static function format(float $value, int $format = self::VIEW): string
    {
        switch($format) {
            case self::XLSX:
                return number_format($value, 2, ',', '');
                break;
            default:
                return number_format($value, 2, ',', ' ');
        }
    }

    /**
     * @param int $value
     * @param int $format
     * @return string
     */
    public static function formatFromBase(int $value, int $format = self::VIEW): string
    {
        return self::format(self::fromBase($value), $format);
    }
}
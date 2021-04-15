<?php


namespace Itbizon\Finance;

/**
 * Class DistributionItem
 * @package Itbizon\Finance
 */
class DistributionItem
{
    protected $stock;
    protected $amount;

    public function __construct(Stock $stock, int $amount)
    {
        $this->stock = $stock;
        $this->amount = $amount;
    }

    /**
     * @return Stock
     */
    public function getStock(): Stock
    {
        return $this->stock;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }
}
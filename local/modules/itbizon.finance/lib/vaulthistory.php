<?php


namespace Itbizon\Finance;


class VaultHistory extends Model\EO_VaultHistory
{
    /**
     * @return string
     */
    public function getBalancePrint()
    {
        return sprintf('%.2f', $this->getBalance() / 100.);
    }
}
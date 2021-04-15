<?php


namespace Itbizon\Finance;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Exception;
use Itbizon\Finance\Model\OperationCategoryTable;
use Itbizon\Finance\Model\RequestTable;
use Itbizon\Finance\Model\StockTable;
use Itbizon\Finance\Model\VaultTable;


Loc::loadMessages(__FILE__);

/**
 * Class Stock
 * @package Itbizon\Finance
 */
class Stock extends Model\EO_Stock
{
    const INCOME  = -StockTable::STOCK_INCOME;
    const MARGIN  = -StockTable::STOCK_MARGIN;
    const CORRECT = -StockTable::STOCK_CORRECT_PROFIT;

    protected $virtualStock = false;
    protected $parentStock = null;
    protected $childrenStocks = [];
    protected $optionName = '';

    /**
     * @param int $id
     * @return static
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Exception
     */
    public static function createVirtualStockById(int $id): self
    {
        switch($id) {
            case self::INCOME:
                return self::createIncomeStock();
            case self::MARGIN:
                return self::createMarginStock();
            case self::CORRECT:
                return self::createCorrectProfitStock();
        }
        throw new Exception('Virtual stock with id '.$id.' not found!');
    }

    /**
     * @return static
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function createIncomeStock(): self
    {
        return self::createVirtualStock(
            self::INCOME,
            Loc::getMessage('ITB_FIN.STOCK.GROUP.INCOME'),
            'stockIncomePrc'
        );
    }

    /**
     * @return static
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function createMarginStock(): self
    {
        return self::createVirtualStock(
            self::MARGIN,
            Loc::getMessage('ITB_FIN.STOCK.GROUP.MARGIN'),
            'stockMarginPrc'
        );
    }

    /**
     * @return static
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function createCorrectProfitStock(): self
    {
        return self::createVirtualStock(
            self::CORRECT,
            Loc::getMessage('ITB_FIN.STOCK.GROUP.CORRECT_PROFIT'),
            'stockCorrectProfitPrc'
        );
    }

    /**
     * @param int $id
     * @param string $name
     * @param string $optionName
     * @return Stock
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function createVirtualStock(int $id, string $name, string $optionName): self
    {
        $percent = Option::get('itbizon.finance', $optionName, 0);

        $stock = new self();
        $stock->setId($id);
        $stock->setName($name);
        $stock->setPercent($percent);
        $stock->setOptionName($optionName);
        $stock->virtualStock = true;
        return $stock;
    }

    /**
     * @return bool
     */
    public function isVirtualStock(): bool
    {
        return $this->virtualStock;
    }

    /**
     * @return null|Stock
     */
    public function getParentStock(): ?self
    {
        return $this->parentStock;
    }

    /**
     * @return Stock[]
     */
    public function getChildrenStocks(): array
    {
        return $this->childrenStocks;
    }

    /**
     * @return string
     */
    public function getResponsibleName(): string
    {
        $responsible = $this->getResponsible();
        if($responsible) {
            return $responsible->getLastName().' '.$responsible->getName();
        }
        return '';
    }

    /**
     * @param int $count
     * @return int
     */
    public function getCount(int $count = 0): int
    {
        $count++;
        foreach($this->getChildrenStocks() as $stock) {
            $stock->getCount($count);
        }
        return $count;
    }

    /**
     * @return string
     */
    public function getResponsibleUrl(): string
    {
        return '/company/personal/user/'.$this->getResponsibleId().'/';
    }

    /**
     * @return Vault|null
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getVault(): ?Vault
    {
        return VaultTable::getById($this->getId())->fetchObject();
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return '/finance/stock/edit/' . $this->getId() . '/';
    }

    /**
     * @param bool $all
     * @return int
     */
    public function getBalance(bool $all = false)
    {
        if($this->isVirtualStock()) {
            $balance = 0;
            foreach($this->getChildrenStocks() as $stock) {
                if($all) {
                    $balance += $stock->getBalance($all);
                } else {
                    if(!$stock->isVirtualStock()) {
                        $balance += $stock->getBalance();
                    }
                }
            }
            return $balance;
        } else {
            return parent::getBalance();
        }
    }

    /**
     * @param bool $all
     * @return int
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getLockBalance(bool $all = false): int
    {
        $balance = 0;
        if($this->isVirtualStock()) {
            foreach($this->getChildrenStocks() as $stock) {
                if($all) {
                    $balance += $stock->getLockBalance($all);
                } else {
                    if(!$stock->isVirtualStock()) {
                        $balance += $stock->getLockBalance();
                    }
                }
            }
        } else {
            $result  = RequestTable::getList([
                'select' => ['ID', 'STATUS', 'AMOUNT'],
                'filter' => ['=STOCK_ID' => $this->getId(), '=STATUS' => [RequestTable::STATUS_APPROVE, RequestTable::STATUS_CONFIRM]]
            ]);
            while($request = $result->fetchObject()) {
                $balance += $request->getAmount();
            }
        }
        return $balance;
    }

    /**
     * @param Stock $stock
     */
    public function addChild(self $stock)
    {
        $stock->parentStock = $this;
        $this->childrenStocks[$stock->getId()] = $stock;
    }

    /**
     * @return int
     */
    public function getChildPercent(): int
    {
        $childPercent = 0;
        foreach($this->getChildrenStocks() as $stock) {
            $childPercent += $stock->getPercent();
        }
        return $childPercent;
    }

    /**
     * @param int $percent
     * @return Stock
     * @throws ArgumentOutOfRangeException
     */
    public function setVirtualPercent(int $percent): self
    {
        if($this->isVirtualStock()) {
            if(!empty($this->getOptionName())) {
                Option::set('itbizon.finance', $this->getOptionName(), $percent);
                $this->setPercent($percent);
            }
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getOptionName(): string
    {
        return $this->optionName;
    }

    /**
     * @param string $optionName
     * @return Stock
     */
    public function setOptionName(string $optionName): self
    {
        $this->optionName = $optionName;
        return $this;
    }

    /**
     * @param Stock $tree
     * @return ErrorCollection
     */
    protected static function checkStockTree(Stock $tree): ErrorCollection
    {
        $errors = new ErrorCollection();
        $childrenStocks = $tree->getChildrenStocks();
        if(!empty($childrenStocks)) {
            $percent = $tree->getChildPercent();
            if($percent <> 10000) {
                $errors->add([new Error(str_replace(['#STOCK_NAME#', '#PERCENT#'], [$tree->getName(), $percent/100], Loc::getMessage('ITB_FIN.STOCK.ERROR.PERCENT_NOT_EQUAL_100')))]);
            }
            foreach($childrenStocks as $stock) {
                $errors->add(self::checkStockTree($stock)->getValues());
            }
        }
        return $errors;
    }

    /**
     * @return ErrorCollection
     */
    public static function check(): ErrorCollection
    {
        $errors = new ErrorCollection();
        try {
            $tree = StockTable::getTree();
            $errors->add(self::checkStockTree($tree)->getValues());

            $options = Option::getForModule('itbizon.finance');
            $reserveStockId = intval($options['reserveStockId']);
            $reserveStock = StockTable::getById($reserveStockId)->fetchObject();
            if(!$reserveStock) {
                $errors->add([new Error(Loc::getMessage('ITB_FIN.STOCK.ERROR.RESERVE_STOCK_NOT_FOUND'))]);
            }

            $incomeCategoryId = intval($options['incomeCategoryId']);
            $incomeCategory = OperationCategoryTable::getById($incomeCategoryId)->fetchObject();
            if(!$incomeCategory || !$incomeCategory->getAllowIncome()) {
                $errors->add([new Error(Loc::getMessage('ITB_FIN.STOCK.ERROR.INVALID_INCOME_CATEGORY'))]);
            }
        } catch(Exception $e) {
            $errors->add([new Error($e->getMessage())]);
        }
        return $errors;
    }

    /**
     * @param int $amount
     * @param array $data
     * @return int
     * @throws Exception
     */
    public function getDistributeData(int $amount, array &$data = []): int
    {
        if($amount < 0) {
            throw new Exception('Сумма к распределению меньше 0'); //TODO LANG
        }
        if(!empty($this->getChildrenStocks())) {
            $childAmount = 0;
            foreach($this->getChildrenStocks() as $stock) {
                if(!empty($stock->getChildrenStocks())) {
                    $childAmount += $stock->getDistributeData($amount - $childAmount, $data);
                } else {
                    $childAmount += $stock->getDistributeData($amount, $data);
                }
            }
            return $childAmount;
        } else {
            $myAmount = intval($amount/100 * $this->getPercent()/100);
            if($myAmount > $amount) {
                $myAmount = $amount;
            }
            $data[] = new DistributionItem($this, $myAmount);
            $amount -= $myAmount;
            return $myAmount;
        }
    }
}
<?php


namespace Itbizon\Service\Component;


use Bitrix\Main\Grid;
use Bitrix\Main\UI;
use Exception;

/**
 * Class GridHelper
 * @package Itbizon\Service\Component
 */
class GridHelper
{
    protected $gridId;
    protected $filterId;
    protected $gridOptions;
    protected $filterOptions;
    protected $navigation;
    protected $filter = [];
    protected $columns = [];
    protected $rows = [];

    /**
     * GridHelper constructor.
     * @param string $gridId
     * @param string $filterId
     * @throws Exception
     */
    public function __construct(string $gridId, string $filterId = '')
    {
        if(empty($gridId))
            throw new Exception('Grid id can not be empty!');
        if(empty($filterId))
            $filterId = $gridId;
        $this->gridId = $gridId;
        $this->filterId = $filterId;
        $this->gridOptions = new Grid\Options($this->gridId);
        $this->filterOptions = new UI\Filter\Options($this->filterId);
        $this->navigation = new UI\PageNavigation($this->gridId);

        $navParams = $this->gridOptions->GetNavParams();
        $this->navigation->allowAllRecords(true)
            ->setPageSize($navParams['nPageSize'])
            ->initFromUri();
    }

    /**
     * Set filter array for filter
     * @param array $filter
     * @return $this
     */
    public function setFilter(array $filter): self
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * Add field to filter array for filter
     * @param array $field
     * @return $this
     */
    public function addFilter(array $field): self
    {
        $this->filter[] = $field;
        return $this;
    }

    /**
     * Set columns array for grid
     * @param array $columns
     * @return $this
     */
    public function setColumns(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Add column to column array for grid
     * @param array $column
     * @return $this
     */
    public function addColumn(array $column): self
    {
        $this->columns[] = $column;
        return $this;
    }

    /**
     * Set rows array for grid
     * @param array $rows
     * @return $this
     */
    public function setRows(array $rows): self
    {
        $this->rows = $rows;
        return $this;
    }

    /**
     * Add row to row array for grid
     * @param array $row
     * @return $this
     */
    public function addRow(array $row): self
    {
        $this->rows[] = $row;
        return $this;
    }

    /**
     * Return grid id
     * @return string
     */
    public function getGridId(): string
    {
        return $this->gridId;
    }

    /**
     * Return filter id
     * @return string
     */
    public function getFilterId(): string
    {
        return $this->filterId;
    }

    /**
     * Return grid option object
     * @return Grid\Options
     */
    public function getGridOptions(): Grid\Options
    {
        return $this->gridOptions;
    }

    /**
     * Return filter option object
     * @return UI\Filter\Options
     */
    public function getFilterOptions(): UI\Filter\Options
    {
        return $this->filterOptions;
    }

    /**
     * Return filter array
     * @return array
     */
    public function getFilter(): array
    {
        return $this->filter;
    }

    /**
     * Return columns array
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Return rows array
     * @return array
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * Return filter array from ORM 'filter'
     * @return array
     */
    public function getFilterData(): array
    {
        return $this->getFilterOptions()->getFilterLogic($this->getFilter());
    }

    /**
     * Return navigation object
     * @return UI\PageNavigation
     */
    public function getNavigation(): UI\PageNavigation
    {
        return $this->navigation;
    }

    /**
     * Return sort array fot ORM 'order'
     * @param array $default
     * @return array
     */
    public function getSort(array $default = ['sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]): array
    {
        return $this->getGridOptions()->getSorting($default)['sort'];
    }
}
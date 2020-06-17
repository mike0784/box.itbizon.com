<?php


namespace Itbizon\Template\Utils;


class ExcelService
{
    private $xlsx;

    public function __construct(string $filePath)
    {
        $this->xlsx = new SimpleXLSX($filePath);
        if (!$this->xlsx->success()) {
            throw new \Exception('xls error: ' . $this->xlsx->error());
        }
    }

    /**
     * @param string $cellName
     * @param string $cellLink
     * @return array
     */
    public function getDataTable(string $cellName, string $cellLink): array
    {
        $countRows = count($this->xlsx->rows());
        $data = [];

        for ($i = 1; $i <= $countRows; $i++) {
            $excelData = [];

            $name = !empty($this->xlsx->getCell(0, $cellName . $i)) ?
                $this->xlsx->getCell(0, $cellName . $i) : 'default';
            $link = $this->xlsx->getCell(0, $cellLink . $i);

            if (filter_var($link, FILTER_VALIDATE_URL)) {
                $excelData['ID'] = $i;
                $excelData['NAME'] = $name;
                $excelData['LINK'] = $link;
                $data[] = $excelData;
            }
        }
        return $data;
    }

    public static function charsExcelCell($end_column = '', $first_letters = '')
    {
        $columns = [];
        $length = strlen($end_column);
        $letters = range('A', 'Z');

        // Iterate over 26 letters.
        foreach ($letters as $letter) {
            // Paste the $first_letters before the next.
            $column = $first_letters . $letter;
            // Add the column to the final array.
            $columns[] = $column;
            // If it was the end column that was added, return the columns.
            if ($column == $end_column)
                return $columns;
        }

        // Add the column children.
        foreach ($columns as $column) {
            // Don't itterate if the $end_column was already set in a previous itteration.
            // Stop iterating if you've reached the maximum character length.
            if (!in_array($end_column, $columns) && strlen($column) < $length) {
                $new_columns = self::charsExcelCell($end_column, $column);
                // Merge the new columns which were created with the final columns array.
                $columns = array_merge($columns, $new_columns);
            }
        }

        return $columns;
    }
}
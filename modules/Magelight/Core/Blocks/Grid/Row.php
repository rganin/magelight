<?php

namespace Magelight\Core\Blocks\Grid;

use Magelight\Traits\TForgery;

/**
 * Class Row
 * @package Magelight\Core\Blocks\Grid
 *
 * @method static $this forge(array $rowData)
 */
class Row
{
    use TForgery;
    /**
     * @var array
     */
    protected $rowData = [];

    /**
     * FOrgery constructor
     *
     * @param array $rowData
     */
    public function __forge(array $rowData)
    {
        $this->rowData = $rowData;
    }

    /**
     * Get all row data
     *
     * @return array
     */
    public function getRowData()
    {
        return $this->rowData;
    }

    /**
     * Get Data for cell rendering from row (usually by column config)
     *
     * @param array $fields - array of row fields to be fetched. ['field1', 'alias_f2' => 'field_2', ...]
     * @return array
     */
    public function getCellData(array $fields)
    {
        $result = [];
        foreach ($this->rowData as $key => $rowCell) {
            // return data in result array under direct name
            if (in_array($key, $fields)) {
                $result[$key] = $rowCell;
            // return under alias
            } else if (isset($fields[$key])) {
                $result[$fields[$key]] = $rowCell;
            }
        }
        return $result;
    }
}

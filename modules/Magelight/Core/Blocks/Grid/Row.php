<?php
/**
 * Magelight
 *
 * NOTICE OF LICENSE
 *
 * This file is open source and it`s distribution is based on
 * Open Software License (OSL 3.0). You can obtain license text at
 * http://opensource.org/licenses/osl-3.0.php
 *
 * For any non license implied issues please contact rganin@gmail.com
 *
 * DISCLAIMER
 *
 * This file is a part of a framework. Please, do not modify it unless you discard
 * further updates.
 *
 * @version 1.0
 * @author Roman Ganin
 * @copyright Copyright (c) 2012-2015 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

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

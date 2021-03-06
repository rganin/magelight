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

namespace Magelight\Grid\Blocks\Grid;

use Magelight\Core\Blocks\Element;

/**
 * Class Cell
 * @package Magelight\Grid\Blocks\Grid
 *
 * @method static $this forge()
 * @property array $data - row data
 */
abstract class Cell extends Element
{
    /**
     * @var null|string
     */
    protected $tag = null;

    /**
     * Fields of row data array that are used to render cell data
     *
     * @var array
     */
    protected $useFields = [];

    /**
     * Reset renderer state
     *
     * @return $this
     */
    public function resetState()
    {
        return $this;
    }

    /**
     * Use following fields from row data on render
     *
     * @param array $fields - ['field1', 'alias_2' => 'field_2', 'field_3' ...]
     *
     * @return $this
     */
    public function useFields(array $fields = [])
    {
        $this->useFields = $fields;
        return $this;
    }

    /**
     * Get fields for use in row data render
     *
     * @return array
     */
    public function getUseFields()
    {
        return $this->useFields;
    }

    /**
     * Get first element of data array in cell renderer
     *
     * @param mixed $default
     * @return mixed|null
     */
    protected function getFirstDataArrayElement($default = null)
    {
        return !empty($this->data) ? $this->data[array_keys($this->data)[0]] : $default;
    }
}

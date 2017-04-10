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

use Magelight\Core\Blocks\Element;
use Magelight\Core\Blocks\Grid;
use Magelight\Traits\TForgery;

/**
 * Class Column
 * @package Magelight\Core\Blocks\Grid
 *
 * @method static $this forge()
 */
class Column
{
    use TForgery;

    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var bool|int
     */
    protected $visible = true;

    /**
     * @var bool|int
     */
    protected $sortable = true;

    /**
     * Field to be passed to renderer
     *
     * @var string[]
     */
    protected $fields;

    /**
     * @var Cell
     */
    protected $cellRenderer;

    /**
     * Forgery constructor.
     */
    public function __forge()
    {

    }

    /**
     * Set column grid
     *
     * @param Grid $grid
     * @return $this
     */
    public function setGrid(Grid $grid)
    {
        $this->grid = $grid;
        return $this;
    }

    /**
     * Set column title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set cell field
     *
     * @param string $rowFieldName - index of field in row assoc array
     * @param string|null $fieldNameAlias - alias of the field for cell renderer
     * @return Column
     */
    public function setCellField($rowFieldName, $fieldNameAlias = null)
    {
        return $this->setCellFields([$fieldNameAlias ?: $rowFieldName => $rowFieldName]);
    }

    /**
     * Set column fields for cell
     *
     * @param array $rowFieldNamesArray - ['alias' => 'field', 'field2', 'alias_2' => 'field_3']
     * @return $this
     */
    public function setCellFields(array $rowFieldNamesArray)
    {
        $this->fields = $rowFieldNamesArray;
        return $this;
    }

    /**
     * Set is column visible
     * todo: implement visibility setting and processing
     * @param bool $flag
     * @return $this
     */
    public function setVisible($flag = true)
    {
        $this->visible = (bool)$flag;
        return $this;
    }

    /**
     * Set is column sortable
     * todo: implement sortability setting and processing
     * @param bool $flag
     * @return $this
     */
    public function setSortable($flag = true)
    {
        $this->sortable = (bool)$flag;
        return $this;
    }

    /**
     * Get column title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get column fields
     *
     * @return \string[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get cache key
     *
     * @return string
     */
    public function getKey()
    {
        return implode(',', $this->fields);
    }

    /**
     * Set renderer for cell
     *
     * @param Cell|null $renderer
     *
     * @return $this
     */
    public function setCellRenderer(Cell $renderer = null)
    {
        $this->cellRenderer = $renderer;
        return $this;
    }

    /**
     * Get cell renderer
     *
     * @return Cell|Element
     */
    public function getCellRenderer()
    {
        if (!$this->cellRenderer) {
            return Grid\Cell\Plaintext::forge();
        }
        return $this->cellRenderer;
    }
}

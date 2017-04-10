<?php

namespace Magelight\Core\Blocks\Grid;

use Magelight\Core\Blocks\Element;

/**
 * Class Cell
 * @package Magelight\Core\Blocks\Grid
 *
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
     * Render row data in cell by cell config
     *
     * @param array $rowData
     * @return string
     */
    public function renderRowData(array $rowData)
    {
        foreach ($this->useFields as $alias => $field) {
            $this->data[is_string($alias) ? $alias : $field] = $rowData[$field];
        }
        return $this->toHtml();
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

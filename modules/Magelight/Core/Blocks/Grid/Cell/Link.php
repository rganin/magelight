<?php

namespace Magelight\Core\Blocks\Grid\Cell;

use Magelight\Core\Blocks\Grid\Cell;

/**
 * Class Link
 * @package Magelight\Core\Blocks\Grid\Cell
 *
 * @param string $match - a url match
 * @param string $text - link text to be used if not set textField index in row data
 */
class Link extends Cell
{
    protected $tag = 'a';

    /**
     * @var null|string
     */
    protected $match = null;

    /**
     * Add only params that are present in match mask to url
     *
     * @var bool
     */
    protected $matchOnlyMaskParams = true;

    /**
     * @var null|string
     */
    protected $textField = null;

    /**
     * @var array
     */
    protected $paramAliases = [];

    /**
     * @param string $match - URL match
     * @param bool $matchOnlyMaskParams - add only params that are present in mask to url
     * @return $this
     */
    public function setMatch($match, $matchOnlyMaskParams = true)
    {
        $this->match = $match;
        $this->matchOnlyMaskParams = $matchOnlyMaskParams;
        return $this;
    }

    /**
     * Set index in row ID that will be used for link text
     *
     * @param null|string $field
     * @return $this
     */
    public function setTextField($field = null)
    {
        if ($field) {
            $this->textField = $field;
        }
        return $this;
    }

    /**
     * Set row data aliases
     *
     * @param string $rowDataIndex
     * @param null|string $alias
     * @deprecated
     * @return $this
     */
    public function useRowDataAsParams($rowDataIndex, $alias = null)
    {
        $this->paramAliases[$rowDataIndex] = $alias ?: $rowDataIndex;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        if (isset($this->match)) {
            $this->setAttribute('href', $this->url($this->match, $this->data, null, $this->matchOnlyMaskParams));
        }
        $this->setContent($this->textField ? $this->data[$this->textField] : $this->text);
        return parent::toHtml();
    }
}

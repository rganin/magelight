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

namespace Magelight\Grid\Blocks\Grid\Cell;

use Magelight\Core\Blocks\Element;
use Magelight\Grid\Blocks\Grid\Cell;

/**
 * Class Image
 * @package Magelight\Grid\Blocks\Grid\Cell
 *
 * @method static $this forge()
 * @property string $alt
 */
class Image extends Cell
{
    protected $tag = 'div';

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
    protected $altField = null;

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
    public function setAltField($field = null)
    {
        if ($field) {
            $this->altField = $field;
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
    public function beforeToHtml()
    {
        $this->setContent('');
        $this->setClass('image-wrapper');

        $container = Element::forge();
        $container->setClass('image-container');

        $image = Element::forge()->setTag('img')->addClass('grid-thumb');
        if (isset($this->match)) {
            $image->setAttribute('src', $this->url($this->match, $this->data, null, $this->matchOnlyMaskParams));
        }
        $image->setAttribute('alt', $this->altField ? $this->data[$this->altField] : $this->alt);

        $this->addContent($container->addContent($image));

        return $this;
    }
}

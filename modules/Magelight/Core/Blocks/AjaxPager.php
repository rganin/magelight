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
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Magelight\Core\Blocks;

/**
 * @method static \Magelight\Core\Blocks\AjaxPager forge(\Magelight\Db\Collection $collection)
 */
class AjaxPager extends Pager
{

    /**
     * Forgery constructor
     *
     * @param \Magelight\Db\Collection $collection - collection to build pager for
     */
    public function __forge(\Magelight\Db\Collection $collection = null)
    {
        parent::__forge($collection);
        \Magelight\Core\Blocks\Document::getFromRegistry()
            ->addJs('Magelight/Core/static/js/ajax-pager.js');
        $this->addClass('ajax-pager');

    }

    /**
     * Set target container selector
     *
     * @param string $selector
     * @return AjaxPager
     */
    public function setTargetContainerSelector($selector = '')
    {
        return $this->setAttribute('data-target-selector', $selector);
    }
}

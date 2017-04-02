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
 * @copyright Copyright (c) 2013 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Webform\Blocks;

/**
 * @method static $this forge()
 */
class FilterForm extends Form
{
    /**
     * Use session storage for filter
     *
     * @var bool
     */
    protected $useSessionStorage = true;

    /**
     * Load filter form from request
     *
     * @param \Magelight\Http\Request $request
     * @return FilterForm
     */
    public function loadFromRequest(\Magelight\Http\Request $request = null)
    {
        parent::loadFromRequest($request);
        if ($this->useSessionStorage && empty($this->requestFields)) {
            $this->loadFromSession();
        }
        $this->saveToSession();
        return $this;
    }

    /**
     * Set use session for data storage
     *
     * @param bool $flag
     * @return FilterForm
     */
    public function useSession($flag = true)
    {
        $this->useSessionStorage = $flag;
        return $this;
    }

    /**
     * Get collection filter from form
     *
     * @return mixed
     */
    public function getCollectionFilter()
    {
        return \Magelight\Db\CollectionFilter::forge($this->getRequestFields());
    }

    /**
     * Initialize filter form
     *
     * @return \Magelight\Block|void
     */
    public function initBlock()
    {
        $this->addClass('filter-form');
    }
}

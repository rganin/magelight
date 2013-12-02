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

namespace Magelight\Visitors\Blocks;

/**
 * Class VisitorsList
 *
 * @package Magelight\Visitors\Blocks
 *
 * @method static \Magelight\Visitors\Blocks\VisitorsList forge(\Magelight\Db\Collection $visitorsCollection, $currentPage = 0)
 */
class VisitorsList extends \Magelight\Block
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'Magelight/Visitors/templates/visitors-list.phtml';

    /**
     * Collection object
     *
     * @var \Magelight\Db\Collection
     */
    protected $_collection;

    /**
     * Current page
     *
     * @var int
     */
    protected $_currentPage;

    /**
     * Forgery constructor
     *
     * @param \Magelight\Db\Collection $visitorsCollection
     * @param int $currentPage
     */
    public function __forge(\Magelight\Db\Collection $visitorsCollection, $currentPage = 0)
    {
        $this->_collection = $visitorsCollection;
        $this->_currentPage = $currentPage;
        $this->_collection->setPage($currentPage);
        $total = 0;
        $this->visitors = $this->_collection->fetchAll(true, $total);
        $this->total = $total;
        $this->sectionAppend('visitors-pager', $this->_getPagerBlock());
    }

    /**
     * Get pager block
     *
     * @return \Magelight\Core\Blocks\Pager
     */
    protected function _getPagerBlock()
    {
        $pagerBlock = \Magelight\Core\Blocks\Pager::forge($this->_collection);
        $pagerBlock->setCurrentPage($this->_currentPage);
        return $pagerBlock;
    }
}

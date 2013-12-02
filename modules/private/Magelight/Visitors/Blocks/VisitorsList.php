<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 01.12.13
 * Time: 22:19
 * To change this template use File | Settings | File Templates.
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

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rganin
 * Date: 11.10.13
 * Time: 13:44
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Admin\Blocks\Scaffold;

/**
 * Class EntityList
 * @package Magelight\Blocks\Scaffold
 *
 * @method static EntityList forge($entity, $page)
 */
class EntityList extends \Magelight\Block
{
    protected $_entity;

    protected $_page;

    /**
     * @var \Magelight\Admin\Models\Scaffold\Scaffold
     */
    protected $_scaffold;

    protected $_template = 'Magelight/Admin/templates/scaffold/entity-list.phtml';

    public function __forge($entity, $page)
    {
        $this->setEntity($entity);
        $this->setPage($page);
        $this->_scaffold = \Magelight\Admin\Models\Scaffold\Scaffold::forge();
        $this->_scaffold->loadEntities();
        $this->sectionReplace('pager', \Magelight\Core\Blocks\Pager::forge($this->getCollection())
            ->setRoute(
                \Magelight::app()->getCurrentAction()['match'],
                ['entity' => $entity]
            )->addClass('pagination-small')
            ->addClass('pagination-centered'));
    }

    public function setEntity($entity)
    {
        $this->_entity = $entity;
    }

    public function setPage($page)
    {
        $this->_page = $page;
    }

    public function beforeToHtml()
    {
        $this->tableFields = $this->_scaffold->getEntityFields($this->_entity);
    }

    /**
     * Get entity collection
     *
     * @return \Magelight\Db\Collection
     */
    public function getCollection()
    {
        return \Magelight\Db\Collection::forge(
            $this->_scaffold->getEntityModel($this->_entity)->getOrm()
        )->setLimit(10)->setPage($this->_page);
    }
}
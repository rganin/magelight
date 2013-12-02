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

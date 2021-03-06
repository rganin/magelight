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
 * @method static $this forge($entity, $page)
 */
class EntityList extends \Magelight\Block
{
    /**
     * @var string
     */
    protected $entity;

    /**
     * @var int
     */
    protected $page;

    /**
     * @var \Magelight\Admin\Models\Scaffold\Scaffold
     */
    protected $scaffold;

    /**
     * @var string
     */
    protected $template = 'Magelight/Admin/templates/scaffold/entity-list.phtml';

    /**
     * Forgery
     *
     * @param $entity
     * @param $page
     */
    public function __forge($entity, $page)
    {
        $this->setEntity($entity);
        $this->setPage($page);
        $this->scaffold = \Magelight\Admin\Models\Scaffold\Scaffold::forge();
        $this->scaffold->loadEntities();
        $this->sectionReplace('pager', \Magelight\Core\Blocks\Pager::forge($this->getCollection())
            ->setRoute(
                \Magelight\App::getInstance()->getCurrentAction()['match'],
                ['entity' => $entity]
            )->addClass('pagination-small')
            ->addClass('pagination-centered'));
    }

    /**
     * Set entity
     *
     * @param string $entity
     * @return $this
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Set page
     *
     * @param int $page
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeToHtml()
    {
        $this->tableFields = $this->scaffold->getEntityFields($this->entity);
        return parent::beforeToHtml();
    }

    /**
     * Get entity collection
     *
     * @return \Magelight\Db\Collection
     */
    public function getCollection()
    {
        return \Magelight\Db\Collection::forge(
            $this->scaffold->getEntityModel($this->entity)->getOrm()
        )->setLimit(10)->setPage($this->page);
    }
}

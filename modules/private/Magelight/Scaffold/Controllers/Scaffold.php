<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rganin
 * Date: 11.10.13
 * Time: 15:50
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Scaffold\Controllers;

class Scaffold extends \Magelight\Core\Controllers\BaseController
{
    /**
     * @var \Magelight\Scaffold\Models\Scaffold
     */
    protected $_scaffold = null;

    public function beforeExecute()
    {
        $this->app()->fireEvent('access_scaffolding', [
                'controller' => $this,
                'user_id' => $this->session()->get('user_id')
            ]
        );
        parent::beforeExecute();
        $this->view()->sectionReplace('top', '');
        $this->_scaffold = \Magelight\Scaffold\Models\Scaffold::forge();
        $this->_scaffold->loadEntities();
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->view()->setTitle('Scaffold index action');
        $entitiesBlock = \Magelight\Scaffold\Blocks\Entities::forge();
        $entitiesBlock->entities = $this->_scaffold->getEntities();
        $this->view()->sectionReplace('content', $entitiesBlock);
        $this->renderView();
    }

    public function createAction()
    {
        $this->view()->sectionReplace('content', 'Scaffold create action');
        $this->renderView();
    }

    public function readAction()
    {
        $this->view()->sectionReplace('content', 'Scaffold read action');
        $this->renderView();
    }

    public function updateAction()
    {
        $this->view()->sectionReplace('content', 'Scaffold update action');
        $this->renderView();
    }

    public function deleteAction()
    {
        $this->view()->sectionReplace('content', 'Scaffold delete action');
        $this->renderView();
    }

    public function entity_listAction()
    {
        $this->view()->sectionReplace('content', 'Scaffold entity_list action');
        $this->renderView();
    }
}
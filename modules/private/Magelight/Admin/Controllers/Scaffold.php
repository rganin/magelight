<?php

namespace Magelight\Admin\Controllers;

class Scaffold extends Base
{
    /**
     * @var \Magelight\Admin\Models\Scaffold\Scaffold
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
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->view()->setTitle('Admin - Scaffold index action');
        $this->view()->sectionReplace('content', \Magelight\Admin\Blocks\Scaffold\Entities::forge());
        $this->renderView();
    }

    public function entity_listAction()
    {
        $this->view()->sectionReplace('content', 'Scaffold entity_list action');
        $entity = $this->request()->getGet('entity');
        $page = $this->request()->getGet('page');
        $this->view()->sectionReplace('content', \Magelight\Admin\Blocks\Scaffold\EntityList::forge($entity, $page));
        $this->renderView();
    }

    public function createAction()
    {
        $this->view()->sectionReplace('content', 'Scaffold create action');
        $this->renderView();
    }

    public function readAction()
    {
        $entity = $this->request()->getGet('entity');
        $id = $this->request()->getGet('id');
        $content = \Magelight\Admin\Blocks\Scaffold\EntityForm::forge($entity, $id);
        $this->view()->sectionReplace('content', $content);
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
}

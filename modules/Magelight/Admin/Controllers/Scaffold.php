<?php

namespace Magelight\Admin\Controllers;

class Scaffold extends Base
{
    /**
     * Entity
     *
     * @var string
     */
    protected $_entity = null;

    /**
     * @var \Magelight\Admin\Models\Scaffold\Scaffold
     */
    protected $_scaffold;

    public function beforeExecute()
    {
        \Magelight\Event\Manager::getInstance()->dispatchEvent('access_scaffolding', [
                'controller' => $this,
                'user_id' => $this->session()->get('user_id')
            ]
        );
        $this->_entity = $this->request()->getGet('entity');
        parent::beforeExecute();
        $this->_breadcrumbsBlock->addBreadcrumb(__('Scaffolding'), 'admin/scaffold');
        if (!empty($this->_entity)) {
            $this->_breadcrumbsBlock->addBreadcrumb(
                $this->_entity, 'admin/scaffold/{entity}/', ['entity' => $this->_entity]
            );
        }
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->view()->setTitle('Admin - '. __('Scaffold index action'));
        $this->view()->sectionReplace('content', \Magelight\Admin\Blocks\Scaffold\Entities::forge());
    }

    public function entity_listAction()
    {
        $this->view()->setTitle('Admin - ' . __('Scaffold entity_list action'));
        $page = $this->request()->getGet('page');
        $this->view()->sectionReplace(
            'content',
            \Magelight\Admin\Blocks\Scaffold\EntityList::forge($this->_entity, $page)
        );
    }

    public function createAction()
    {
        $this->view()->setTitle('Admin - ' . __('Scaffold create action'));
        $this->_breadcrumbsBlock->addBreadcrumb(
            __('Create ') . $this->_entity, 'admin/scaffold/{entity}/create', ['entity' => $this->_entity]
        );
        $this->view()->sectionReplace(
            'content',
            \Magelight\Admin\Blocks\Scaffold\EntityForm::forge($this->_entity)
        );
    }

    public function readAction()
    {
        $id = $this->request()->getGet('id');
        $this->_breadcrumbsBlock->addBreadcrumb(
            __('Edit ') . $this->_entity, 'admin/scaffold/{entity}/read/{id}', [
                'entity' => $this->_entity,
                'id' => $id,
            ]
        );
        $this->view()->sectionReplace(
            'content',
            \Magelight\Admin\Blocks\Scaffold\EntityForm::forge($this->_entity)
                ->loadEntityData($id)
        );
    }

    public function updateAction()
    {

        $form = \Magelight\Admin\Blocks\Scaffold\EntityForm::forge($this->_entity);
        $form->loadFromRequest();
        $scaffold = $form->getScaffold();
        $id = $form->getFieldValue($scaffold->getEntityIdField($this->_entity));
        $this->_breadcrumbsBlock->addBreadcrumb(
            __('Edit ') . $this->_entity, 'admin/scaffold/{entity}/read/{id}', [
                'entity' => $this->_entity,
                'id' => $id,
            ]
        );
        if (!$form->isEmptyRequest()) {
            $model = $scaffold->getEntityModel($this->_entity, $form->getRequestFields());
            try {
                if ($model->save()) {
                    $form->addResult(
                        __("%s entity with ID %s saved.", [$this->_entity, $id]), 'alert-success');
                } else {
                    $form->addResult(
                        __("Cannot save %s entity with ID %s.",  [$this->_entity, $id]));
                }
            } catch (\Exception $e) {
                $form->addResult($e->getMessage());
            }
        }
        $this->view()->sectionReplace('content', $form);
    }

    public function deleteAction()
    {
        $id = $this->request()->getGet('id');
        $this->_breadcrumbsBlock->addBreadcrumb(
            'Delete ' . $this->_entity . ' id=' . $id,
            'admin/scaffold/{entity}/delete/{id}',
            [
                'entity' => $this->_entity,
                'id' => $id,
            ]
        );
        $block = \Magelight\Admin\Blocks\Scaffold\Delete::forge($this->_entity, $id);
        $form = $block->getDeleteForm();
        $form->loadFromRequest();
        if ($form->isEmptyRequest()) {
            $this->view()->sectionReplace('content', $block);
        } else {
            if ($form->getFieldValue('id') === $id) {
                $scaffold = \Magelight\Admin\Models\Scaffold\Scaffold::forge();
                $scaffold->loadEntities();
                $model = $scaffold->getEntityModel($this->_entity);
                $form->createResultRow();
                if ($model->delete($id)) {
                    $this->redirectInternal('admin/scaffold/{entity}', ['entity' => $this->_entity]);
                } else {
                    $form->addResult(__('Can`t delete'));
                }
            }
        }
    }

    public function afterExecute()
    {
        $this->renderView();
    }
}

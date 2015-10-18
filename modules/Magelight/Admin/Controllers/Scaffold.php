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
 * @copyright Copyright (c) 2012-2015 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Admin\Controllers;

/**
 * Class Scaffold
 * @package Magelight\Admin\Controllers
 */
class Scaffold extends Base
{
    /**
     * Entity
     *
     * @var string
     */
    protected $entity = null;

    /**
     * @var \Magelight\Admin\Models\Scaffold\Scaffold
     */
    protected $scaffold;

    /**
     * Before execute handler
     *
     * @throws \Magelight\Exception
     */
    public function beforeExecute()
    {
        \Magelight\Event\Manager::getInstance()->dispatchEvent('access_scaffolding', [
                'controller' => $this,
                'user_id' => $this->session()->get('user_id')
            ]
        );
        $this->entity = $this->request()->getGet('entity');
        parent::beforeExecute();
        $this->breadcrumbsBlock->addBreadcrumb(__('Scaffolding'), 'admin/scaffold');
        if (!empty($this->entity)) {
            $this->breadcrumbsBlock->addBreadcrumb(
                $this->entity, 'admin/scaffold/{entity}/', ['entity' => $this->entity]
            );
        }
        return parent::beforeExecute();
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->view()->setTitle('Admin - '. __('Scaffold index action'));
        $this->view()->sectionReplace('content', \Magelight\Admin\Blocks\Scaffold\Entities::forge());
    }

    /**
     * Entitylist action
     */
    public function entity_listAction()
    {
        $this->view()->setTitle('Admin - ' . __('Scaffold entity_list action'));
        $page = $this->request()->getGet('page');
        $this->view()->sectionReplace(
            'content',
            \Magelight\Admin\Blocks\Scaffold\EntityList::forge($this->entity, $page)
        );
    }

    /**
     * Entity creation action
     */
    public function createAction()
    {
        $this->view()->setTitle('Admin - ' . __('Scaffold create action'));
        $this->breadcrumbsBlock->addBreadcrumb(
            __('Create ') . $this->entity, 'admin/scaffold/{entity}/create', ['entity' => $this->entity]
        );
        $this->view()->sectionReplace(
            'content',
            \Magelight\Admin\Blocks\Scaffold\EntityForm::forge($this->entity)
        );
    }

    /**
     * Read entity action
     */
    public function readAction()
    {
        $id = $this->request()->getGet('id');
        $this->breadcrumbsBlock->addBreadcrumb(
            __('Edit ') . $this->entity, 'admin/scaffold/{entity}/read/{id}', [
                'entity' => $this->entity,
                'id' => $id,
            ]
        );
        $this->view()->sectionReplace(
            'content',
            \Magelight\Admin\Blocks\Scaffold\EntityForm::forge($this->entity)
                ->loadEntityData($id)
        );
    }

    /**
     * Update entity action
     */
    public function updateAction()
    {

        $form = \Magelight\Admin\Blocks\Scaffold\EntityForm::forge($this->entity);
        $form->loadFromRequest();
        $scaffold = $form->getScaffold();
        $id = $form->getFieldValue($scaffold->getEntityIdField($this->entity));
        $this->breadcrumbsBlock->addBreadcrumb(
            __('Edit ') . $this->entity, 'admin/scaffold/{entity}/read/{id}', [
                'entity' => $this->entity,
                'id' => $id,
            ]
        );
        if (!$form->isEmptyRequest()) {
            $model = $scaffold->getEntityModel($this->entity, $form->getRequestFields());
            try {
                if ($model->save()) {
                    $form->addResult(
                        __("%s entity with ID %s saved.", [$this->entity, $id]), 'alert-success');
                } else {
                    $form->addResult(
                        __("Cannot save %s entity with ID %s.",  [$this->entity, $id]));
                }
            } catch (\Exception $e) {
                $form->addResult($e->getMessage());
            }
        }
        $this->view()->sectionReplace('content', $form);
    }

    /**
     * Delete entity action
     */
    public function deleteAction()
    {
        $id = $this->request()->getGet('id');
        $this->breadcrumbsBlock->addBreadcrumb(
            'Delete ' . $this->entity . ' id=' . $id,
            'admin/scaffold/{entity}/delete/{id}',
            [
                'entity' => $this->entity,
                'id' => $id,
            ]
        );
        $block = \Magelight\Admin\Blocks\Scaffold\Delete::forge($this->entity, $id);
        $form = $block->getDeleteForm();
        $form->loadFromRequest();
        if ($form->isEmptyRequest()) {
            $this->view()->sectionReplace('content', $block);
        } else {
            if ($form->getFieldValue('id') === $id) {
                $scaffold = \Magelight\Admin\Models\Scaffold\Scaffold::forge();
                $scaffold->loadEntities();
                $model = $scaffold->getEntityModel($this->entity);
                $form->createResultRow();
                if ($model->delete($id)) {
                    $this->redirectInternal('admin/scaffold/{entity}', ['entity' => $this->entity]);
                } else {
                    $form->addResult(__('Can`t delete'));
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function afterExecute()
    {
        $this->renderView();
        return parent::afterExecute();
    }
}

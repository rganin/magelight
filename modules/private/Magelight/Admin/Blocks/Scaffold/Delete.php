<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 01.12.13
 * Time: 13:39
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Admin\Blocks\Scaffold;

/**
 * Class Delete
 * @package Magelight\Admin\Blocks\Scaffold
 *
 * @method static \Magelight\Admin\Blocks\Scaffold\Delete forge($entity, $id)
 */
class Delete extends \Magelight\Block
{
    protected $_template = 'Magelight/Admin/templates/scaffold/delete.phtml';

    /**
     * @var \Magelight\Webform\Blocks\Form
     */
    protected $_deleteForm;

    /**
     * Forgery constructor
     *
     * @param string $entity
     * @param int $id
     */
    public function __forge($entity, $id)
    {
        $this->entity = $entity;
        $this->entity_title = $entity;
        $this->id = $id;
        $this->sectionReplace('delete-form', $this->getDeleteForm());
    }

    /**
     * Get delete form
     *
     * @return \Magelight\Webform\Blocks\Form
     */
    public function getDeleteForm()
    {
        if (!$this->_deleteForm instanceof \Magelight\Webform\Blocks\Form) {
            $this->_deleteForm = \Magelight\Webform\Blocks\Form::forge()->setConfigs(
                'delete-form',
                $this->url('admin/scaffold/{entity}/delete/{id}',
                    [
                        'entity' => $this->entity,
                        'id'     => $this->id
                    ]
                ));
            $cancelBtn = \Magelight\Webform\Blocks\Elements\Abstraction\Element::forge()
                ->setTag('a')
                ->setClass('btn btn-success')
                ->setAttribute('href', $this->url('admin/scaffold/{entity}', ['entity' => $this->entity]))
                ->setContent('Cancel');
            $this->_deleteForm->addContent(\Magelight\Webform\Blocks\Elements\InputHidden::forge()->setName('id')->setValue($this->id));
            $this->_deleteForm->addButtonsRow([
                \Magelight\Webform\Blocks\Elements\Button::forge()
                    ->setContent('Delete')->setType('submit')->addClass('btn-warning'),
                $cancelBtn
            ]);
        }
        return $this->_deleteForm;
    }
}

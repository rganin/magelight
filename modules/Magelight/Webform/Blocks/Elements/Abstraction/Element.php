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

namespace Magelight\Webform\Blocks\Elements\Abstraction;

/**
 * @method static $this forge()
 */
class Element extends \Magelight\Core\Blocks\Element
{
    /**
     * Form pointer
     *
     * @var \Magelight\Webform\Blocks\Form
     */
    protected $form = null;

    /**
     * Registered ids
     *
     * @var array
     */
    protected static $registeredIds = [];

    /**
     * Set element attribute
     *
     * @param string $name
     * @param string $value
     * @param string $quotaiton
     * @return Element
     * @throws \Magelight\Exception
     */
    public function setAttribute($name, $value, $quotaiton = self::QUOTATION_DEFAULT)
    {
        if ($name === 'id') {
            throw new \Magelight\Exception(
                __('Direct id assignment is not allowed. Use setId() method to set id attribute.')
            );
        }
        return parent::setAttribute($name, $value, $quotaiton);
    }

    /**
     * Set element ID
     *
     * @param string $id
     * @return Element
     */
    public function setId($id)
    {
        $id = $this->wrapId($id);
        $this->attributes['id']['value'] = $id;
        return $this->registerId($id);
    }

    /**
     * Register ID globally
     *
     * @param string $id
     * @return Element
     */
    protected function registerId($id)
    {
        self::$registeredIds[$id] = $this;
        return $this;
    }

    /**
     * Check is ID registered
     * @param string $id
     * @return bool
     */
    protected function isIdRegistered($id)
    {
        return isset(self::$registeredIds[$id]);
    }

    /**
     * Wrap ID globally to unique value
     *
     * @param string $id
     * @return mixed
     */
    protected function wrapId($id)
    {
        $newId = $id;
        $suffix = 0;
        while (isset(self::$registeredIds[$newId])) {
            $newId = $id . '-' . $suffix;
            $suffix++;
        }
        return $newId;
    }

    /**
     * Set element content
     *
     * @param string $content
     * @return Element
     */
    public function setContent($content)
    {
        return $this->addContent($content);
    }

    /**
     * Set form that owns this element
     *
     * @param \Magelight\Webform\Blocks\Form $form
     * @return Element
     */
    public function setForm(\Magelight\Webform\Blocks\Form $form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * Bind element to form
     *
     * @param \Magelight\Webform\Blocks\Form $form
     * @return Element
     */
    public function bindForm(\Magelight\Webform\Blocks\Form $form = null)
    {
        $this->form = $form;
        foreach ($this->content as $child) {
            if ($child instanceof \Magelight\Webform\Blocks\Elements\Abstraction\Element) {
                /* @var $child \Magelight\Webform\Blocks\Elements\Abstraction\Element */
                $child->bindForm($this->form);
            }
        }
        return $this;
    }

    /**
     * Get bound form
     *
     * @return \Magelight\Webform\Blocks\Form|null
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Get element by ID
     *
     * @param string $id
     *
     * @return \Magelight\Webform\Blocks\Elements\Abstraction\Element|null
     */
    public function getElementById($id)
    {
        $result = null;
        foreach ($this->content as $element) {
            if ($element instanceof \Magelight\Webform\Blocks\Elements\Abstraction\Element) {
                if ($element->getId() == $id) {
                    return $element;
                } else {
                    $result = $element->getElementById($id);
                }
            }
            if ($result instanceof \Magelight\Webform\Blocks\Elements\Abstraction\Element) {
                break;
            }
        }
        return $result;
    }

    /**
     * Generate element id from name
     *
     * @param null $name
     * @return null|string
     */
    public function generateIdFromName($name = null)
    {
        if ($name === null) {
            $name = $this->getAttribute('name', null);
            if ($name === null) {
                return null;
            }

        }
        return $this->tag . '-' . preg_replace("([^a-z0-9]+)", '', $name);
    }
}

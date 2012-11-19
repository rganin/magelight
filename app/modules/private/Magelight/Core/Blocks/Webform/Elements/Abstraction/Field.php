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
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Core\Blocks\Webform\Elements\Abstraction;

/**
 * @method static \Magelight\Core\Blocks\Webform\Elements\Abstraction\Field forge()
 * @method \Magelight\Core\Blocks\Webform\Elements\Abstraction\Field setContent()
 */
class Field extends Element
{
    /**
     * Element tag
     *
     * @var string
     */
    protected $_tag = 'input';

    /**
     * Set element name attribute
     *
     * @param string $name
     * @return Field
     */
    public function setName($name)
    {
        $this->setAttribute('name', $name);
        $this->setId($this->_tag . '-' . preg_replace("([^a-z0-9]+)", '', $name));
        return $this;
    }

    /**
     * Set element value attribute
     *
     * @param string $value
     * @return Element
     */
    public function setValue($value)
    {
        return $this->setAttribute('value', $value);
    }

    /**
     * Get element name
     *
     * @return mixed|string
     */
    public function getName()
    {
        return $this->getAttribute('name');
    }

    /**
     * Set element placeholder content
     *
     * @param string $placeholderContent
     * @return Element
     */
    public function setPlaceholder($placeholderContent)
    {
        return $this->setAttribute('placeholder', $placeholderContent);
    }

    public function beforeToHtml()
    {
        if ($this->_form instanceof \Magelight\Core\Blocks\Webform\Form) {
            $this->setAttribute('name', $this->_form->wrapName($this->getAttribute('name')));
        }
        return $this;
    }
}

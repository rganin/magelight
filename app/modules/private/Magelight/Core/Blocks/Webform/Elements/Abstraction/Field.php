<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 16.11.12
 * Time: 23:35
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Core\Blocks\Webform\Elements\Abstraction;

/**
 * @method static \Magelight\Core\Blocks\Webform\Elements\Abstraction\Field forge()
 * @method \Magelight\Core\Blocks\Webform\Elements\Abstraction\Field setContent()
 */
class Field extends Element
{
    protected $_tag = 'input';

    public function setName($name)
    {
        $this->setAttribute('name', $name);
        $this->setId($this->_tag . '-' . preg_replace("([^a-z0-9]+)", '', $name));
        return $this;
    }

    public function setValue($value)
    {
        return $this->setAttribute('value', $value);
    }

    public function getName()
    {
        return $this->getAttribute('name');
    }

    public function setPlaceholder($placeholderContent)
    {
        return $this->setAttribute('placeholder', $placeholderContent);
    }
}

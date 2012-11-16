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

namespace Magelight\Core\Blocks\Form;

class Form extends AbstractFormElement
{
    const METHOD_POST = 'post';
    const METHOD_GET = 'get';
    const METHOD_PUT = 'put';
    const METHOD_DELETE = 'delete';

    const TYPE_GENERIC = '';
    const TYPE_MULTIPART = 'multipart/form-data';

    public function __construct($type = self::TYPE_GENERIC, $method = self::METHOD_POST)
    {
        $this->setAttribute('type', $type);
        $this->setAttribute('method', $method);
    }

    public function addFieldset(Fieldset $fieldset)
    {
        $this->_elements[] = $fieldset;
        return $this;
    }

    public function addField(Field $field)
    {
        $this->_elements[] = $field;
        return $this;
    }

    public function setAction($actionUrl,
                              $params = array(),
                              $type = \Magelight\Helpers\UrlHelper::TYPE_HTTP,
                              $useUrlHelper = true
    )
    {
            return $useUrlHelper ? $this->setAttribute(
                'action',
                \Magelight\Helpers\UrlHelper::getInstance()->getUrl($actionUrl, $params, $type)
            ) :  $this->setAttribute('action', $actionUrl);
    }

    public function fieldset()
    {
        return \Magelight\Core\Blocks\Form\Fieldset::forge();
    }

    public function field()
    {
        return \Magelight\Core\Blocks\Form\Field::forge();
    }
}
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

namespace Magelight\Webform\Blocks;

/**
 * @method static \Magelight\Webform\Blocks\AjaxForm forge()
 */
class AjaxForm extends Form
{
    /**
     * Data passing type
     */
    const DATA_TYPE_HTML = 'html';
    const DATA_TYPE_JSON = 'json';

    /**
     * Forgery constructor
     */
    public function __forge()
    {
        $this->setAttribute('data-async', 'true');
        $this->setAttribute('data-async-target', '');
        \Magelight\Core\Blocks\Document::getInstance()
            ->addJs('Magelight/Webform/static/js/ajax-form.js');
    }

    /**
     * Set form configurations
     *
     * @param string $name
     * @param string $action
     * @param string $dataType
     * @param string $enctype
     * @param string $method
     *
     * @return Form
     */
    public function setConfigs($name,
                               $action,
                               $dataType = self::DATA_TYPE_HTML,
                               $enctype  = 'multipart/form-data',
                               $method   = 'post'
    )
    {
        $this->setResultDataType($dataType);
        return parent::setConfigs($name, $action, $enctype, $method);
    }

    /**
     * Set form result data type
     *
     * @param string $dataType
     *
     * @return Elements\Abstraction\Element
     */
    public function setResultDataType($dataType = self::DATA_TYPE_HTML)
    {
        return $this->setAttribute('data-async-result-type', $dataType);
    }
}

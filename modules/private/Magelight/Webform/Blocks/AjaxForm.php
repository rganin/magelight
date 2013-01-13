<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 13.01.13
 * Time: 14:19
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Webform\Blocks;

/**
 * @method static \Magelight\Webform\Blocks\AjaxForm forge()
 */
class AjaxForm extends Form
{
    const DATA_TYPE_HTML = 'html';
    const DATA_TYPE_JSON = 'json';

    public function __forge()
    {
        $this->setAttribute('data-async', 'true');
        $this->setAttribute('data-async-target', '');
        \Magelight\Core\Blocks\Document::getFromRegistry()
            ->addJs('modules/private/Magelight/Webform/static/js/ajax-form.js');
    }

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

    public function setResultDataType($dataType = self::DATA_TYPE_HTML)
    {
        return $this->setAttribute('data-async-result-type', $dataType);
    }
}
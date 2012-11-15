<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category
 * @package
 * @subpackage
 * @author
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
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

namespace Bike\Html\Form;

class Form extends \Bike\Html\Tag
{
    /**
     * Tag name
     *
     * @var string
     */
    protected $_name = 'form';

    protected $_isAjax = false;
    
    protected $_ajaxResultFieldId = null;

    protected $_children = array();
    
    protected $_formData = array();

    public function setMultipartType()
    {
        $this->setType ('multipart/form-data');
    }

    public function setType($type = '')
    {
        return $this->setAttribute('type', $type);
    }
   
    public function setId($id)
    {
        return $this->setAttribute('id', $id);        
    }

    public function setAction($action)
    {
        return $this->setAttribute('action', $action);
    }

    public function setIsAjax($value = true)
    {
        $this->_isAjax = $value;
        return $this;
    }

    public function addFieldset(\Bike\Html\Form\Fieldset $fieldset)
    {

    }
    
    public function render()
    {
        if ($this->_isAjax && is_null($this->_ajaxResultFieldId)) {
            throw new \Bike\Exception('Ajax form if field is not set!');
        }
    }
}
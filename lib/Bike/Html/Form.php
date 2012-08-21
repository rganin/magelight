<?php

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
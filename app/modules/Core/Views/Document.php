<?php
/**
 * $$name_placeholder_notice$$
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
 * @version $$version_placeholder_notice$$
 * @author $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Core\Views;

class Document extends \Bike\View
{
    protected $_template = 'document.phtml';
    
    const REGISTRY_KEY = 'core/document';
    
    protected function __construct()
    {
        \Bike::app()->setRegistryObject(self::REGISTRY_KEY, $this);
    }
    
    public function addCss($path, $after = null, $media = 'all')
    {
        $css = $this->get('css', array());
        $entry = array(
            'path' => $path,
            'media' => $media,
            'inline' => false,
            'content' => null,
        );
        $css = \Bike\Helpers\ArrayHelper::getInstance()->insertToArray($css, $path, $entry, $after);
        $this->set('css', $css);
    }
    
    public function addInlineCss($content, $after = null, $media = 'all')
    {
        $css = $this->get('css', array());
        $entry = array(
            'path' => null,
            'media' => $media,
            'inline' => true,
            'content' => $content,
        );
        $path = md5($content);
        $css = \Bike\Helpers\ArrayHelper::getInstance()->insertToArray($css, $path, $entry, $after);
        $this->set('css', $css);
    }
    
    public function addJs($path, $after = null)
    {
        $js = $this->get('js', array());
        $entry = array(
            'path' => $path,
            'inline' => false,
            'content' => null,
        );
        $js = \Bike\Helpers\ArrayHelper::getInstance()->insertToArray($js, $path, $entry, $after);
        $this->set('js', $js);
    }
    
    public function addInlineJs($content, $after = null)
    {
        $js = $this->get('js', array());
        $entry = array(
            'path' => null,
            'inline' => true,
            'content' => $content,
        );
        $path = md5($content);
        $js = \Bike\Helpers\ArrayHelper::getInstance()->insertToArray($js, $path, $entry, $after);
        $this->set('js', $js);
    }
    
    public function setTitle($title)
    {
        $this->set('title', $title);
        return $this;
    }
    
    public function setLang($lang)
    {
        $this->set('lang', $lang);
        return $this;
    }
}
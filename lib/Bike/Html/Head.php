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

namespace Bike\Html;

class Head extends Tag
{
    /**
     * Meta tags array
     * 
     * @var array of Tag
     */
    protected $_meta = array();
    
    /**
     * Links array
     * 
     * @var array of Tag
     */
    protected $_links = array();
    
    /**
     * Javascript array
     * 
     * @var array of Tag
     */
    protected $_js = array();
    
    /**
     * Title tag
     * 
     * @var string
     */
    protected $_title = '';
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('head');
    }
    
    /**
     * Add JS to head
     * 
     * @param string $path
     * @param string $after
     * @return Head
     */
    public function addJs($path, $after = null)
    {
        $tag = new Tag('script');
        $tag->addAttribute('type', 'text/javascript');
        $tag->addAttribute('src', $path);
        $this->_js = \Helpers\ArrayHelper::insertToArray($this->_js, $path, $tag, $after);
        return $this;
    }
    

    
    /**
     * Add inline JS to head
     * 
     * @param string $content
     * @param string $after
     * @return Head
     */
    public function addInlineJs($content, $after = null)
    {
        $tag = new Tag('script');
        $tag->addAttribute('type', 'text/javascript')->setContent($content);
        $this->_js = \Helpers\ArrayHelper::insertToArray($this->_js, md5($content), $tag, $after);
        return $this;
    }
    
    /**
     * Add CSS to head
     * 
     * @param string $path
     * @param string $after
     * @param string $media
     * @return Head
     */
    public function addCss($path, $after = null, $media = 'all')
    {
        $tag = new Tag('link');
        $tag->addAttribute('rel', 'stylesheet')
            ->addAttribute('href', $path)
            ->addAttribute('type', 'text/css')
            ->addAttribute('media', $media)
            ->setShort(true);
        $this->_links = \Helpers\ArrayHelper::insertToArray($this->_links, $path, $tag, $after);
        return $this;
    }
    
    /**
     * Add inline CSS to head
     * 
     * @param string $content
     * @param string $after
     * @param string $media
     * @return Head
     */
    public function addInlineCss($content, $after = null, $media = 'all')
    {
        $tag = new Tag('style');
        $tag->addAttribute('media', $media)
            ->setContent($content);
        $this->_links = \Helpers\ArrayHelper::insertToArray($this->_links, md5($content), $tag, $after);
        return $this;
    }
    
    /**
     * Add META TAG to head
     * 
     * @param string $name
     * @param string $content
     * @param string $httpEquiv
     * @return Head
     */
    public function addMeta($name, $content, $httpEquiv = null)
    {
        $tag = new Tag('meta');
        $tag->addAttribute('content', $content)->addAttribute('name', $name);
        if (!empty($httpEquiv)) {
            $tag->addAttribute('http-equiv', $httpEquiv);
        }
        $this->_meta[] = $tag;
        return $this;
    }
    
    /**
     * Set favicon
     * 
     * @param string $path
     * @return Head
     */
    public function setFavicon($path)
    {
        $tag = new Tag('link');
        $tag->addAttribute('rel', 'shortcut icon')
            ->addAttribute('href', $path);
        $this->_links = \Helpers\ArrayHelper::insertToArray($this->_links, $path, $tag);
        return $this;
    }
    
    /**
     * Set document title
     * 
     * @param string $title
     * @return Head
     */
    public function setTitle($title)
    {
        $tag = new Tag('title');
        $tag->setContent($title);
        $this->_title = $tag;
        return $this;
    }
    
    /**
     * Render JS in head
     * 
     * @return string
     */
    protected function renderJs()
    {
        $html = '';
        foreach ($this->_js as $tag) {
            /* @var $tag Tag*/
            $html .= $tag->render();
        }
        return $html;
    }
    
    /**
     * Render links in head
     * 
     * @return string
     */
    protected function renderLinks()
    {
        $html = '';
        foreach ($this->_links as $tag) {
            /* @var $tag Tag*/
            $html .= $tag->render();
        }
        return $html;
    }
    
    /**
     * Render meta tags in head
     * 
     * @return string
     */
    protected function renderMeta()
    {
        $html = '';
        foreach ($this->_meta as $tag) {
            /* @var $tag Tag*/
            $html .= $tag->render();
        }
        return $html;
    }
    
    /**
     * Render HEAD
     * 
     * @return string
     */
    public function render()
    {
        $this->setContent($this->renderMeta() . $this->renderLinks() . $this->renderJs());
        return parent::render();
    }    
    
    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->_js);
        unset($this->_links);
        unset($this->_meta);
        unset($this->_title);
    }
}
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
 * @version   $$version_placeholder_notice$$
 * @uthor     $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Bike\Html;

class Head extends \Bike\Block
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
     * Add JS to head
     * 
     * @param string $path
     * @param string $after
     * @return Head
     */
    public function addJs($path, $after = null)
    {
        $tag = new \Bike\Html\Tag('script');
        $tag->setAttribute('type', 'text/javascript');
        $tag->setAttribute('src', $path);
        $this->_js = \Bike\Helpers\ArrayHelper::insertToArray($this->_js, $path, $tag, $after);
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
        $tag = new \Bike\Html\Tag('script');
        $tag->setAttribute('type', 'text/javascript')->setContent($content);
        $this->_js = \Bike\Helpers\ArrayHelper::insertToArray($this->_js, md5($content), $tag, $after);
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
        $tag = new \Bike\Html\Tag('link');
        $tag->setAttribute('rel', 'stylesheet')
            ->setAttribute('href', $path)
            ->setAttribute('type', 'text/css')
            ->setAttribute('media', $media)
            ->setShort(true);
        $this->_links = \Bike\Helpers\ArrayHelper::insertToArray($this->_links, $path, $tag, $after);
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
        $tag = new \Bike\Html\Tag('style');
        $tag->setAttribute('media', $media)
            ->setContent($content);
        $this->_links = \Bike\Helpers\ArrayHelper::insertToArray($this->_links, md5($content), $tag, $after);
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
        $tag = new \Bike\Html\Tag('meta');
        $tag->setAttribute('content', $content)
            ->setAttribute('name', $name);
        if (!empty($httpEquiv)) {
            $tag->setAttribute('http-equiv', $httpEquiv);
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
        $tag = new \Bike\Html\Tag('link');
        $tag->setAttribute('rel', 'shortcut icon')
            ->setAttribute('href', $path);
        $this->_links = \Bike\Helpers\ArrayHelper::insertToArray($this->_links, $path, $tag);
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
        $tag = new \Bike\Html\Tag('title');
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
            /* @var $tag \Bike\Html\Tag*/
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
            /* @var $tag \Bike\Html\Tag*/
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
            /* @var $tag \Bike\Html\Tag*/
            $html .= $tag->render();
        }
        return $html;
    }

    /**
     * Render title
     * 
     * @return string
     */
    protected function renderTitle()
    {
        return $this->_title->render();    
    }

    /**
     * Render HEAD block
     * 
     * @return string|void
     */
    public function toHtml()
    {
        $this->beforeToHtml();
        echo $this->renderTitle();
        echo $this->renderMeta();
        echo $this->renderLinks();
        echo $this->renderJs();
        $this->afterToHtml();
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
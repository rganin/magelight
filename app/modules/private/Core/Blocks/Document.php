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
 * @version $$version_placeholder_notice$$
 * @author $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Core\Blocks;

class Document extends \Magelight\Block
{
    /**
     * Document template
     * 
     * @var string
     */
    protected $_template = 'modules/private/Core/templates/document.phtml';
    
    /**
     * Key to store this document in registry
     */
    const REGISTRY_KEY = 'core/document';
    
    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->saveToRegistry();
    }

    /**
     * Get document from registry
     *
     * @param string $key
     * @return \Core\Blocks\Document
     */
    public static function getFromRegistry($key = self::REGISTRY_KEY)
    {
        return \Magelight::app()->getRegistryObject($key);
    }

    /**
     * Save object to registry
     *
     * @param string $key
     */
    public function saveToRegistry($key = self::REGISTRY_KEY)
    {
        \Magelight::app()->setRegistryObject($key, $this);
    }

    /**
     * Before To HTML
     *
     * @return \Magelight\Block|void
     */
    public function beforeToHtml()
    {
        $lang = \Magelight::app()->config()->getConfigFirst('global/document/default_lang');
        $this->setLang($lang);
        return $this;
    }
    
    /**
     * Add css to head
     * 
     * @param string $path
     * @param string|null $after
     * @param string $media
     */
    public function addCss($path, $after = null, $media = 'all')
    {
        $css = $this->get('css', array());
        $entry = array(
            'path' => $path,
            'media' => $media,
            'inline' => false,
            'content' => null,
        );
        $css = \Magelight\Helpers\ArrayHelper::getInstance()->insertToArray($css, $path, $entry, $after);
        $this->set('css', $css);
    }
    
    /**
     * Add inline css to head
     * 
     * @param string $content
     * @param string|null $after
     * @param string $media
     */
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
        $css = \Magelight\Helpers\ArrayHelper::getInstance()->insertToArray($css, $path, $entry, $after);
        $this->set('css', $css);
    }
    
    /**
     * Add javascript to head
     * 
     * @param string $path
     * @param string|null $after
     */
    public function addJs($path, $after = null)
    {
        $js = $this->get('js', array());
        $entry = array(
            'path' => $path,
            'inline' => false,
            'content' => null,
        );
        $js = \Magelight\Helpers\ArrayHelper::getInstance()->insertToArray($js, $path, $entry, $after);
        $this->set('js', $js);
    }
    
    /**
     * Add inline javascript to head
     * 
     * @param string $content
     * @param string|null $after
     */
    public function addInlineJs($content, $after = null)
    {
        $js = $this->get('js', array());
        $entry = array(
            'path' => null,
            'inline' => true,
            'content' => $content,
        );
        $path = md5($content);
        $js = \Magelight\Helpers\ArrayHelper::getInstance()->insertToArray($js, $path, $entry, $after);
        $this->set('js', $js);
    }
    
    /**
     * Set page title
     * 
     * @param string $title
     * @return Document
     */
    public function setTitle($title)
    {
        $this->set('title', $title);
        return $this;
    }
    
    /**
     * Set document language
     * 
     * @param string[2] $lang
     * @return Document
     */
    public function setLang($lang)
    {
        $this->set('lang', $lang);
        return $this;
    }

    /**
     * Add meta tag
     *
     * @param array $arrayOfAttributes
     */
    public function addMeta(array $arrayOfAttributes)
    {
        $meta = $this->get('meta', array());
        $index = isset($arrayOfAttributes['name']) ? $arrayOfAttributes['name'] :
            (isset($arrayOfAttributes['http-equiv']) ? $arrayOfAttributes['http-equiv'] : count($meta));
        $meta[$index] = $arrayOfAttributes;
        $this->set('meta', $meta);
    }

    /**
     * Render meta tags
     *
     * @return string
     */
    public function renderMeta()
    {
        $metaSection = '';
        foreach ($this->get('meta', array()) as $metaTagAttributes) {
            if (!is_array($metaTagAttributes)) {
                continue;
            }
            $metaString = "<meta";
            foreach ($metaTagAttributes as $name => $value) {
                $metaString .= " {$name}=\"{$value}\"";
            }
            $metaString .=" />";
            $metaSection .= $metaString . PHP_EOL;
        }
        return $metaSection;
    }

    /**
     * Render title tag
     *
     * @return string
     */
    public function renderTitle()
    {
        return '<title>' . $this->get('title', '') . '</title>' . PHP_EOL;
    }

    /**
     * Render CSS data
     *
     * @return string
     */
    public function renderCss()
    {
        $style = '';
        foreach ($this->get('css', array()) as $css) {
            if (!$css['inline']) {
                $style .=
                    "<link rel=\"stylesheet\" href=\"{$css['path']}\" type=\"text/css\" media=\"{$css['media']}\" />"
                    . PHP_EOL;
            } else {
                $style .= "<style>
                {$css['content']}
                </style>" . PHP_EOL;
            }
        }
        return $style;
    }

    /**
     * Render JS data
     *
     * @return string
     */
    public function renderJs()
    {
        $scripts = '';
        foreach ($this->get('js', array()) as $js) {
            if (!$js['inline']) {
                $scripts .=
                    "<script type=\"text/javascript\" src=\"{$js['path']}\"></script>" . PHP_EOL;
            } else {
                $scripts .= "<script type=\"text/javascript\">
                {$js['content']}
                </script>" . PHP_EOL;
            }
        }
        return $scripts;
    }
}

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

namespace Magelight;
use Magelight\Forgery as Forgery;
/**
 * @static forge() \Magelight\Blocks
 */
abstract class Block
{
    use Forgery;
    use \Magelight\Components\CodePoolFetcher;

    /**
     * Path to template
     *
     * @var string
     */
    protected $_template = null;

    /**
     * Blocks variables
     *
     * @var array
     */
    protected $_vars = [];

    /**
     * Blocks global variables
     *
     * @var array
     */
    protected static $_globalVars = [];

    /**
     * Blocks global sections
     *
     * @var array
     */
    protected static $_sections = [];

    /**
     * Are section initialized globally flag
     *
     * @var bool
     */
    protected static $_sectionsInitialized = false;

    /**
     * Is section initialized flag
     *
     * @var bool
     */
    protected $_initialized = false;

    /**
     * Set Blocks property
     *
     * @param string $name
     * @param mixed $value
     * @return Block
     */
    public function set($name, $value)
    {
        $this->_vars[$name] = $value;
        return $this;
    }

    /**
     * Get Blocks property
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return isset($this->_vars[$name]) ? $this->_vars[$name] : $default;
    }

    /**
     * Set global property
     *
     * @param string $name
     * @param mixed $value
     * @return Block
     */
    public function setGlobal($name, $value)
    {
        self::$_globalVars[$name] = $value;
        return $this;
    }

    /**
     * Get global property
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getGlobal($name, $default = null)
    {
        return isset(self::$_globalVars[$name]) ? self::$_globalVars[$name] : $default;
    }

    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->_vars[$name] = $value;
    }

    /**
     * Getter
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->_vars[$name]) ? $this->_vars[$name] : null;
    }

    /**
     * Append section
     *
     * @param string $name - section name
     * @param \Magelight\Block|string $block
     * @return Block
     */
    public function sectionAppend($name, $block)
    {
        $block->initBlock();
        if (!isset(self::$_sections[$name]) || !is_array(self::$_sections[$name])) {
            return $this->sectionReplace($name, $block);
        }
        self::$_sections[$name][] = $block;
        return $this;
    }

    /**
     * Prepend section
     *
     * @param $name
     * @param Block|string $block
     * @return Block
     */
    public function sectionPrepend($name, $block)
    {
        $block->initBlock();
        if (!isset(self::$_sections[$name]) || !is_array(self::$_sections[$name])) {
            return $this->sectionReplace($name, $block);
        }
        self::$_sections = array_unshift(self::$_sections[$name], $block);
        return $this;
    }

    /**
     * Replace section (clear and create new one with given block)
     *
     * @param $name
     * @param Block|string $block
     * @return Block
     */
    public function sectionReplace($name, $block)
    {
        $block->initBlock();
        if (!is_array(self::$_sections)) {
            self::$_sections = [];
        }
        self::$_sections[$name] = [$block];
        return $this;
    }

    /**
     * Delete section by name
     *
     * @param string $name
     * @return Block
     */
    public function sectionDelete($name)
    {
        unset(self::$_sections[$name]);
        return $this;
    }

    /**
     * Render Blocks to html or whatever is given in template
     *
     * @return string
     * @throws Exception
     */
    public function toHtml()
    {
        $this->initBlock();
        $class = get_called_class();
        if (empty($this->_template)) {
            if (\Magelight::app()->isInDeveloperMode()) {
                throw new \Magelight\Exception("Undeclared template in block '{$class}'");
            } else {
                return '';
            }
        }
        $this->beforeToHtml();
        ob_start();
        include($this->_template);
        $this->afterToHtml();
        return ob_get_clean();
    }

    /**
     * Before render to HTML event
     *
     * @return Block
     */
    protected function beforeToHtml()
    {
        return $this;
    }


    /**
     * After render to Html event
     *
     * @return Block
     */
    protected function afterToHtml()
    {
        return $this;
    }

    /**
     * Render section by name
     *
     * @param string $name
     * @return string
     * @throws Exception
     */
    public function section($name)
    {
        $html = '';
        if (!isset(self::$_sections[$name]) && \Magelight::app()->isInDeveloperMode()) {
            trigger_error("Undefined section call - '{$name}' in " . get_called_class(), E_USER_NOTICE);
        } elseif (isset(self::$_sections[$name]) && is_array(self::$_sections[$name])) {
            foreach (self::$_sections[$name] as $sectionBlock) {
                if ($sectionBlock instanceof Block) {
                    /* @var $sectionBlock \Magelight\Block */
                    $html .= $sectionBlock->toHtml();
                } elseif (is_string($sectionBlock)) {
                    $html .= $sectionBlock;
                }
            }
        }
        return $html;
    }

    /**
     * Test template
     *
     * @param string $template
     * @return Block
     */
    public function setTemplate($template)
    {
        $this->_template = $template;
        return $this;
    }

    /**
     * Initialize Blocks
     *
     * @return Block
     */
    public function init()
    {
        return $this;
    }

    /**
     * Initialize block if it wasn`t initialized
     *
     * @return Block
     */
    public function initBlock()
    {
        if (!$this->_initialized) {
            $this->init();
            $this->_initialized = true;
        }
        return $this;
    }

    /**
     * Fetch url by match mask
     *
     * @param string $match - url match mask
     * @param array $params - params to be passed to URL
     * @param string $type - URL type (http|https)
     * @return string
     */
    public function url($match, $params = [], $type = \Magelight\Helpers\UrlHelper::TYPE_HTTP)
    {
        return \Magelight::app()->url($match, $params, $type);
    }
}

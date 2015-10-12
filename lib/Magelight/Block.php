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

/**
 * Abstract block
 *
 * @method static \Magelight\Block forge()
 */
class Block
{
    /**
     * Use magelight forgery
     */
    use Traits\TForgery;

    /**
     * Allow fetching code pool
     */
    use \Magelight\Traits\TCodePoolFetcher;

    /**
     * Use caching trait
     */
    use \Magelight\Traits\TCache;

    /**
     * Use coalesce trait
     */
    use Traits\TCoalesce;

    /**
     * Path to template
     *
     * @var string
     */
    protected $_template = null;

    /**
     * Enable block html caching
     *
     * @var bool
     */
    protected $_cacheEnabled = false;

    /**
     * Cache Key
     *
     * @var null
     */
    protected $_cacheKey = null;

    /**
     * Cache lifetime
     *
     * @var int
     */
    protected $_cacheTtl = 3600;

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
     * Isset magic
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->_vars[$name]);
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
        if ($block instanceof \Magelight\Block) {
            $block->initBlock();
        }
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
        if ($block instanceof \Magelight\Block) {
            $block->initBlock();
        }
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
        if ($block instanceof \Magelight\Block) {
            $block->initBlock();
        }
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
        if ($html = $this->getFromCache(null)) {
            return $html;
        }
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
        include(str_replace('\\', DS, $this->_template));
        $this->afterToHtml();
        $html = ob_get_clean();
        $this->setToCache($html);
        return $html;
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
     * @param bool $addOnlyMaskParams - add to url only params that are present in URL match mask
     * @return string
     */
    public function url($match, $params = [], $type = \Magelight\Helpers\UrlHelper::TYPE_HTTP, $addOnlyMaskParams = false)
    {
        return \Magelight\Helpers\UrlHelper::getInstance()->getUrl($match, $params, $type, $addOnlyMaskParams);
    }

    /**
     * Pasre int to date
     *
     * @param int $value
     * @return string
     */
    public function date($value)
    {
        $dateFormat = \Magelight::app()->getConfig('global/view/date_format', 'Y-m-d');
        return date($dateFormat, $value);
    }

    /**
     * int to date and time
     *
     * @param int $value
     * @return string
     */
    public function dateTime($value)
    {
        $dateFormat = \Magelight::app()->getConfig('global/view/date_time_format', 'Y-m-d H:i:s');
        return date($dateFormat, $value);
    }

    /**
     * Custom mask int to dateTime conversion
     *
     * @param string $dateFormat
     * @param int $value
     * @return string
     */
    public function dateTimeCustom($dateFormat, $value)
    {
        return date($dateFormat, $value);
    }

    /**
     * Escape HTML special chars in text
     *
     * @param string $text
     * @return string
     */
    public function escapeHtml($text)
    {
        return static::escapeHtmlStatic($text);
    }

    /**
     * Escape HTML special chars in text
     *
     * @param string $text
     * @return string
     */
    public static function escapeHtmlStatic($text)
    {
        return htmlspecialchars($text);
    }


    /**
     * Truncate text
     *
     * @param string $text
     * @param int $length
     * @param string $addOn
     * @param string $encoding
     * @return string
     */
    public function truncate($text, $length, $addOn = '', $encoding = 'utf-8')
    {
        return mb_strimwidth($text, 0, $length + strlen($addOn), $addOn, $encoding);
    }

    /**
     * Truncate text preserving last word
     *
     * @param string $text
     * @param int $length
     * @param string $addOn
     * @param string $encoding
     * @return string
     */
    public function truncatePreserveWords($text, $length, $addOn = '', $encoding = 'utf-8')
    {
        if ($length == 0) {
            return '';
        } else {
            $startPos = $length - 1;
            $startPos = min($startPos, mb_strlen($text, $encoding));
            $length = mb_strpos($text, ' ', $startPos, $encoding);
            if (!$length) {
                $length = $startPos;
            }
        }
        return $this->truncate($text, $length, $addOn, $encoding);
    }

    /**
     * Load block perspective from config node
     *
     * @param string $perspective - path to perspective in config
     * @return Block
     */
    public function loadPerspective($perspective = 'global/perspectives/default')
    {
        return $this->_processPerspective(\Magelight::app()->config()->getConfig($perspective));
    }

    /**
     * Process loaded perspective
     *
     * @param \SimpleXMLElement $perspective
     * @return Block
     */
    protected function _processPerspective(\SimpleXMLElement $perspective)
    {
        foreach ($perspective->sections->children() as $sectionName => $node)
        {
            foreach ($node->block as $block) {
                $block = '\\' . trim((string)$block, '\\/');
                $this->sectionAppend($sectionName, call_user_func([$block, 'forge']));
            }
            if (isset($node->sections)) {
                $this->_processPerspective($node);
            }
        }
        return $this;
    }
}

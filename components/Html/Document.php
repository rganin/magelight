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
 * @uthor $$author_placeholder_notice$$
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Html;

class Document
{
    /**
     * Doctypes
     */
    const DOCTYPE_HTML5 = 
        '<!DOCTYPE HTML>';
    
    const DOCTYPE_HTML4_STRICT = 
        '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
    
    const DOCTYPE_HTML4_TRANSITIONAL = 
        '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
    
    const DOCTYPE_HTML4_FRAMESET = 
        '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
    
    const DOCTYPE_XHTML_1_STRICT = 
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
    
    const DOCTYPE_XHTML_1_TRANSITIONAL = 
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    
    const DOCTYPE_XHTML_1_FRAMESET = 
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
    
    const DOCTYPE_XHTML_1_1 = 
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
 
    /**
     * Language
     */
    const DEFAULT_LANGUAGE = 'en';
       
    /**
     * HTML Language
     * 
     * @var string
     */
    protected $_language = self::DEFAULT_LANGUAGE;
 
    /**
     * Doctype
     * 
     * @var string
     */
    protected $_docType = self::DOCTYPE_HTML5;
    
    /**
     * Head object
     * 
     * @var Head
     */
    protected $_head = null;
    
    /**
     * Body object
     * 
     * @var Body
     */
    protected $_body = null; 
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_head = new Head();
        $this->_body = new Body();
    }
    
    /**
     * Set doctype
     * 
     * @param string $docType
     * @return Document
     */
    public function setDocType($docType = self::DOCTYPE_HTML5)
    {
        if (empty($docType)) {
            $this->_docType = self::DOCTYPE_HTML5; 
        } else {
            $this->_docType = $docType;
        }
        return $this;
    }
    
    /**
     * Set language
     * 
     * @param string $lang
     * @return Document
     */
    public function setLanguage($lang = self::DEFAULT_LANGUAGE)
    {
        $this->_language = empty($lang) ? self::DEFAULT_LANGUAGE : $lang;
        return $this;
    }
    
    /**
     * Render document
     * 
     * @return string
     */
    public function render()
    {
        $tag = new Tag('html');
        $tag->addAttribute('lang', $this->_language);
        $tag->setContent($this->_head->render() . $this->_body->render());
        return $this->_docType . $tag->render();
    }
    
    /**
     * Get document head
     * 
     * @return Head
     */
    public function head()
    {
        return $this->_head;
    }
    
    /**
     * Get document body
     * 
     * @return Body
     */
    public function body()
    {
        return $this->_body;
    }
    
    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->_docType);
        unset($this->_language);
        unset($this->_body);
        unset($this->_head);
    }
}
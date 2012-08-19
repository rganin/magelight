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

class Document extends \Bike\Block
{
    /**
     * Default document registry index
     */
    const DEFAULT_REGISTRY_INDEX = 'document';
    
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
     * Doctype
     * 
     * @var string
     */
    protected $_doctype = self::DOCTYPE_HTML5;

    /**
     * Template
     * 
     * @var string
     */
    protected $_template = 'document.phtml';

    /**
     * Sections
     * 
     * @var array
     */
    protected $_sections = array(
        'head' => 'Core\\Blocks\\Head',
        'body' => 'Core\\Blocks\\Body',
    );
    
    protected function __construct()
    {
        $this->_sections = array(
            'head' => \Bike\Html\Head::create(),
            'body' => 'Bike\\Html\\Body',
        );
        $this->setRegistryObject(self::DEFAULT_REGISTRY_INDEX, $this);
    }
    
    /**
     * Set doctype
     * 
     * @param string $docType
     *
     * @return Document
     */
    public function setDocType($docType = self::DOCTYPE_HTML5)
    {
        $this->_doctype = $docType;
        return $this;
    }

    /**
     * Get head block
     * 
     * @return \Bike\Html\Head 
     */
    public function head()
    {
        return $this->_sections['head'];
    }
}
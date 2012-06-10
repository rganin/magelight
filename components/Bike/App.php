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

namespace Bike;

class App
{
    /**
     * Documents array
     * 
     * @var array[\Html\Document]
     */
    protected $_documents = array();
    
    /**
     * Get document
     * 
     * @param string $index
     * @return \Html\Document
     */
    public function document($index = 'default')
    {
        if (!isset($this->_documents[$index])) {
            $this->_documents[$index] = new \Html\Document();
        }
        return $this->_documents[$index];
    }
    
    public function router()
    {
        
    }
    
    public function run()
    {
        
    }
}
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

namespace Bike\Loaders;

class Config
{
    /**
     * Configuration
     * 
     * @var array
     */
    protected $_config = array();

    /**
     * Load configuration and merge it
     * 
     * @param $filename
     *
     * @return Config
     */
    public function loadConfig($filename)
    {
        $xmlHelper = \Bike::helper('xml');
        /* @var \Bike\Helpers\XmlHelper $xmlHelper*/
        $this->_config = array_replace_recursive(
            $this->_config, 
            $xmlHelper->xmlToArray(new \SimpleXMLIterator(file_get_contents($filename)))
        );
        return $this;
    }

    /**
     * Get loaded Configuration
     * 
     * @return array
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->_config);
    }
}
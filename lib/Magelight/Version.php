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
 * Version wrapper class
 */
class Version
{
    /**
     * Version as string
     * @va string
     */
    protected $_versionString;

    /**
     * Version as array
     *
     * @var array
     */
    protected $_versionStruct;

    /**
     * Constructor
     *
     * @param string $version
     */
    public function __construct($version)
    {
        $this->_versionString = $version;
        $this->_buildStruct($version);
    }

    /**
     * Version to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_versionString;
    }

    /**
     * Convert version string to structure
     *
     * @param string $version
     */
    protected function _buildStruct($version)
    {
        $version = preg_replace('/[^0-9\.]/', '.', $version);
        $version = preg_replace('/(\.)(?![^\.])/', '', $version);
        $this->_versionStruct = explode('.', $version);
    }

    /**
     * Get version structure
     *
     * @return array
     */
    public function getVersionStruct()
    {
        return $this->_versionStruct;
    }

    /**
     * Is version equal to given one
     *
     * @param string|Version $version
     * @return bool
     */
    public function isEqual($version)
    {
        $versionStruct = $this->_prepareVersionToCompareStruct($version);
        foreach ($this->_versionStruct as $key => $versionNum) {
            if (!isset($versionStruct[$key]) || $versionStruct[$key] !== $versionNum) {
                return false;
            }
        }
        return true;
    }

    /**
     * Is version lower than given one
     *
     * @param string|Version $version
     * @return bool
     */
    public function isLowerThan($version)
    {
        $versionStruct = $this->_prepareVersionToCompareStruct($version);
        foreach ($this->_versionStruct as $key => $versionNum) {
            if (!isset($versionStruct[$key])) {
                $versionStruct[$key] = 0;
            }
            if ($versionStruct[$key] > $versionNum) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is version greater than given one
     *
     * @param string|Version $version
     * @return bool
     */
    public function isGreaterThan($version)
    {
        $versionStruct = $this->_prepareVersionToCompareStruct($version);
        foreach ($this->_versionStruct as $key => $versionNum) {
            if (!isset($versionStruct[$key])) {
                $versionStruct[$key] = 0;
            }
            if ($versionStruct[$key] < $versionNum) {
                return true;
            }
        }
        return false;
    }

    /**
     * Prepare version structure for comparison
     *
     * @param string|Version $version
     * @return array
     * @throws Exception
     */
    protected function _prepareVersionToCompareStruct($version)
    {
        if (!$version instanceof \Magelight\Version) {
            if (!is_string($version)) {
                throw new \Magelight\Exception('Version must be a string or instance of ' . __CLASS__);
            }
            $version = new \Magelight\Version($version);
        }
        return $version->getVersionStruct();
    }
}

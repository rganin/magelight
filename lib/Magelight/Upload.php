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
 * @copyright Copyright (c) 2013 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight;

/**
 * @method static \Magelight\Upload forge($data = [])
 */
class Upload
{
    /**
     * Using forgery
     */
    use \Magelight\Traits\TForgery;

    /**
     * Uploaded file data
     *
     * @var array
     */
    protected $fileData = [];

    /**
     * Allowed upload extensions
     *
     * @var array
     */
    protected $allowedExtensions = [];

    /**
     * Restricted upload extensions
     *
     * @var array
     */
    protected $restrictedExtensions = [];

    /**
     * Is upload object moved and saved to target path
     *
     * @var bool
     */
    protected $isSaved = false;

    /**
     * Path where upload object was saved to
     *
     * @var string
     */
    protected $savedPath;

    /**
     * Forgery constructor
     *
     * @param array $data
     */
    public function __forge($data = [])
    {
        $this->setFileData($data);
    }

    /**
     * Set uploaded file data
     *
     * @param array $data
     * @return Upload
     */
    public function setFileData($data = [])
    {
        $this->fileData = $data;
        return $this;
    }

    /**
     * Get upload name
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->fileData['name']) ? $this->fileData['name'] : null;
    }

    /**
     * Get upload TMP name
     *
     * @return string|null
     */
    public function getTmpName()
    {
        return isset($this->fileData['tmp_name']) ? $this->fileData['tmp_name'] : null;
    }

    /**
     * Get upload size
     *
     * @return int|null
     */
    public function getSize()
    {
        return isset($this->fileData['size']) ? $this->fileData['size'] : null;
    }

    /**
     * Get upload error code
     *
     * @return int|null
     */
    public function getError()
    {
        return isset($this->fileData['error']) ? $this->fileData['error'] : null;
    }

    /**
     * Get upload mime type
     *
     * @return string|null
     */
    public function getType()
    {
        return isset($this->fileData['type']) ? $this->fileData['type'] : null;
    }

    /**
     * Get file name extension
     *
     * @return mixed|null
     */
    public function getExtension()
    {
        return isset($this->fileData['name']) ? pathinfo($this->fileData['name'], PATHINFO_EXTENSION) : null;
    }

    /**
     * Save upload to file
     *
     * @param string $path
     * @param string|null $filename
     * @param bool $createPath
     * @param int $mode
     * @return bool
     */
    public function saveTo($path, $filename = null, $createPath = true, $mode = 0755)
    {
        if (empty($filename)) {
            $filename = $this->getName();
        }
        $path = str_ireplace('\\/', DS, rtrim($path, '\\/') . DS . $filename);
        $dir = dirname($path);
        if ($createPath && !file_exists($dir)) {
            mkdir($dir, $mode, true);
        }
        if (!is_writable($dir)) {
            trigger_error("Moving upload failed. Path {$dir} is not writable.", E_USER_WARNING);
        }
        if (move_uploaded_file($this->getTmpName(), $path)) {
            chmod($path, $mode);
            $this->isSaved = true;
            $this->savedPath = $path;
            return true;
        }
        return false;
    }

    /**
     * Is upload saved and moved to target path
     *
     * @return bool
     */
    public function isSaved()
    {
        return $this->isSaved;
    }

    /**
     * Get path where object was saved to
     *
     * @return string
     */
    public function getSavedPath()
    {
        return $this->savedPath;
    }

    /**
     * Check does upload have allowed extension
     *
     * @param array $allowedExtensions
     * @return bool
     */
    public function hasAllowedExtension(array $allowedExtensions = [])
    {
        if (empty($allowedExtensions)) {
            $allowedExtensions = $this->allowedExtensions;
        }
        $filename = $this->getName();
        foreach ($allowedExtensions as $ext) {
            if (preg_match('/^.*' . $ext . '$/i', $filename)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check does upload have restricted extension
     *
     * @param array $restrictedExtensions
     * @return bool
     */
    public function hasRestrictedExtension(array $restrictedExtensions = [])
    {
        if (empty($restrictedExtensions)) {
            $restrictedExtensions = $this->restrictedExtensions;
        }
        $filename = $this->getName();
        foreach ($restrictedExtensions as $ext) {
            if (preg_match('/^.*' . $ext . '$/i', $filename)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Set allowed upload extensions
     *
     * @param array $extensions
     * @return $this
     */
    public function setAllowedExtensions(array $extensions)
    {
        $this->allowedExtensions = $extensions;
        return $this;
    }

    /**
     * Set restricted upload extensions
     *
     * @param array $extensions
     * @return $this
     */
    public function setRestrictedExtensions(array $extensions)
    {
        $this->restrictedExtensions = $extensions;
        return $this;
    }
}

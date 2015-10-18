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
namespace Magelight\Http;

/**
 * Files array wrapper class
*/
class FilesArray extends \ArrayObject
{
    const I_NAME      = 'name';
    const I_TEMP_NAME = 'tmp_name';
    const I_SIZE      = 'size';
    const I_TYPE      = 'type';
    const I_ERROR     = 'error';

    /**
     * Fetch current element
     *
     * @return FilesArray
     */
    public function current() {
        return $this->normalize(parent::current());
    }

    /**
     * Get offset
     *
     * @param mixed $offset
     * @return FilesArray|mixed
     */
    public function offsetGet($offset) {
        return $this->normalize(parent::offsetGet($offset));
    }

    /**
     * Normalize the PHP platform developer`s idiotism
     *
     * @param $entry
     * @return FilesArray
     */
    protected function normalize($entry) {
        if(isset($entry[self::I_NAME]) && is_array($entry[self::I_NAME])) {
            $files = array();
            foreach($entry[self::I_NAME] as $k => $name) {
                $files[$k] = array(
                    self::I_NAME       => $name,
                    self::I_TEMP_NAME  => $entry[self::I_TEMP_NAME][$k],
                    self::I_SIZE       => $entry[self::I_SIZE][$k],
                    self::I_TYPE       => $entry[self::I_TYPE][$k],
                    self::I_ERROR      => $entry[self::I_ERROR][$k]
                );
            }
            return new self($files);
        }
        return $entry;
    }
}

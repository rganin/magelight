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

abstract class Collection
{
    /**
     * Current position
     * 
     * @var int
     */
    protected $position = 0;
    
    /**
     * Items 
     * 
     * @var array
     */
    protected $items = [];
    
    /**
     * Constructor
     * 
     * @param array $items
     */
    public function __construct($items = [])
    {
        $this->items = $items;
        $this->position = 0;
    }
    
    /**
     * Set current position
     * 
     * @param $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }
    
    /**
     * Get current position
     * 
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }
    
    /**
     * Get item from collection
     * 
     * @param int $position
     * @param mixed $default
     * @return mixed
     */
    public function getItem($position, $default = null)
    {
        return isset($this->items[$position]) ? $this->items[$position] : $default;
    }
    
    /**
     * Get next item from collection
     * 
     * @param mixed $default
     * @return mixed
     */
    public function getNextItem($default = null)
    {
        return isset($this->items[$this->position + 1]) ? $this->items[$this->position + 1] : $default; 
    }
    
    /**
     * Get previous item from collection
     * 
     * @param mixed $default
     * @return mixed
     */
    public function getPrevItem($default = null)
    {
        return isset($this->items[$this->position - 1]) ? $this->items[$this->position - 1] : $default;
    }
    
    /**
     * Go to next item
     * 
     * @return Collection
     */
    public function goNext()
    {
        $this->position++;
        return $this;
    }
    
    /**
     * Go to previous item
     * 
     * @return Collection
     */
    public function goPrev()
    {
        $this->position--;
        return $this;
    }
    
    /**
     * Add item to collection
     * 
     * @param mixed $item
     * @return Collection
     */
    public function addItem($item)
    {
        $this->items[] = $item;
        return $this;
    }
    
    /**
     * Delete item from collection
     * 
     * @param int $index
     * @return Collection
     */
    public function delItem($index)
    {
        $this->items[$index] = null;
        unset($this->items[$index]);
        return $this;
    }
}
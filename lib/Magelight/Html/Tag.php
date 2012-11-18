<?php

namespace Magelight\Html;

class Tag
{
    const DEFUALT_TYPE = 'div';

    protected $type = self::DEFUALT_TYPE;

    protected $empty = false;

    protected $attributes = [];

    protected $content = '';

    public function __construct($type = self::DEFUALT_TYPE, $empty = false)
    {
        $this->type = $type;
        $this->empty = $empty;
    }

    public function addAttribute($name, $value = null)
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    public function delAttribute($name)
    {
        unset($this->attributes[$name]);
        return $this;
    }

    public function getAttribute($name, $default = null)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
    }

    public function setEmpty($empty = true)
    {
        $this->empty = $empty;
        $this->content = '';
        return $this;
    }

    public function setContent($content = '')
    {
        $this->content = $content;
        return $this;
    }
}

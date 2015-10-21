<?php

namespace Magelight\Webform\Blocks\Elements;

/**
 * Class InputAppended
 * @package Magelight\Webform\Blocks\Elements
 *
 * @method static $this forge()
 */
class InputPrepended extends Input
{
    /**
     * @var Abstraction\Element
     */
    protected $addon;

    /**
     * @var Abstraction\Element
     */
    protected $wrapper;

    /**
     * {@inheritdoc}
     */
    public function __forge()
    {
        $this->addon = Abstraction\Element::forge()->setClass('input-group-addon')->setTag('span');
        $this->wrapper = Abstraction\Element::forge()->setClass('input-group');
        parent::__forge();
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        $this->wrapper->addContent($this->addon);
        $this->wrapper->addContent(parent::toHtml());
        return $this->wrapper->toHtml();
    }

    /**
     * Add addon content
     *
     * @param $content
     * @return $this
     */
    public function addAddonContent($content)
    {
        $this->addon->addContent($content);
        return $this;
    }

    /**
     * Set addon content
     *
     * @param $content
     * @return $this
     */
    public function setAddonContent($content)
    {
        $this->addon->setContent($content);
        return $this;
    }

    /**
     * Get wrapper instance
     *
     * @return Abstraction\Element
     */
    public function getWrapper()
    {
        return $this->wrapper;
    }

    /**
     * Get wrapper instance
     *
     * @return Abstraction\Element
     */
    public function getAddon()
    {
        return $this->addon;
    }
}

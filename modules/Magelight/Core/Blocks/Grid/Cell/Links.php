<?php

namespace Magelight\Core\Blocks\Grid\Cell;

use Magelight\Core\Blocks\Grid\Cell;

class Links extends Cell
{
    protected $tag = 'div';

    /**
     * @var Link[]
     */
    protected $links = [];

    /**
     * @param Link $link
     * @return $this
     */
    public function addLink(Link $link)
    {
        $this->links[] = $link;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resetState()
    {
        $this->setContent('');
        return parent::resetState();
    }

    /**
     * {@inheritdoc}
     */
    public function beforeToHtml()
    {
        foreach ($this->links as $link) {
            $this->addContent($link);
            $this->addContent('&nbsp;');
            $link->data = $this->data;
        }
        return parent::beforeToHtml();
    }

    /**
     * Use following fields from row data on render
     *
     * @param array $fields - ['field1', 'alias_2' => 'field_2', 'field_3' ...]
     *
     * @return $this
     */
    public function useFields(array $fields = [])
    {
        $this->useFields = $fields;
        foreach ($this->links as $link) {
            $link->useFields($fields);
        }
        return $this;
    }
}

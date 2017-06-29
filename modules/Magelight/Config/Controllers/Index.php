<?php

namespace Magelight\Config\Controllers;


use Magelight\Config;

class Index extends \Magelight\Admin\Controllers\Base
{
    public function beforeExecute()
    {
        parent::beforeExecute();
        $this->breadcrumbsBlock->addBreadcrumb(__('Config'), 'admin/config');
        return $this;
    }

    public function indexAction()
    {
        $config = Config::getInstance()->getConfigData();
        $array = $this->walkXml($config);
        $this->view()->sectionReplace('content', 'config content');
        $this->renderView();
    }

    protected function walkXml(\SimpleXMLElement $element, $path = '/', &$targetArray = [])
    {
        if (empty($element->children())) {
            $targetArray[$path] = (string)$element;
        } else {
            /** @var \SimpleXMLElement $child */
            foreach ($element->children() as $child) {
                $this->walkXml($child, $path . '/' . $child->getName(), $targetArray);
            }
        }
        return $targetArray;
    }
}

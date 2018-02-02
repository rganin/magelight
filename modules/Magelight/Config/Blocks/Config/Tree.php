<?php

namespace Magelight\Config\Blocks\Config;

use Magelight\Block;
use Magelight\Core\Blocks\Document;

class Tree extends Block
{
    /**
     * @inheritdoc
     */
    protected $template = 'Magelight/Config/templates/config/tree.phtml';

    /**
     * @inheritdoc
     */
    public function initBlock()
    {
        Document::getInstance()->addCss('Magelight/Config/static/css/bootstrap-treeview.min.css');
        Document::getInstance()->addJs('Magelight/Config/static/js/bootstrap-treeview.min.js');
        return parent::initBlock();
    }

    /**
     * Build menu tree for frontend rendering
     *
     * @return mixed
     */
    public function buildMenuTreeArray()
    {
        /** @var \Magelight\Config\Models\Config $config */
        $config = \Magelight\Config\Models\Config::getInstance();
        $menuTree = [];
        $editableNodes = $config->getEditableNodes(\Magelight\Config::getInstance()->getConfigData());
        foreach ($editableNodes as $nodePath => $node) {
            $nodePathArray = explode('/', ltrim($nodePath, '/'));
            array_pop($nodePathArray);
            $path = '/';
            $menu = &$menuTree;
            foreach ($nodePathArray as $nodePathPart) {
                $path .= '/' . $nodePathPart;
                $element = \Magelight\Config::getInstance()->getConfig($path);
                $nodeName = (string)$element->getName();
                if (!isset($menu[$nodeName])) {
                    $attributes = $config->getElementAttributes($element);
                    $menu[$nodeName] = [
                        'text' => isset($attributes['title']) ? $attributes['title'] : $nodeName,
                        'path' => $path,
                        'nodes' => []
                    ];
                }
                $menu = &$menu[$nodeName]['nodes'];
            }
        }
        $menuTree = $this->adapterWalkMenu(['nodes' => $menuTree]);
        return $menuTree['nodes'];
    }

    /**
     * Change menu to correspond the frontend menu renderer format
     *
     * @param array $arr
     * @return array
     */
    public function adapterWalkMenu($arr)
    {
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $arr[$key] = $this->adapterWalkMenu($value);
            }
        }
        if (isset($arr['nodes'])) {
            $arr['nodes'] = array_values($arr['nodes']);
            if (empty($arr['nodes'])) {
                unset($arr['nodes']);
                $arr['href'] = $this->url('admin/config', ['path' => $arr['path']]);
            } else {
                $arr['selectable'] = false;
            }
        }
        return $arr;
    }
}

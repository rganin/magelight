<?php

namespace Magelight\Config\Blocks\Config;

use Magelight\Config;
use Magelight\Webform\Blocks\Elements\Input;
use Magelight\Webform\Blocks\Fieldset;

/**
 * Class Form
 * @package Magelight\Config\Blocks\Config
 *
 * @method static $this forge($configPath) - forge a webform
 */
class Form extends \Magelight\Webform\Blocks\Form
{
    public function __forge($configPath)
    {
        $config = Config::getInstance()->getConfig($configPath);
        $editableNodes = Config\Models\Config::getInstance()->getEditableNodes($config);
        $fieldset = Fieldset::forge();
        $fieldset->setLegend(Config::getInstance()->getConfigAttribute($configPath, 'title'));
        /** @var \SimpleXMLElement $node */
        foreach ($editableNodes as $path => $node) {
            $name = ltrim($path, '/');
            $fieldset->addRowField(
                Input::forge()->setName('config[' . $configPath . '/' . $name . '][value]')->setValue($node['value']),
                isset($node['attributes']['label']) ? $node['attributes']['label'] : $name,
                isset($node['attributes']['hint']) ? $node['attributes']['hint'] : ''
            );
        }

        $this->addFieldset($fieldset);
    }
}

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 12.12.12
 * Time: 1:15
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Minifier\Blocks;

class Document extends \Magelight\Core\Blocks\Document
{
    public function renderCss()
    {
        $css = $this->buildDependencies($this->get('css', []));
        $this->set('css', \Magelight\Minifier\Models\Minifier::forge()->getMinifiedCss($css));
        return parent::renderCss();
    }

    public function renderJs()
    {
        $js = $this->buildDependencies($this->get('js', []));
        $this->set('js', \Magelight\Minifier\Models\Minifier::forge()->getMinifiedJs($js));
        return parent::renderJs();
    }
}
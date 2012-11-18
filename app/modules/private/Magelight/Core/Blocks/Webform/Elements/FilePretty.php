<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 18.11.12
 * Time: 14:51
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Core\Blocks\Webform\Elements;

/**
 * @method static \Magelight\Core\Blocks\Webform\Elements\FilePretty forge($title = null)
 */
class FilePretty extends Abstraction\Field
{
    public function __forge()
    {
        $this->setButtonTitle('Select file');
    }

    public function setButtonTitle($title = null)
    {
        $this->set('title', empty($title) ? 'Select file' : $title);
    }

    protected $_template = 'Magelight/Core/templates/webform/elements/file-pretty.phtml';
}

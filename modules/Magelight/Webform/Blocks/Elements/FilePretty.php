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
 * @copyright Copyright (c) 2012-2015 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Webform\Blocks\Elements;

/**
 * @method static \Magelight\Webform\Blocks\Elements\FilePretty forge($title = null)
 */
class FilePretty extends Abstraction\Field
{
    /**
     * Template path
     *
     * @var string
     */
    protected $template = 'Magelight/Webform/templates/webform/elements/file-pretty.phtml';

    /**
     * Forgery constructor
     */
    public function __forge()
    {
        $this->setButtonTitle();
        $this->addClass('form-control');
    }

    /**
     * Set button title
     *
     * @param string $title
     */
    public function setButtonTitle($title = null)
    {
        $this->set('title', empty($title) ? 'Select file' : $title);
    }
}

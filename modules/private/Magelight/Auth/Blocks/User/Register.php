<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 02.12.12
 * Time: 22:47
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Auth\Blocks\User;

/**
 * @method static \Magelight\Auth\Blocks\User\Register forge()
 */
class Register extends \Magelight\Block
{
    /**
     * Register form template
     *
     * @var string
     */
    protected $_template = 'Magelight/Auth/templates/user/register.phtml';

    /***
     * Init overide
     *
     * @return \Magelight\Block|void
     */
    public function init()
    {
        \Magelight\Core\Blocks\Document::getFromRegistry()->sectionReplace(
            'ulogin-widget-register',
            \Magelight\Auth\Blocks\UloginWidget::forge()->setConfigIndex('register')
        );
    }
}

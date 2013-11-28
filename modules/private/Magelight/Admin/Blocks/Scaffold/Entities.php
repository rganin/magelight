<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rganin
 * Date: 11.10.13
 * Time: 16:30
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Admin\Blocks\Scaffold;

/**
 * Class Entities
 * @package Magelight\Admin\Blocks
 *
 * @method static \Magelight\Admin\Blocks\Scaffold\Entities forge()
 */
class Entities extends \Magelight\Block
{
    protected $_template = 'Magelight/Admin/templates/scaffold/entities.phtml';

    protected $_scaffold;

    public function __forge()
    {
        $this->_scaffold = \Magelight\Admin\Models\Scaffold\Scaffold::forge();
        $this->entities = $this->_scaffold->loadEntities();
    }
}
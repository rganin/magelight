<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rganin
 * Date: 11.10.13
 * Time: 16:30
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Scaffold\Blocks;

/**
 * Class Entities
 * @package Magelight\Scaffold\Blocks
 *
 * @method static \Magelight\Scaffold\Blocks\Entities forge()
 */
class Entities extends \Magelight\Block
{
    protected $_template = 'Magelight/Scaffold/templates/entities.phtml';

    protected $_scaffold;

    public function __forge()
    {
        $this->_scaffold = \Magelight\Scaffold\Models\Scaffold::forge();
        $this->entities = $this->_scaffold->loadEntities();
    }
}
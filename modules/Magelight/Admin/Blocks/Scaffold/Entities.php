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
 * @copyright Copyright (c) 2013 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
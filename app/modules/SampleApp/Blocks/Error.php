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

namespace SampleApp\Blocks;

/**
 * Class Error
 * @package SampleApp\Blocks
 */
class Error extends \Magelight\Block
{
    /**
     * Error templates
     */
    const TEMPLATE_404 = 'SampleApp/templates/errors/404.phtml';
    const TEMPLATE_403 = 'SampleApp/templates/errors/403.phtml';
    const TEMPLATE_401 = 'SampleApp/templates/errors/401.phtml';

    /**
     * @var string
     */
    protected $template = self::TEMPLATE_404;
}

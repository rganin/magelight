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

namespace Magelight\Visitors\Blocks;

/**
 * Class VisitorsSummary
 *
 * @package Magelight\Visitors\Blocks
 */
class VisitorsSummary extends \Magelight\Block
{
    /**
     * Template to render
     *
     * @var string
     */
    protected $template = 'Magelight/Visitors/templates/visitors-summary.phtml';

    /**
     * Forgery constructor
     */
    public function __forge()
    {
        $model = \Magelight\Visitors\Models\Visitor::forge();
        $this->total_count = $model->orm()->totalCount();
        $time = time();
        $todayStart = $time - ($time % 86400);
        $todayEnd = $todayStart + 86400;
        $this->today_count = $model->orm()->whereGt('time', $todayStart)->whereLt('time', $todayEnd)->totalCount();
    }
}

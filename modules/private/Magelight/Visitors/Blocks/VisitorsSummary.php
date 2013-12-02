<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 01.12.13
 * Time: 20:06
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Visitors\Blocks;

class VisitorsSummary extends \Magelight\Block
{
    protected $_template = 'Magelight/Visitors/templates/visitors-summary.phtml';

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
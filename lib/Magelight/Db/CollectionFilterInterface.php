<?php
/**
 * Created by PhpStorm.
 * User: rganin
 * Date: 05.01.2016
 * Time: 12:12
 */

namespace Magelight\Db;

interface CollectionFilterInterface
{
    /**
     * Get filter expression
     *
     * @return null|Common\Expression\ExpressionInterface
     */
    public function getFilterExpression();
}

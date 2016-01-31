<?php
/**
 * Created by PhpStorm.
 * User: rganin
 * Date: 04.01.2016
 * Time: 18:46
 */

namespace Magelight\Db\Common\Expression;

interface ExpressionInterface
{
    public function __toString();

    public function getParams();

    public function isEmpty();
}

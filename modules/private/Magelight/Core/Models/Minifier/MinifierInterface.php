<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 12.12.12
 * Time: 2:23
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Core\Models\Minifier;

interface MinifierInterface
{
    public function minify($buffer);
}
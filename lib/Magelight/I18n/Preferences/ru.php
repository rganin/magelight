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

return [
    'plural_function' => function ($n) {
        return (int)($n % 10 == 1 && $n % 100 != 11 ? 0 : ($n %10 >= 2 && $n %10 <= 4 && ($n % 100 < 10 || $n %100 >= 20) ? 1 : 2)) + 1;
    },
    'plural_forms' => 3
];

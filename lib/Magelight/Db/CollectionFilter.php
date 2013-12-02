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

namespace Magelight\Db;

/**
 * @method static \Magelight\Db\CollectionFilter forge(array $FilterData = [])
 */
class CollectionFilter
{
    use \Magelight\Traits\TForgery;

    /**
     * Filter data set
     *
     * @var array
     */
    protected $_filterData = [];

    /**
     * Corgery constructor
     *
     * @param array $filterData
     */
    public function __forge(array $filterData = [])
    {
        $this->_filterData = $filterData;
    }

    /**
     * Get filter methods array
     *
     * @return array
     */
    public function getFilterMethods()
    {
        $methods = [];
        foreach ($this->_filterData as $statement => $params) {
            foreach ($params as $field => $value) {
                $field = preg_replace('/[^a-z0-9\._-]+/i', '', $field);
                if (Common\Orm::isWhereStatement($statement)) {
                    if (!empty($value)) {
                        $methods[] = [
                            'statement' => $statement,
                            'field'     => $field,
                            'value'     => $value,
                        ];
                    }
                }
            }
        }
        return $methods;
    }
}

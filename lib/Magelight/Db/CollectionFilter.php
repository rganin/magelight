<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 03.01.13
 * Time: 20:59
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Db;

/**
 * @method static \Magelight\Db\CollectionFilter forge(array $FilterData = [])
 */
class CollectionFilter
{
    use \Magelight\TForgery;

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

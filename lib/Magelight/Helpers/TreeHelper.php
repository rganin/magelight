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

namespace Magelight\Helpers;

/**
 * Class TreeHelper
 * @package Magelight\Helpers
 *
 * @method static \Magelight\Helpers\TreeHelper forge()
 */
class TreeHelper
{
    use \Magelight\Traits\TForgery;

    /**
     * Build tree from flat array of elements containing ID and PARENT_ID
     *
     * @param array $dataset
     * @param int   $parentIdStart
     * @param string $parentIndex
     * @param string $idIndex
     * @param string $childrenIndex
     * @return array
     */
    public function buildTree($dataset,
                       $parentIdStart = 0,
                       $parentIndex = 'parent_id',
                       $idIndex = 'id',
                       $childrenIndex = 'children'
    ){
        $parentsArray = [];
        foreach ($dataset as $value) {
            $parentsArray[$value[$parentIndex]][$value[$idIndex]] = $value;
        }
        $tree = $parentsArray[$parentIdStart];
        $this->createTree($tree, $parentsArray, $childrenIndex);
        return $tree;
    }

    /**
     * Create tree from array recursively
     *
     * @param array $tree
     * @param array $parentsArray
     * @param string $childrenIndex
     */
    protected function createTree(&$tree, $parentsArray, $childrenIndex){
        foreach ($tree as $key => $value) {
            if(!isset($value[$childrenIndex])) {
                $tree[$key][$childrenIndex] = [];
            }
            if(array_key_exists($key, $parentsArray)){
                $tree[$key][$childrenIndex] = $parentsArray[$key];
                $this->createTree($tree[$key][$childrenIndex], $parentsArray, $childrenIndex);
            }
        }
    }
}

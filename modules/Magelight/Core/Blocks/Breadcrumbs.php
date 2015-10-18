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

namespace Magelight\Core\Blocks;

/**
 * Breadcrumbs page block
 *
 * @method static \Magelight\Core\Blocks\Breadcrumbs forge()
 */
class Breadcrumbs extends \Magelight\Block
{
    /**
     * Active bradcrumb CSS class name
     */
    const CLASS_ACTIVE = 'active';

    /**
     * @var string
     */
    protected $template = "Magelight/Core/templates/breadcrumbs.phtml";

    /**
     * Forgery constructor
     */
    public function __forge()
    {
        $this->breadcrumbs = [];
    }

    /**
     * Add breadcrumb
     *
     * @param string $title
     * @param string|null $routeMatch
     * @param array $routeParams
     * @param string $class
     * @return $this
     */
    public function addBreadcrumb($title, $routeMatch = null, $routeParams = [], $class = '')
    {
        $breadcrumbs = $this->breadcrumbs;
        $breadcrumbs[] = [
            'route_match'  => $routeMatch,
            'route_params' => $routeParams,
            'title'        => $title,
            'class'        => $class
        ];
        $this->breadcrumbs = $breadcrumbs;
        return $this;
    }
}

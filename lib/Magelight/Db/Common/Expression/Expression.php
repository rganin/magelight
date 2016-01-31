<?php
/**
 * Created by PhpStorm.
 * User: rganin
 * Date: 04.01.2016
 * Time: 18:46
 */

namespace Magelight\Db\Common\Expression;

use Magelight\Traits\TForgery;

/**
 * Class Expression
 * @package Magelight\Db\Common\Expression
 *
 * @method static Expression forge($expression = '', $params = [])
 */
class Expression implements ExpressionInterface
{
    use TForgery;

    /**
     * Expression string
     *
     * @var string
     */
    protected $expression = '';

    /**
     * @var array
     */
    protected $params = [];

    /**
     * Constructor
     *
     * @param string $expression
     * @param array $params
     */
    public function __forge($expression = '', $params = [])
    {
        $this->expression = $expression;
        $this->setParams($params);
    }

    /**
     * Set expression
     *
     * @param string|ExpressionInterface $expression
     *
     * @return $this
     */
    public function setExpression($expression)
    {
        $this->expression = $expression;
        return $this;
    }

    /**
     * Set parameters
     *
     * @param array $params
     *
     * @return $this
     */
    public function setParams($params = [])
    {
        $this->params = [];
        return $this->pushParams($params);
    }

    /**
     * Push (add) params
     *
     * @param array $params
     *
     * @return $this
     */
    public function pushParams($params = [])
    {
        if (!is_array($params)) {
            $params = [$params];
        }
        foreach ($params as $p) {
            $this->params[] = $p;
        }
        return $this;
    }

    /**
     * Stringify expression
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->expression;
    }

    /**
     * Get expression params
     *
     * @return array
     */
    public function getParams()
    {
        foreach ($this->params as $key => $param) {
            if ($param instanceof ExpressionInterface) {
                $this->params[$key] = (string)$param;
            }
        }
        return $this->params;
    }

    /**
     * Is empty expression
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->expression);
    }
}

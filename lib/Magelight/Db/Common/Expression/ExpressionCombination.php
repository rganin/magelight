<?php
/**
 * Created by PhpStorm.
 * User: rganin
 * Date: 05.01.2016
 * Time: 0:52
 */

namespace Magelight\Db\Common\Expression;
/**
 * Class Expression
 * @package Magelight\Db\Common\ExpressionCombination
 *
 * @method static $this forge($expression = '', $params = [])
 */
class ExpressionCombination extends Expression
{
    /**
     * Logic constants
     */
    const LOGIC_AND = 'AND';
    const LOGIC_OR = 'OR';
    const LOGIC_AND_NOT = 'AND NOT';
    const LOGIC_OR_NOT = 'OR NOT';

    /**
     * Combination logic
     *
     * @var string
     */
    protected $logic = self::LOGIC_AND;

    /**
     * Combined expressions
     *
     * @var array
     */
    protected $expressions = [];

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $result = '';
        $count = 0;
        if (!count($this->expressions)) {
            return $result;
        } else {
            $result = '(';
            foreach ($this->expressions as $expr) {
                if ($count > 0) {
                    $result .= ' ' . $this->logic . ' ';
                }
                $result .= (string)$expr . ' ';
                $count++;
            }
            $result .= ')';

        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getParams()
    {
        $result = [];
        foreach ($this->expressions as $expr) {
            if ($expr instanceof ExpressionInterface) {
                foreach ($expr->getParams() as $param) {
                    $result[] = $param;
                }
            }
        }
        return $result;
    }

    /**
     * Set expression
     *
     * @param ExpressionInterface $expression
     *
     * @return $this
     */
    public function setExpression($expression)
    {
        $this->expressions = [];
        $this->expressions[] = $expression;
        return $this;
    }

    /**
     * Add expression
     *
     * @param string|ExpressionInterface $expression
     *
     * @return $this
     */
    public function addExpression($expression)
    {
        $this->expressions[] = $expression;
        return $this;
    }

    /**
     * @param $logic
     * @return $this
     */
    public function setLogic($logic)
    {
        $this->logic = $logic;
        return $this;
    }

    /**
     * @return ExpressionCombination
     */
    public function setLogicAnd()
    {
        return $this->setLogic(self::LOGIC_AND);
    }

    /**
     * @return ExpressionCombination
     */
    public function setLogicAndNot()
    {
        return $this->setLogic(self::LOGIC_AND_NOT);
    }

    /**
     * @return ExpressionCombination
     */
    public function setLogicOr()
    {
        return $this->setLogic(self::LOGIC_OR);
    }

    /**
     * @return ExpressionCombination
     */
    public function setLogicOrNot()
    {
        return $this->setLogic(self::LOGIC_OR_NOT);
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        if (!empty($this->expressions)) {
            foreach ($this->expressions as $expression) {
                if ($expression instanceof ExpressionInterface && !$expression->isEmpty()) {
                    return false;
                }
                if (is_string($expression) && !empty($expression)) {
                     return false;
                }
            }
        }
        return true;

    }
}

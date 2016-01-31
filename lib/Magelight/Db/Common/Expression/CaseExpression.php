<?php

namespace Magelight\Db\Common\Expression;

/**
 * Class CaseExpression
 * @package Magelight\Db\Common\Expression
 *
 * @method static CaseExpression forge($expression = '', $params = [])
 */
class CaseExpression extends Expression
{

    /**
     * Array of when/the expressions
     *
     * @var array
     */
    protected $whenThenExpressions = [];

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $expr = ['CASE'];
        $expr[] = $this->expression;
        foreach ($this->whenThenExpressions as $whenThen) {
            $expr[] = 'WHEN';
            $expr[] = $whenThen['WHEN'];
            $expr[] = 'THEN';
            $expr[] = $whenThen['THEN'];
        }
        $expr[] = 'END';
        return '(' . implode(' ', $expr) . ')';
    }

    /**
     * Set case expression
     *
     * @param string|ExpressionInterface $expression
     *
     * @return $this
     */
    public function setCase($expression)
    {
        $this->expression = $expression;
        if ($expression instanceof ExpressionInterface) {
            $this->setParams($expression->getParams());
        } else {
            $this->setParams([]);
        }
        return $this;
    }

    /**
     * Add when expression
     *
     * @param string|ExpressionInterface $expression
     * @param string|ExpressionInterface $thenExpression
     *
     * @return $this
     */
    public function when($expression, $thenExpression)
    {
        $this->whenThenExpressions[] = [
            'WHEN' => $expression,
            'THEN' => $thenExpression
        ];
        if ($expression instanceof ExpressionInterface) {
            $this->pushParams($expression->getParams());
        }
        if ($thenExpression instanceof ExpressionInterface) {
            $this->pushParams($thenExpression->getParams());
        }
        return $this;
    }
}

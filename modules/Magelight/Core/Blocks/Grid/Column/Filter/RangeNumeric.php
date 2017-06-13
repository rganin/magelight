<?php

namespace Magelight\Core\Blocks\Grid\Column\Filter;

use Magelight\Core\Blocks\Element;
use Magelight\Core\Blocks\Grid\Column;
use Magelight\Db\Common\Expression\Expression;
use Magelight\Db\Common\Expression\ExpressionCombination;
use Magelight\Webform\Blocks\Elements\Input;

class RangeNumeric extends AbstractFilter
{
    /**
     * @var Input
     */
    protected $inputFrom;

    /**
     * @var Input
     */
    protected $inputTo;

    /**
     * {@inheritdoc}
     */
    public function __forge()
    {
        $this->setTag('div');
        $this->inputFrom = Input::forge()->setAttribute(
            'type',
            'number'
        )->setClass(
            'form-control input-sm'
        )->setPlaceholder(__('From', [], 1, 'filters'));
        if ($this->getForm()) {
            $this->inputFrom->setForm($this->getForm());
        }
        $this->inputTo = clone $this->inputFrom;
        $this->inputTo->setPlaceholder(__('To', [], 1, 'filters'));
        $this->addContent($this->inputFrom)->addContent($this->inputTo);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterSqlExpression()
    {
        if (empty($this->inputFrom->getValue()) || empty($this->inputTo->getValue())) {
            return Expression::forge('');
        } else {
            $expression = ExpressionCombination::forge()->setLogicAnd();
            if (!empty($this->inputFrom->getValue())) {
                $expression->addExpression(
                    Expression::forge("{$this->getName()} >= ?", $this->inputFrom->getValue())
                );
            }
            if (!empty($this->inputTo->getValue())) {
                $expression->addExpression(
                    Expression::forge("{$this->getName()} <= ?", $this->inputTo->getValue())
                );
            }
        }
        return $expression;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilterField($rowFieldName)
    {
        parent::setFilterField($rowFieldName);
        $this->inputFrom->setName($this->getName() . '_from');
        $this->inputTo->setName($this->getName() . '_to');
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setForm(\Magelight\Webform\Blocks\Form $form)
    {
        $this->inputFrom->setForm($form);
        $this->inputTo->setForm($form);
        return parent::setForm($form);
    }
}
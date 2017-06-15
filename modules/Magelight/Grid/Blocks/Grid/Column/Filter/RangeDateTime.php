<?php

namespace Magelight\Grid\Blocks\Grid\Column\Filter;

use Magelight\Core\Blocks\Document;
use Magelight\Db\Common\Expression\Expression;
use Magelight\Db\Common\Expression\ExpressionCombination;
use Magelight\Webform\Blocks\Elements\Input;

class RangeDateTime extends AbstractFilter
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
            'text'
        )->setClass(
            'form-control input-sm datetimepicker'
        )->setPlaceholder(
            __('From', [], 1, 'filters')
        )->setAttribute('data-bound', 'from');
        if ($this->getForm()) {
            $this->inputFrom->setForm($this->getForm());
        }
        $this->inputTo = clone $this->inputFrom;
        $this->inputTo->setPlaceholder(__('To', [], 1, 'filters'))->setAttribute('data-bound', 'to');
        $this->addContent($this->inputFrom)
            ->addContent($this->inputTo);
        Document::getInstance()->addJs('Magelight/Grid/static/js/date-range-filter.js');
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
                    Expression::forge("{$this->getName()} >= ?", strtotime($this->inputFrom->getValue()))
                );
            }
            if (!empty($this->inputTo->getValue())) {
                $expression->addExpression(
                    Expression::forge("{$this->getName()} <= ?", strtotime($this->inputTo->getValue()))
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
        $this->inputFrom->setName($this->getName() . '[from]');
        $this->inputTo->setName($this->getName() . '[to]');
        // has to generate JS after names are set
        $this->addContent(
            "<script type=\"text/javascript\">
                $(document).ready(function () {
                    var div = $('div#{$this->getId()}'); 
                    div.find('input[data-bound=\"from\"]').datetimepicker(filterDatePickerConfig);
                    div.find('input[data-bound=\"to\"]').datetimepicker(filterToDatePickerConfig);
                });
            </script>"
        );
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

    public function setValue($value)
    {
        if (!empty($value['from'])) {
            $this->inputFrom->setValue($value['from']);
        }
        if (!empty($value['to'])) {
            $this->inputTo->setValue($value['to']);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmptyValue()
    {
        return ($this->inputFrom->getValue() === '' || $this->inputFrom->getValue() === null)
            && ($this->inputTo->getValue() === '' || $this->inputTo->getValue() === null);
    }
}
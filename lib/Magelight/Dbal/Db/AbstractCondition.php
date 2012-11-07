<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 03.11.12
 * Time: 20:07
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Dbal\Db;

abstract class AbstractCondition
{
    /**
     * Subconditions logic
     */
    const LOGIC_AND     = 'AND';
    const LOGIC_OR      = 'OR';
    const LOGIC_AND_NOT = 'AND NOT';
    const LOGIC_OR_NOT  = 'OR NOT';

    protected $conditionsMap = [
        'Eq'        => ':expression = :param',
        'Neq'       => ':expression != :param',
        'Null'      => ':expression IS NULL',
        'NotNull'   => ':expression IS NOT NULL',
        'Gt'        => ':expression > :param',
        'Gte'       => ':expression >= :param',
        'Lt'        => ':expression < :param',
        'Lte'       => ':expression <= :param',
        'Like'      => ':expression LIKE :param',
        'In'        => ':expression IN (:param)',
        'NotIn'     => ':expression NOT IN (:param)',
        'Ex'        => ':expression',
        ''          => '',
    ];

    protected $expression    = null;

    protected $type          = null;

    protected $param         = null;

    protected $subconditions = [];

    public function __construct($type = '', $expression = null , $param = null)
    {
        $this->type = $type;
        $this->expression = $expression;
        $this->param = $this->prepareParam($param);
    }

    protected function convertToSet()
    {
        $this->type = '';
    }

    protected function addSubCondition(AbstractCondition $condition, $logic)
    {
        $this->convertToSet();
        if (empty($this->subconditions)) {
            $this->subconditions[] = clone $this;
        }
        $this->subconditions[][$logic] = $condition;
    }

    public function andCond(AbstractCondition $condition)
    {
        $this->addSubCondition($condition, self::LOGIC_AND);
        return $this;
    }

    public function orCond(AbstractCondition $condition)
    {
        $this->addSubCondition($condition, self::LOGIC_OR);
        return $this;
    }

    public function andNotCond(AbstractCondition $condition)
    {
        $this->addSubCondition($condition, self::LOGIC_AND_NOT);
        return $this;
    }

    public function orNotCond(AbstractCondition $condition)
    {
        $this->addSubCondition($condition, self::LOGIC_OR_NOT);
        return $this;
    }

    abstract public function getRenderedCondition();

    abstract public function render();
}

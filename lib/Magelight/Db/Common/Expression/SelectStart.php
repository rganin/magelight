<?php
/**
 * Created by PhpStorm.
 * User: rganin
 * Date: 04.01.2016
 * Time: 18:44
 */

namespace Magelight\Db\Common\Expression;

class SelectStart extends Expression
{
    /**
     * Current table name
     *
     * @var string
     */
    protected $tableName = null;

    /**
     * SelectFields
     *
     * @var array
     */
    protected $selectFields = [];

    /**
     * Aliases for data keys
     *
     * @var array
     */
    protected $fieldAliases = [];

    /**
     * Distinct field
     *
     * @var null|string
     */
    protected $distinctField = null;

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $query[] = 'SELECT';
        $query[] = empty($this->selectFields) ? '*' : join(', ', $this->getSelectFields());
        $query[] = 'FROM';
        $query[] = $this->tableName;
        return implode(' ', $query);
    }

    /**
     * Add select field
     *
     * @param $expression
     * @param null $alias
     * @param array $params
     * @param bool $distinct
     * @return $this
     */
    public function selectField($expression, $alias = null, $params = [], $distinct = false)
    {
        if (!empty($expression)) {
            $this->selectFields[] = $expression;
            if (!empty($alias)) {
                $this->fieldAliases[$expression] = $alias;
            }
            if ($distinct) {
                $this->distinctField = $expression;
            }
            $this->pushParams($params);
        }
        return $this;
    }

    /**
     * Add select field filter
     *
     * @param array $fields
     * @return $this
     */
    public function selectFields($fields = [])
    {
        $this->selectFields = [];
        if (empty($fields)) {
            return $this;
        }
        if (!is_array($fields)) {
            $fields = func_get_args();
        }
        foreach ($fields as $field) {
            $this->selectField($field);
        }
        return $this;
    }

    /**
     * Get the select fields for query
     *
     * @return array
     */
    protected function getSelectFields()
    {
        $fields = [];
        foreach ($this->selectFields as $selectField) {
            if ($this->distinctField == $selectField) {
                $fields[] = isset($this->fieldAliases[$selectField])
                    ? 'DISTINCT(' . $selectField . ') AS ' . $this->fieldAliases[$selectField]
                    : 'DISTINCT(' . $selectField . ')';
            } else {
                $fields[] = isset($this->fieldAliases[$selectField])
                    ? $selectField . ' AS ' . $this->fieldAliases[$selectField]
                    : $selectField;
            }

        }
        return $fields;
    }
}

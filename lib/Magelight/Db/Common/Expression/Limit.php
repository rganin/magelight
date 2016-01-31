<?php

/**
 * Created by PhpStorm.
 * User: rganin
 * Date: 04.01.2016
 * Time: 18:45
 */
class Limit
{
    /**
     * Build joins
     *
     * @return string
     */
    protected function buildJoins()
    {
        $query = [];
        foreach ($this->join as $join) {
            $query[] = $join['logic'];
            $query[] = $join['table'];
            if (!empty($join['alias'])) {
                $query[] = $join['alias'];
            }
            $query[] = 'ON ' . $join['on'];
            $this->pushParams($join['params']);
        }
        return implode(' ', $query);
    }
}
<?php

namespace Magelight\Dbal\Db\MySql;

class Orm extends \Magelight\Dbal\Db\AbstarctOrm
{
    /**
     * Quotation character
     *
     * @var string
     */
    protected $quoteChar = '`';
}
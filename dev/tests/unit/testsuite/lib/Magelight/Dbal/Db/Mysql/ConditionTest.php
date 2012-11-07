<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 04.11.12
 * Time: 10:23
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Dbal\Db\MySql;

class ConditionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    /**
     * @test
     */
    public function conditionPositive()
    {
        $condition = new Condition(Condition::COND_EQ, 'id', 12);
        $condition->andCond(new Condition(Condition::COND_EQ, 'test', 0));
        echo $condition->render();
    }

    public function tearDown()
    {

    }
}
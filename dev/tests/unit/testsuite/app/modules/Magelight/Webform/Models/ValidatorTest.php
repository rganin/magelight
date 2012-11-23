<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 23.11.12
 * Time: 22:28
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Webform\Models;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
//    /**
//     * @test
//     */
//    public function multiArrayTest()
//    {
//        $arr = [
//            'login' => '111',
//            'pass'  => '222',
//            'test'  => [
//                'exam' => 2,
//                'restul' => 3,
//            ],
//        ];
//        $res = \Magelight\Helpers\ArrayHelper::multiArrayToVector($arr);
//        var_dump($res);
//    }

    /**
     * @test
     */
    public function validatorTest()
    {
        $validator = Validator::forge();
        $validator->fieldRules('login', 'Login field')
            ->maxLength(20)->chainRule()
            ->minLength(5);
        $validator->fieldRules('email', 'Email field')
            ->email()->chainRule()
            ->rangeLength(5, 30);
        $result = $validator->validate(['login' => 'iddqd', 'email' => 'iddqd@meta.ua'])->result()->isSuccess();
        $this->assertTrue($result);
    }


}
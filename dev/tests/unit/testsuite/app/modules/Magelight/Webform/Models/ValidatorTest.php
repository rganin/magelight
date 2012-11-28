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
        $result = $validator->validate(['login' => 'iddqd', 'email' => 'iddqd@meta.ua'])->result();
        $this->assertTrue($result->isSuccess());
    }

    /**
     * @test
     */
    public function validatorComplexTest()
    {
        $validator = Validator::forge();
        $validator->fieldRules('user[skills][general][]', 'General skills')
            ->maxLength(20)->chainRule()->minLength(5);

        $result = $validator->validate([
            'user' => [
                'skills' => [
                    'general' => ['12345', '54321', '42232']
                ]
            ]
        ])->result();
        $this->assertTrue($result->isSuccess());
    }
}

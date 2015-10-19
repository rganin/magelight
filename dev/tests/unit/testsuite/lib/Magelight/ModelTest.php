<?php
/**
 * Magelight
 *
 * NOTICE OF LICENSE
 *
 * This file is open source and it`s distribution is based on
 * Open Software License (OSL 3.0). You can obtain license text at
 * http://opensource.org/licenses/osl-3.0.php
 *
 * For any non license implied issues please contact rganin@gmail.com
 *
 * DISCLAIMER
 *
 * This file is a part of a framework. Please, do not modify it unless you discard
 * further updates.
 *
 * @version 1.0
 * @author Roman Ganin
 * @copyright Copyright (c) 2012-2015 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight;

/**
 * Class ModelTest
 * @package Magelight
 */
class ModelTest extends \Magelight\TestCase
{
    /**
     * @var \Magelight\App|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $appMock;

    /**
     * @var \Magelight\Db\Common\Adapter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dbMock;

    /**
     * @var \Magelight\Db\Common\Orm|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ormMock;

    /**
     * @var Model
     */
    protected $model;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->appMock = $this->getMock(\Magelight\App::class, [], [], '', false);
        \Magelight\App::forgeMock($this->appMock);

        $this->dbMock = $this->getMock(\Magelight\Db\Common\Adapter::class, [], [], '', false);

        $this->appMock->expects($this->any())
            ->method('db')
            ->with(APP::DEFAULT_INDEX)
            ->will($this->returnValue($this->dbMock));

        $this->dbMock->expects($this->any())
            ->method('getType')
            ->will($this->returnValue(\Magelight\Db\Common\Adapter::TYPE_MYSQL));

        $this->ormMock = $this->getMock(\Magelight\Db\Mysql\Orm::class, [], [], '', false);
        \Magelight\Db\Mysql\Orm::forgeMock($this->ormMock);

        $this->ormMock->expects($this->once())->method('create')->with(['arg1' => 'value1'], false);

        $this->model = Model::forge(['arg1' => 'value1']);
    }

    public function testGetOrm()
    {
        $this->assertEquals($this->ormMock, $this->model->getOrm());
    }

    public function testSetOrm()
    {
        /** @var \Magelight\Db\Mysql\Orm|\PHPUnit_Framework_MockObject_MockObject $otherOrmMock */
        $otherOrmMock = $this->getMock(\Magelight\Db\Mysql\Orm::class, [], [], '', false);
        $this->model->setOrm($otherOrmMock);
        $this->assertEquals($otherOrmMock, $this->model->getOrm());
    }

    public function testAfterLoad()
    {
        $this->assertEquals($this->model, $this->model->afterLoad());
    }

    public function testGetId()
    {
        $this->ormMock->expects($this->any())->method('getValue')->with('id')->will($this->returnValue(1));
        $this->assertEquals(1, $this->model->getId());
        $this->assertEquals(1, $this->model->id);

    }

    public function testSetId()
    {
        $this->ormMock->expects($this->once())->method('setValue')->with('id', 2);
        $this->ormMock->expects($this->any())->method('getValue')->with('id')->will($this->returnValue(2));
        $this->model->id = 2;
        $this->assertEquals(2, $this->model->getId());
    }

    public function testUnset()
    {
        $this->ormMock->expects($this->once())->method('unsetValue')->with('id');
        unset($this->model->id);
    }

    public function testDeleteById()
    {
        $this->ormMock->expects($this->once())->method('delete')->with(2);
        Model::deleteById(2);
    }

    public function testDelete()
    {
        $this->ormMock->expects($this->once())->method('delete')->with(null);
        $this->model->delete();
    }

    public function testSave()
    {
        $this->ormMock->expects($this->once())->method('isNew')->will($this->returnValue(true));
        $this->ormMock->expects($this->once())->method('mergeData')->with([]);
        $this->ormMock->expects($this->once())
            ->method('save')
            ->with(false, false, false)
            ->will($this->returnValue(true));
        $this->assertTrue($this->model->save());
    }

    public function testDeleteBy()
    {
        $this->ormMock->expects($this->once())->method('deleteBy')->with('id', 3);
        $this->model->deleteBy('id', 3);
    }

    public function testAsArray()
    {
        $recordData = [
            'id' => 1,
            'name' => 'John',
            'lastname' => 'Doe'
        ];
        $this->ormMock->expects($this->exactly(2))
            ->method('getData')
            ->with(['id', 'name', 'lastname'])
            ->will($this->returnValue($recordData));
        $this->assertEquals($recordData, $this->model->asArray(['id', 'name', 'lastname']));
        $this->assertEquals($recordData, $this->model->asArray('id', 'name', 'lastname'));
    }

    public function testFindBy()
    {
        $field = 'id';
        $value = 5;
        $ormMock = $this->getMock(\Magelight\Db\Mysql\Orm::class, ['whereEq', 'fetchModel'], [], '', false);
        \Magelight\Db\Mysql\Orm::forgeMock($ormMock);
        $ormMock->expects($this->once())->method('fetchModel')->will($this->returnValue($this->model));
        $ormMock->expects($this->once())->method('whereEq')->with($field, $value)->will($this->returnSelf());
        $this->assertEquals($this->model, Model::findBy($field, $value));
    }

    public function testFind()
    {
        $field = 'id';
        $value = 5;
        $ormMock = $this->getMock(\Magelight\Db\Mysql\Orm::class, ['whereEq', 'fetchModel'], [], '', false);
        \Magelight\Db\Mysql\Orm::forgeMock($ormMock);
        $ormMock->expects($this->once())->method('fetchModel')->will($this->returnValue($this->model));
        $ormMock->expects($this->once())->method('whereEq')->with($field, $value)->will($this->returnSelf());
        $this->assertEquals($this->model, Model::find($value));
    }

    public function testModelsToArrayRecursive()
    {
        $model1 = $this->getMock(\Magelight\Model::class, [], [], '', false);
        $model2 = $this->getMock(\Magelight\Model::class, [], [], '', false);
        $model3 = $this->getMock(\Magelight\Model::class, [], [], '', false);
        $model1->expects($this->once())->method('asArray')->will($this->returnValue([1]));
        $model2->expects($this->once())->method('asArray')->will($this->returnValue([2]));
        $model3->expects($this->once())->method('asArray')->will($this->returnValue([3]));
        $arr = [$model1, $model2, $model3];
        $expectedArr = [0 => [1], 1 => [2], 2 => [3]];
        $this->assertEquals($expectedArr, \Magelight\Model::modelsToArrayRecursive($arr));
    }

    public function testGetFlatCollection()
    {
        $collectionMock = $this->getMock(\Magelight\Db\Collection::class, [], [], '', false);
        \Magelight\Db\Collection::forgeMock($collectionMock);
        $this->assertEquals($collectionMock, \Magelight\Model::getFlatCollection());
    }

    public function testMergeData()
    {
        $this->ormMock->expects($this->once())->method('mergeData')->with([1], true);
        $this->assertEquals($this->model, $this->model->mergeData([1], true));
    }

    public function testGetRandomIds()
    {
        $limit = 3;
        $this->ormMock->expects($this->once())
            ->method('selectFields')
            ->with([$this->model->getIdField()])
            ->will($this->returnSelf());
        $this->ormMock->expects($this->once())
            ->method('orderByDesc')
            ->with('RAND()')
            ->will($this->returnSelf());
        $this->ormMock->expects($this->once())
            ->method('limit')
            ->with($limit)
            ->will($this->returnSelf());
        $this->ormMock->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue([['id' => 11], ['id' => 13], ['id' => 12]]));

        $this->assertEquals([11, 13, 12], $this->model->getRandomIds($limit));
    }

    public function testEscapePropertiesHtml()
    {
        $property = '<div>property</div>';
        $expectedProperty = '&lt;div&gt;property&lt;/div&gt;';
        $this->ormMock->expects($this->once())->method('getValue')->with('property')->will($this->returnValue($property));
        $this->ormMock->expects($this->once())->method('setValue')->with('property', $expectedProperty);
        $this->model->escapePropertiesHtml(['property']);
    }

    public function testCastPropertiesInt()
    {
        $property = '123';
        $expectedProperty = 123;
        $this->ormMock->expects($this->once())->method('getValue')->with('property')->will($this->returnValue($property));
        $this->ormMock->expects($this->once())->method('setValue')->with('property', $expectedProperty);
        $this->model->castPropertiesInt(['property']);
    }

    public function testCastPropertiesString()
    {
        $property = 123;
        $expectedProperty = '123';
        $this->ormMock->expects($this->once())->method('getValue')->with('property')->will($this->returnValue($property));
        $this->ormMock->expects($this->once())->method('setValue')->with('property', $expectedProperty);
        $this->model->castPropertiesString(['property']);
    }

    public function testCastPropertiesFloat()
    {
        $property = '123.456';
        $expectedProperty = 123.456;
        $this->ormMock->expects($this->once())->method('getValue')->with('property')->will($this->returnValue($property));
        $this->ormMock->expects($this->once())->method('setValue')->with('property', $expectedProperty);
        $this->model->castPropertiesFloat(['property']);
    }

    public function testCastPropertiesArray()
    {
        $property = 123;
        $expectedProperty = [123];
        $this->ormMock->expects($this->once())->method('getValue')->with('property')->will($this->returnValue($property));
        $this->ormMock->expects($this->once())->method('setValue')->with('property', $expectedProperty);
        $this->model->castPropertiesArray(['property']);
    }

    public function testCastPropertiesObject()
    {
        $property = 123;
        $expectedProperty = (object)123;
        $this->ormMock->expects($this->once())->method('getValue')->with('property')->will($this->returnValue($property));
        $this->ormMock->expects($this->once())->method('setValue')->with('property', $expectedProperty);
        $this->model->castPropertiesObject(['property']);
    }
}

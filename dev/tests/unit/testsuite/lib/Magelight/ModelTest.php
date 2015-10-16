<?php

namespace Magelight;

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


}

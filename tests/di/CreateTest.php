<?php
use qpf\deunit\TestUnit;
use qpf\base\Core;

include __DIR__ . '/../boot.php';

class Foo extends Core
{
    public $value;
}

class CreateTest extends TestUnit
{
    public function setUp()
    {
        QPF::app();
    }
    
    /**
     * 测试创建类, 并初始化属性
     */
    public function testCreate()
    {
        echor(QPF::create([
            '$class' => 'Foo',
            'value' => 'ok',
        ]));
    }
}

echor(CreateTest::runTestUnit());
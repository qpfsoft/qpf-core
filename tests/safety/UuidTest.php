<?php
use qpf\deunit\TestUnit;
use qpf\safety\Uuid;
use qpf\safety\Guid;

include __DIR__ . '/../boot.php';

class UuidTest extends TestUnit
{
    public function testBase()
    {
        return [
            Uuid::v3(Guid::id('qpf'), 'build'),
            Uuid::v4(),
            Uuid::v5(Guid::id('qpf'), 'build'),
        ];
    }
    
    /**
     * 需要安装扩展
     * @return array
     */
    public function testPhp()
    {
        return [
            uuid_create(),
        ];
    }
}

echor(UuidTest::runTestUnit());
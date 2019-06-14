<?php
use qpf\deunit\TestUnit;

include __DIR__ . '/../boot.php';

/**
 * QPF 全局类测试
 */
class QPFTest extends TestUnit
{
    /**
     * 命名风格格式化
     */
    public function testNameFormat()
    {
        return [
            ['user_name' => QPF::nameFormatToClass('user_name')],
            ['user_name' => QPF::nameFormatToClass('user_name', false)],
            ['UserName' => QPF::nameFormatToClass('user_name', false)],
            ['UserName'  => QPF::nameFormatToFunc('UserName')],
        ];
    }
}

echor(QPFTest::runTestUnit());
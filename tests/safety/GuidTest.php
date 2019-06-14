<?php
use qpf\deunit\TestUnit;
use qpf\safety\Guid;

include __DIR__ . '/../boot.php';

class GuidTest extends TestUnit
{
    public function testBase()
    {
        return [
            Guid::id(),
            Guid::id('qpf'),
            Guid::id('qpf', false),
        ];
    }
}

echor(GuidTest::runTestUnit());
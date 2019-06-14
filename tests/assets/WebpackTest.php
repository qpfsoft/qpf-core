<?php
use qpf\deunit\TestUnit;
use qpf\assets\Webpack;

include __DIR__ . '/../boot.php';

class WebpackTest extends TestUnit
{
    public function setUp()
    {
        QPF::app()->init();
    }
    
    /**
     * 安装web资源包
     * @return array
     */
    public function testInstall()
    {
        return (new Webpack())->install();
    }
}

echor(WebpackTest::runTestUnit());
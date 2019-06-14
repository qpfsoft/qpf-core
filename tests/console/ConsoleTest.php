<?php
use qpf\deunit\TestUnit;
use qpf\console\Console;

include __DIR__ . '/../boot.php';

/**
 * 控制台测试
 * 
 * 控制台仅能执行以`qpf` 关键字开头的内部命令
 */
class ConsoleTest extends TestUnit
{
    public $console;
    
    public function setUp()
    {
        QPF::app()->init();
        $this->console = new Console(QPF::$app);
    }
    
    public function testRun()
    {
        return $this->console->execute('qpf build');
    }
}

echor(ConsoleTest::runTestUnit());
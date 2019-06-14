<?php
use qpf\deunit\TestUnit;
use qpf\console\Runcmd;

include __DIR__ . '/../boot.php';

/**
 * 运行命令
 * 
 * 实现再php脚本中执行控制台命令
 */
class RuncmdTest extends TestUnit
{
    public $cmd;
    
    public function setUp()
    {
        QPF::app()->init();
        $this->cmd = new Runcmd();
    }
    
    public function testBase1()
    {
        // $cmd (命令, 运行{tag}), $args( 'tag' => '-v');
        return $this->cmd->run('php -v');
    }
    
    public function testBase2()
    {
        return $this->cmd->run('node -v');
    }
    
    public function testInfo()
    {
        return $this->cmd->info;
    }
}

echor(RuncmdTest::runTestUnit());
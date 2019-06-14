<?php
use qpf\deunit\TestUnit;
use qpf\console\Runcmd;

include __DIR__ . '/../boot.php';

/**
 * css 压缩测试
 */
class CssTest extends TestUnit
{
    public $cmd;
    
    public function setUp()
    {
        QPF::app()->init();
        $this->cmd = new Runcmd();
        $this->cmd->commands = [
            'css' => 'cleancss -o {dst} {src}',
        ];
    }
    
    /**
     * 
     * @param string $dst
     * @param string $src
     * @param string $basePath
     */
    public function testCleancss()
    {
        $this->cmd->run($this->cmd->commands['css'], 
            [
                'dst' => 'test.min.css',
                'src' => 'test.css'
            ], __DIR__ . '/static', $this->cmd->getCliEnv());
    }
    

    public function testInfo()
    {
        return $this->cmd->info;
    }
}

echor(CssTest::runTestUnit());
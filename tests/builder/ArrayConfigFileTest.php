<?php
include __DIR__ . '/../boot.php';

use qpf\deunit\TestUnit;
use qpf\builder\template\ArrayConfigFile;

/**
 * 测试生成php数组配置文件
 */
class ArrayConfigFileTest extends TestUnit
{
    public $file;
    
    public function setUp()
    {
        $this->file = new ArrayConfigFile();
    }
    
    public function testBuild()
    {
        // 头部注解
        $this->file->comment = [
            '数组配置文件'
        ];
        
        // 预设配置
        $this->file->config = [
            'debug' => true,
            'db'    => [
                'host'  => '127.0.0.1',
            ],
        ];
        
        $data = $this->file->getContent();
        
        return file_put_contents(__DIR__ . '/temp/'. __CLASS__ . '.php', $data);
    }
}

echor(ArrayConfigFileTest::runTestUnit());
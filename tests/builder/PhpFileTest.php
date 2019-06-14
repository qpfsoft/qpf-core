<?php
use qpf\deunit\TestUnit;
use qpf\builder\template\PhpFile;

include __DIR__ . '/../boot.php';

/**
 * PHP 文件生成测试
 */
class PhpFileTest extends TestUnit
{
    public $php;
    
    public function setUp()
    {
        $this->php = new PhpFile();
    }

    public function testBuildFile()
    {
        // 设置注解
        $this->php->namespace = 'app';
        
        // 设置类声明
        $this->php->use = [
            'qpf',
            'qpf\base\App',
        ];
        
        // 设置头部注解
        $this->php->comment = [
            '测试文件',
            '',
            '@paran string $test 添加注解'
        ];
        
        // 设置主体内容
        $this->php->content = 'return [];';
        
        // 获取文件内容
        $data = $this->php->getContent();
        
        return file_put_contents(__DIR__ . '/temp/'. __CLASS__ . '.php', $data);
    }
}

echor(PhpFileTest::runTestUnit());
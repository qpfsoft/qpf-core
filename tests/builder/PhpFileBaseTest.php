<?php
use qpf\deunit\TestUnit;
use qpf\builder\template\PhpFile;

include __DIR__ . '/../boot.php';

/**
 * PHP 文件生成测试
 */
class PhpFileTest extends TestUnit
{

    public function testBuildFile()
    {
        $file = new PhpFile();
        $file->content = 'return [];';
        
        // 获取文件内容
        $data = $file->getContent();
        
        return file_put_contents(__DIR__ . '/temp/'. __CLASS__ . '.php', $data);
    }
}

echor(PhpFileTest::runTestUnit());
<?php
include __DIR__ . '/../boot.php';

use qpf\deunit\TestUnit;
use qpf\builder\template\PhpFile;

/**
 * 文件内容模板测试
 */
class FileTemplateTest extends TestUnit
{
    public $file;
    
    public function setUp()
    {
        $this->file = new PhpFile();
    }
    
    /**
     * 渲染模板
     */
    public function testTplRender()
    {
        return $this->file->render($this->getTpl(), [
            'name'  => '红梅',
            'age'   => '12',
        ]);
    }
    
    /**
     * 模板
     * @return string
     */
    public function getTpl()
    {
        return <<<TPL
你的名字是{:name}, 今年{:age}岁了!
-- {:name}同学
TPL;
    }
        
    /**
     * 渲染模板
     */
    public function testTplRender2()
    {
        return $this->file->render('我的名字是{:name}!', [
            'name'  => '红梅',
            'age'   => '12',
        ]);
    }
}

echor(FileTemplateTest::runTestUnit());
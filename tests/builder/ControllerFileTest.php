<?php
include __DIR__ . '/../boot.php';

use qpf\deunit\TestUnit;
use qpf\builder\template\ControllerFile;

class ControllerFileTest extends TestUnit
{
    public $file;
    
    public function setUp()
    {
        $this->file = new ControllerFile();
    }
    
    public function testBuild()
    {
        $this->file->namespace = 'app\home\controller';
        
        $this->file->use = [
            'qpf\base\Controller',
        ];
        
        $this->file->comment = [
            'QPF生成的控制器'
        ];
        
        $this->file->className = 'HomeController';
        
        $data = $this->file->getContent();
        
        return file_put_contents(__DIR__ . '/temp/'. __CLASS__ . '.php', $data);
    }
    
    public function testBuild2()
    {
        $this->file->namespace = 'app\home\controller';
        
        $this->file->use = [
            'qpf\base\Controller',
        ];
        
        $this->file->comment = [
            'QPF生成的控制器'
        ];
        
        $this->file->className = 'AdminController';
        
        $this->file->hello = 'ok';
        
        $data = $this->file->getContent();
        
        return file_put_contents(__DIR__ . '/temp/'. __CLASS__ . '2.php', $data);
    }
}


echor(ControllerFileTest::runTestUnit());
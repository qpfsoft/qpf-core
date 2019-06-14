<?php
use qpf\deunit\TestUnit;
use qpf\builder\Dirstruc;
use qpf\bootstrap\BootstrapInterface;

include __DIR__ . '/../boot.php';

/**
 * 目录结构生成测试
 */
class DirstrucTest extends TestUnit
{
    public $dirs;
    
    public function setUp()
    {
        // 内部依赖
        QPF::app();
        
        $this->dirs = new Dirstruc();
    }
    
    public function testBuild()
    {
        // 绑定内置文件
        $this->dirs->bindInFile('app.php', [
            '$class' => '\qpf\builder\template\PhpFile',
            'content' => 'return [];',
        ]);

        $dir = [
            Dirstruc::IROOT => __DIR__ . '/root', // 根目录, 后续生成内容位置的依据
            
            Dirstruc::IDIR  => [ // 生成目录, 需要优先于文件
                'src',
                'config',
                'dosc',
                'tests',
            ],
            
            Dirstruc::IFILE => [ //  创建文件
                'readme.md' => <<<TPL
帮助
===

该文件由`Dirstruc`生成!
TPL
,
            ],
            
            // 二级 config目录内结构, 可忽略根目录, 自动生成为`__DIR__ /root/config`
            'config'    => [
                Dirstruc::IDIR  => 'route',
                
                'route' => [
                    Dirstruc::IDIR  => 'app',
                    Dirstruc::IFILE => 'app.php', // 生成内置文件
                ],
            ],
            
        ];
        
        // 打印目录结构定义数组
        echor($dir);
        
        // 生成改结构
        $this->dirs->setup($dir);
        
        // 显示日志
        echor($this->dirs->log());
    }
}

echor(DirstrucTest::runTestUnit());
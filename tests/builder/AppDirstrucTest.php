<?php
use qpf\deunit\TestUnit;
use qpf\builder\AppDirstruc;

include __DIR__ . '/../boot.php';

class AppDirstrucTest extends TestUnit
{
    public $builder;
    
    public function setUp()
    {
        QPF::app();
        
        $this->builder = new AppDirstruc();
    }
    
    /**
     * 生成基础目录结构
     */
    public function testBuildBase()
    {
        $build = $this->builder;

        $setup = $build->buildBaseSetup('app', __DIR__ . '/demo');
        
        // 创建默认index模块目录
        $setup['app'][$build::IDIR] = [
            'index',
        ];
        
        // 定义index模块目录结构
        $setup['app']['index'] = $build->buildAppSetup('index');
        
        //echor($setup);
        $this->builder->setup($setup);
    }
    
    /**
     * 生成单个应用目录结构
     */
    public function testBuildApp()
    {
        $build = $this->builder;
        
        // 只需提供, 模块名 与 zonePath
        $setup = $build->buildModuleSetup('admin', __DIR__ . '/demo/app');       
        //echor($setup);
        
        $build->setup($setup, __DIR__ . '/demo/app');
    }
    
    public function testShowLog()
    {
        return $this->builder->log();
    }
    
    public function config_base($root_path)
    {
        return [
            '__root__' => $root_path,
            '__dir__' => ['app', 'runtime', 'config', 'web'],
            'app' => [
                '__file__' => ['common.php', 'event.php', 'provider.php'],
                '__dir__' => ['index'],
                
                'index' => [
                    '__dir__' => ['controller', 'model', 'view'],
                    '__file__' => ['common.php'],
                    'controller' => [
                        '__controller__' => ['index'],
                    ],
                    'view' => [
                        '__dir__' => [],
                    ]
                ],
            ]
        ];
    }
}

echor(AppDirstrucTest::runTestUnit());
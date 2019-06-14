<?php
declare(strict_types=1);

namespace qpf\installer;

use qpf\base\Application as App;

class Install
{
    /**
     * 应用程序
     * @var App
     */
    protected $app;
    
    /**
     * 构造函数
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }
    
    /**
     * 侦听是否需要启动安装程序
     * 
     * - 安装流程.json
     * - @config/db 配置文件
     */
    public function listen()
    {
        if (@\is_readable($this->app->getRuntimePath() . '/install/install.json') ||
             !\file_exists($this->app->getConfigPath() . '/db' . $this->app->getConfigExt()))
        {
            $launch = new launch();
            $launch->checkEnv();
            
            exit;
        }
    }
}
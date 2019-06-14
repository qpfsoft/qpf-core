<?php
namespace qpf\provider;

use qpf\base\ServiceProvider;
use qpf\configs\Config;

class ConfigProvider extends ServiceProvider
{
    /**
     * 延迟加载
     * @var bool
     */
    public $defer = false;
    
    /**
     * 引导
     */
    public function boot()
    {
        $this->app['config']->setPath($this->app->getConfigPath());
        $this->app['config']->setExt($this->app->getConfigExt());
        
        // 若不支持自动关闭
        if($this->app['config']->onYaconf == true) {
            $this->app['config']->onYaconf = class_exists('Yaconf');
        }

        $this->app['config']->useGroup('param');
    }
    
    /**
     * 注册服务
     */
    public function register()
    {
        $this->app->single('config', ['$class' => Config::class]);
    }
}
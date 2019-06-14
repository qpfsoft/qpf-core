<?php
namespace qpf\provider;

use qpf\base\ServiceProvider;

class EnvProvider extends ServiceProvider
{
    /**
     * 延迟加载
     * @var bool
     */
    public $defer = false;
    
    /**
     * 注册
     */
    public function register()
    {
        $this->app->single('env', ['$class' => 'qpf\configs\Env']);
    }
    
    /**
     * 引导
     */
    public function boot()
    {
        // 加载环境变量
        if (is_file($this->app->getRootPath() . '/.env')) {
            $this->app['env']->load($this->app->getRootPath() . '/.env');
        }
    }
}
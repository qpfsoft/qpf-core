<?php
namespace qpf\bootstrap;

use qpf\configs\ConfigProvider;
use qpf\configs\EnvProvider;
use qpf\error\ErrorProvider;

/**
 * 服务引导程序
 */
class ServiceBootstrap implements BootstrapInterface
{
    /**
     * 供应商
     * @var array
     */
    protected $providers = [
        EnvProvider::class,
        ConfigProvider::class,
        ErrorProvider::class,
    ];
    
    
    /**
     * 引导
     */
    public function bootstrap(\qpf\base\Application $app)
    {
        // 注册服务提供商
        $app->setProviders($this->providers);
        
        // 单例服务
        $app->binds([
            'event' => [
                'class' => 'qpf\base\Event',
            ],
        ], true);
    }
}
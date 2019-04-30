<?php
namespace qpf\base;

/**
 * 服务提供商抽象类
 * 
 * ```
 * public function boot($app)
 * ```
 */
abstract class ServiceProvider
{
    /**
     * 延迟加载
     * @var bool
     */
    public $defer = false;
    
    /**
     * 应用程序
     * @var Application
     */
    protected $app;
    
    /**
     * 构造函数
     * @param object $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }
    
    /**
     * 注册服务
     */
    abstract public function register();
}
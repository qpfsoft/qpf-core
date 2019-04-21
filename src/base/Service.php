<?php
namespace qpf\base;

/**
 * 服务器供应商管理
 */
class Service extends Container
{
    /**
     * 延迟加载供应商
     * @var array
     */
    protected $deferProviders = [];
    /**
     * 异步加载的供应商
     * @var array
     */
    protected $asyncProviders = [];
    /**
     * 供应商类列表
     * @var array
     */
    protected $providers = [];
    /**
     * 外观类列表
     * @var array
     */
    protected $facades = [];
    /**
     * 应用是否已启动
     * @var bool
     */
    protected $booted = false;
    
    /**
     * 设置服务列表
     * @param array $providers
     * @return void
     */
    public function setProviders(array $providers)
    {
        $this->providers = array_merge($this->providers, $providers);
    }
    
    /**
     * 设置外观列表
     * @param array $providers
     * @return void
     */
    public function setFacades(array $facades)
    {
        $this->facades = array_merge($this->facades, $facades);
    }
    
    /**
     * 创建类的实例
     * @param string $name 类名, 接口名, 别名
     * @param array $params 构造参数, 会覆盖默认值
     * @param array $option 对象属性配置数组
     * @param bool $reset 本次是否重新创建一个新实例, 默认`false`
     * @return object
     */
    public function make($name, array $params = [], array $option = [], $reset = false)
    {
        // 加载延时服务
        if (isset($this->deferProviders[$name])) {
            $this->register(new $this->deferProviders[$name]($this));
            unset($this->deferProviders[$name]);
        }
        
        return parent::make($name, $params, $option, $reset);
    }
    
    /**
     * 引导供应商
     */
    protected function bootstrap()
    {
        foreach ($this->providers as $provider) {
            $reflectionClass = new \ReflectionClass($provider);
            $properties = $reflectionClass->getDefaultProperties();
            
            if (isset($properties['defer']) && $properties['defer'] === false) {
                // 立即加载服务
                $this->register(new $provider($this));
            } else {
                // 延时加载服务
                $name = substr($reflectionClass->getShortName(), 0, -8);
                $this->deferProviders[$name] = $provider;
            }
        }
    }
    
    /**
     * 注册服务
     * @param mixed $provider 服务名
     * @return object|NULL
     */
    protected function register($provider)
    {
        // 服务已注册
        if ($object = $this->getProvider($provider)) {
            return $object;
        }
        if (is_string($provider)) {
            $provider = new $provider($this);
        }
        if ($provider instanceof ServiceProvider) {
            $provider->register();
        }
        $this->asyncProviders[] = $provider;
        if ($this->booted) {
            $this->bootProvider($provider);
        }
    }
    
    /**
     * 获取已注册的服务
     * @param string $provider 服务名
     * @return object|null
     */
    protected function getProvider($provider)
    {
        $class = is_object($provider) ? get_class($provider) : $provider;
        foreach ($this->asyncProviders as $name) {
            if ($name instanceof $class) {
                return $name;
            }
        }
        
        return null;
    }
    
    /**
     * 运行服务的boot方法
     * @param ServiceProvider $provider 
     */
    protected function bootProvider($provider)
    {
        if (method_exists($provider, 'boot')) {
            $provider->boot($this);
        }
    }
}
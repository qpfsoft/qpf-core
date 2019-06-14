<?php
namespace qpf\base;

/**
 * 服务器供应商管理
 * 
 * 注意:
 * 非延迟加载的服务, 将在[[bootstrap()]]方法运行之后, 执行服务提供商类的[[register()]]
 * 方法来注册服务类到容器, 同时若应用引导完成, 将继续执行服务提供商类的[[boot()]]方法,
 * 可调用服务对象, 完成属性的修改.
 * 
 * 应用程序应确保, 在引导完成后将[[booted]]属性设置为`true`.
 * 否则提供商通过[[boot()]]方法配置服务类的处理将永远不会执行.
 * 
 * 可识别延迟加载的[[bootstrap()]]方法, 将在应用引导完成后仅执行一次.
 * 
 * 所以服务提供商需要在应用引导中完成注册. 后续注册在[[$providers]]属性中的供应商将无效.
 * 
 * 通过[[register()]]方法注册供应商, 将立即执行它的[[register()]]与[[boot()]]方法,
 * 所以延迟加载将失效, 引导方法始终在应用启动后才会执行一次. 错过将不再执行.
 * 
 * 非延迟加载供应商, 将会在应用引导完成后, 立即注册到容器.
 * 若[[boot]]方法中配置了类, 服务实例也将被创建.
 * 
 * 延迟加载供应商, 在通过[[make]]或[[get]]获取容器对象前, 才会进行注册与引导.
 * 
 * 技巧:
 * [[register()]]注册类到容器时, 可使用闭包函数, 并在内部初始化属性. 这将会使服务在调用时
 * 才会被创建并配置属性. 也达到了延迟加载的效果.
 * 
 * 问题:
 * 延迟加载与非延迟, 主要的问题是要向应用程序索要配置. 引导太早将浪费资源.
 * 
 * TODO:
 * 当服务供应商延迟加载. 在供应商boot()方法中$app->env, 会报错: 未定义属性.
 * 当服务供应商非延时加载, 在供应商boot()方法中$app->env, 会真确找到.
 * 使用$app['env'] 或 $app->get('env') 或 $app->make('env') 就不会出现这种问题.
 */
class Service extends Container
{
    /**
     * 延迟加载供应商
     * ```
     * [
     *      '服务名' => '服务供应商类',
     *      'log'   => 'LogProvider',
     * ]
     * ```
     * 当获取服务时`make('log')`将运行供应商进行注册和引导
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
     * ```
     * [
     *        0    => 'LogProvider', // 自动服务名为`log`
     *      'log'  => 'loggerProvider'; // 自定义服务名`log`
     * ]
     * ```
     * 自动服务名为`Provider`8位之前的小写字母.
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
     * 创建类的实例 - 加载延迟服务
     * @param string $name 类名, 接口名, 别名
     * @param array $params 构造参数, 会覆盖默认值
     * @param array $option 对象属性配置数组
     * @param bool $reset 本次是否重新创建一个新实例, 默认`false`
     * @return object
     */
    public function make($name, array $params = [], array $option = [], $reset = false)
    {
        $this->asyncRegister($name);
        
        return parent::make($name, $params, $option, $reset);
    }

    /**
     * 获取容器中的对象实例  - 加载延迟服务
     * @param string $name 类名, 接口名, 别名
     * @return object
     */
    public function get($name)
    {
        $this->asyncRegister($name);
        
        return parent::get($name);
    }
    
    /**
     * 引导供应商
     */
    protected function bootstrap()
    {
        foreach ($this->providers as $alias => $provider) {
            $reflectionClass = new \ReflectionClass($provider);
            $properties = $reflectionClass->getDefaultProperties();
            
            if (isset($properties['defer']) && $properties['defer'] === false) {
                // 立即加载服务
                $this->register(new $provider($this));
            } else {
                // 延时加载服务
                if (is_numeric($alias)) {
                    $name = strtolower(substr($reflectionClass->getShortName(), 0, -8));
                } else {
                    $name = $alias;
                }
                $this->deferProviders[$name] = $provider;
            }
        }
    }
    
    /**
     * 异步注册延迟服务
     * @param mixed $provider 服务名
     * @return void
     */
    protected function asyncRegister($name)
    {
        if (isset($this->deferProviders[$name])) {
            $this->register(new $this->deferProviders[$name]($this));
            unset($this->deferProviders[$name]);
        }
    }
    
    /**
     * 注册服务
     * @param mixed $provider 服务名
     * @return object|null
     */
    public function register($provider)
    {
        // 服务已注册
        $registered = $this->getProvider($provider);
        
        if ($registered) {
            return $registered;
        }
        
        if (is_string($provider)) {
            $provider = new $provider($this);
        }
        
        if ($provider instanceof ServiceProvider) {
            $provider->register();
        } else {
            $this->asyncProviders[] = $provider;
        }
        
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
     * 引导服务
     * @param ServiceProvider $provider 
     */
    protected function bootProvider($provider)
    {
        if (method_exists($provider, 'boot')) {
            $this->call([$provider, 'boot']);
        }
    }
}
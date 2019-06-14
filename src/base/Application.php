<?php
namespace qpf\base;

use qpf;
use qpf\bootstrap\BootstrapInterface;
use qpf\bootstrap\FacadeBootstrap;
use qpf\configs\Config;
use qpf\error\Error;
use qpf\bootstrap\ServiceBootstrap;
use qpf\configs\Env;
use qpf\lang\Lang;
use qpf\web\Http;
use qpf\web\Url;
use qpf\web\Request;
use qpf\web\Web;
use qpf\log\Log;
use qpf\router\Router;
use qpf\response\Response;
use qpf\installing\Install;

/**
 * 应用程序基类
 * 
 * @property Cookie $cookie
 * @property Config $config 配置管理器
 * @property Captcha $captcha 验证码
 * @property Db $db 数据库操作
 * @property Env $env 环境变量
 * @property Event $event 事件处理程序
 * @property Error $error 错误处理程序
 * @property Http $http 
 * @property Url $url url管理
 * @property Middleware $middleware 中间件
 * @property Lang $lang 语言包
 * @property Log $log 日志
 * @property Request $request 请求对象
 * @property Response $response 响应处理程序
 * @property Router $route 路由器
 * @property Session $session 会话参数
 * @property Web $web 网页服务
 * @property Install $install 安装程序
 */
class Application extends Service
{
    use PathPropertyTrait;
    
    /**
     * 应用名称
     * @var string
     */
    protected $name;
    /**
     * 当前应用目录
     * @var string
     */
    protected $appPath;
    /**
     * 应用命名空间
     * @var string
     */
    protected $namespace;
    /**
     * 是否已初始化
     * @var bool
     */
    protected $isInit = false;
    /**
     * 调试模式
     * @var bool|int
     */
    protected $debug = false;
    /**
     * 应用运行环境
     * @var string
     */
    protected $runenv;
    /**
     * 应用开始的时间戳
     * @var int
     */
    protected $startTime;
    /**
     * 应用开始前的内存占用
     * @var int
     */
    protected $startMem;
    /**
     * 配置后缀
     * @var string
     */
    protected $configExt = '.php';
    /**
     * 引导程序
     * @var array
     */
    protected $bootstraps = [
        ServiceBootstrap::class,
        FacadeBootstrap::class,
    ];
    
    /**
     * 构造函数
     */
    public function __construct()
    {
        QPF::$app = $this;
        $this->instance(static::class, $this);
        $this->instance('qpf\base\Container', $this);
        $this->bootstrap();
    }
    
    /**
     * 初始化应用程序
     * @return $this
     */
    public function init()
    {
        if ($this->isInit) {
            return $this;
        }
        
        $this->startTime = microtime(true);
        $this->startMem = memory_get_usage();
        // 引导供应商
        foreach ($this->bootstraps as $class) {
            $class = $this->make($class);
            if ($class instanceof BootstrapInterface) {
                $class->bootstrap($this);
            }
        }
        $this->booted = true;
        parent::bootstrap();

        // 加载应用依赖文件
        $this->appInitFile();
        $this->debuginit();
        
        // 设置系统时区
        $this->setTimeZone($this->config->get('app.timezone'));
        
        $this->event->trigger('AppInit');
        
        $this->isInit = true;
        
        return $this;
    }

    /**
     * 设置系统时区
     * @param string $value
     * @return $this
     */
    public function setTimeZone($value)
    {
        if(!empty($value)) {
            date_default_timezone_set($value);
        }
        
        return $this;
    }
    
    /**
     * 返回当前系统时区
     * @return string
     */
    public function getTimeZone()
    {
        return date_default_timezone_get();
    }
    
    /**
     * 返回配置扩展名
     * @return string
     */
    public function getConfigExt()
    {
        return $this->configExt;
    }

    /**
     * 返回启动时的时间戳
     * @return int
     */
    public function getStartTime()
    {
        return $this->startTime;
    }
    
    /**
     * 返回应用程序开始内存使用情况
     * @return int
     */
    public function getStartMem()
    {
        return $this->startMem;
    }
    
    /**
     * 返回当前运行环境
     * @return string {prod:生产环境, dev:开发环境, test:测试环境}
     */
    public function getRunenv()
    {
        if ($this->runenv === null) {
            $this->runenv = $this->env->get('app_runenv', 'prod');
        }
        
        return strtolower($this->runenv);
    }
    
    /**
     * 开发环境
     * @return bool
     */
    public function isDev()
    {
        return $this->getRunenv() == 'dev';
    }
    
    /**
     * 生产环境
     * @return bool
     */
    public function isProd()
    {
        return $this->getRunenv() == 'prod';
    }
    
    /**
     * 测试环境
     * @return bool
     */
    public function isTest()
    {
        return $this->getRunenv() == 'test';
    }
    
    /**
     * 设置当前应用程序相关路径
     * @param string $appName 应用名称
     * @return $this
     */
    public function setApp($appName)
    {
        $this->name = $appName;
        $this->namespace = $this->zonename . '\\' . $appName;
        $this->appPath = $this->zonePath . DIRECTORY_SEPARATOR . $appName;
        $this->runtimePath = $this->runtimePath . DIRECTORY_SEPARATOR . $appName;
        
        return $this;
    }
    
    /**
     * 设置应用程序命名空间
     * @param string $namespace
     * @return $this
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        
        return $this;
    }
    
    /**
     * 获取应用程序命名空间
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }
    
    /**
     * 设置当前应用名称
     * @param string $name
     * @return $this
     */
    public function name($name)
    {
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * 返回当前访问的应用名称
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * 设置应用程序路径
     * @param string $path
     * @return $this
     */
    public function setAppPath($path)
    {
        $this->appPath = $path;
        
        return $this;
    }
    
    /**
     * 返回当前应用程序目录路径
     * @return string
     */
    public function getAppPath()
    {
        return $this->appPath;
    }
    
    /**
     * 设置调试模式
     * @param bool|int $enable
     * @return $this
     */
    public function debug($enable)
    {
        $this->debug = $enable;
        
        return $this;
    }
    
    /**
     * 是否调试模式
     * @return bool|int
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * 引导程序
     */
    protected function bootstrap()
    {
        $this->qpfPath = dirname(__DIR__);
        $this->qpfsoftPath = $this->getTrueQpfsoftPath($this->qpfPath);
        $this->vendorPath = dirname($this->qpfsoftPath);
        $this->rootPath = dirname($this->vendorPath);
        $this->zonePath = $this->rootPath . DIRECTORY_SEPARATOR . $this->zonename;
        $this->runtimePath = $this->rootPath . DIRECTORY_SEPARATOR . 'runtime';
        $this->webPath = $this->rootPath . DIRECTORY_SEPARATOR . 'web';
        $this->routePath = $this->rootPath . DIRECTORY_SEPARATOR . 'route';
        $this->configPath = $this->rootPath . DIRECTORY_SEPARATOR . 'config';
    }

    /**
     * 获取正确的qpfsoft目录
     * @param string $qpfPath
     */
    protected function getTrueQpfsoftPath($qpfPath)
    {
        if (substr($qpfPath, -3) == 'src') {
            return dirname(dirname($qpfPath));
        }
            
        return dirname($qpfPath);
    }
    
    /**
     * 加载应用初始化依赖文件
     * @return void
     */
    protected function  appInitFile()
    {
        $this->configExt = $this->env->get('config_ext', '.php');

        // 加载全局初始化文件
        if(is_file($this->runtimePath . '/init.php')) {
            include $this->runtimePath  . '/init.php';
        } else {
            $this->loadAppFile();
        }
    }
    
    /**
     * 加载应用依赖文件
     */
    protected function loadAppFile()
    {
        // 加载公共函数文件
        if (is_file($this->zonePath . '/common.php')) {
            include_once $this->zonePath . '/common.php';
        }
        
        // 加载全局事件文件
        if (is_file($this->zonePath . '/event.php')) {
            $this->loadEvent(include $this->zonePath . '/event.php');
        }
        
        // 加载全局服务提供商文件
        if (is_file($this->zonePath . '/provider.php')) {
            $this->bindProvider(include $this->zonePath . '/provider.php');
        }
        
        // 配置列表
        $files = [];
        
        // 框架散配置模式 - 目录下所有文件
        if (is_dir($this->configPath)) {
            $files = glob($this->configPath . '/*' . $this->configExt);
        }

        foreach ($files as $file) {
            $this->config->load($file, pathinfo($file, PATHINFO_FILENAME));
        }
    }
    
    /**
     * 导入事件定义
     * @param array $config
     * @return void
     */
    protected function loadEvent(array $config)
    {
        if(isset($config['event'])) {
            $this->event->bindEvents($config['event']);
        }
        
        if(isset($config['listen'])) {
            $this->event->addEventlisteners($config['listen']);
        }
        
        if(isset($config['subscribe'])){
            $this->event->subscribe($config['subscribe']);
        }
    }
    
    /**
     * 绑定服务供应商
     * @param array $config
     */
    protected function bindProvider(array $config)
    {
        $this->binds($config);
    }
    
    /**
     * 调试模式初始化
     * @return void
     */
    protected function debuginit()
    {
        // 始终以环境变量生效
        $this->debug = $this->env->get('app_debug', $this->config->get('app.debug', false));
        $this->runenv = $this->env->get('app_runenv', 'prod');
        $this->error->register();
        
        if(!$this->debug) {
            ini_set('display_errors', 'Off');
        } elseif (!$this->request->isConsoleRequest()) {
            if (ob_get_level() > 0) {
                $output = ob_get_clean();
            }
            ob_start();
            if (!empty($output)) {
                echo $output;
            }
        }
    }
}
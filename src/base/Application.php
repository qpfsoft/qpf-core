<?php
namespace qpf\base;

use qpf;
use qpf\bootstrap\BootstrapInterface;
use qpf\bootstrap\FacadeBootstrap;
use qpf\configs\Config;
use qpf\error\Error;
use qpf\bootstrap\ServiceBootstrap;
use qpf\configs\Env;

/**
 * 应用程序基类
 * 
 * @property Cookie $cookie
 * @property Config $config 配置管理器
 * @property Captcha $captcha 验证码
 * @property Db $db 数据库操作
 * @property Env $env 环境变量
 * @property Event $event 事件处理程序
 * @property \qpf\error\Error $error 错误处理程序
 * @property Url $url url管理
 * @property Middleware $middleware 中间件
 * @property Lang $lang 语言包
 * @property Request $request 请求对象
 * @property Response $response 响应处理程序
 * @property Session $session 会话参数
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
     * 字符集
     * @var string
     */
    protected $charset = 'UTF-8';
    /**
     * 调试模式
     * @var bool|int
     */
    protected $debug = true;
    /**
     * 应用运行环境
     * @var string
     */
    protected $runenv;
    /**
     * 应用程序URL
     * @var string
     */
    protected $baseurl = 'http://localhost';
    /**
     * 语言环境
     * @var string
     */
    protected $locale = 'zh';
    /**
     * 回退语言环境
     * @var string
     */
    protected $fallback_locale = 'zh';
    /**
     * 应用程序时区
     * @var string
     */
    protected $timezone = 'Asia/Shanghai';
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
     * 应用入口文件
     * @var string
     */
    protected $scriptName;
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
        $this->isInit = true;
        
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
        
        // 设置系统时区
        $this->setTimeZone($this->timezone);
        
        // 加载应用依赖文件
        $this->appInitFile();
        $this->debuginit();
        
        $this->event->trigger('AppInit');
        
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
     * 返回字符集
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }
    
    /**
     * 应用程序URL
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseurl;
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
     * 返回当前语言类型
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
    
    /**
     * 返回回滚语言类型
     * @return string
     */
    public function getFallbackLocale()
    {
        return $this->fallback_locale;
    }
    
    /**
     * 引导程序
     */
    protected function bootstrap()
    {
        $this->scriptName = $this->getScriptName();
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
     * 返回当前请求的脚本名 - 入口文件名
     * @return string
     */
    protected function getScriptName()
    {
        if (isset($_SERVER['SCRIPT_FILENAME'])) {
            $file = $_SERVER['SCRIPT_FILENAME'];
        } elseif (isset($_SERVER['argv'][0])) {
            $file = realpath($_SERVER['argv'][0]);
        }
        
        return isset($file) ? pathinfo($file, PATHINFO_FILENAME) : '';
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
        
        // 框架总配置模式 - web单文件
        if(is_file($this->configPath . DIRECTORY_SEPARATOR . 'web.php')) {
            $files[] = $this->configPath . DIRECTORY_SEPARATOR . 'web.php';
            
            // 框架散配置模式 - 目录下所有文件
        } elseif (is_dir($this->configPath)) {
            $files = glob($this->configPath . DIRECTORY_SEPARATOR . '*' . $this->configExt);
        }

        foreach ($files as $file) {
            $this->config->load($file, pathinfo($file, PATHINFO_FILENAME));
        }
        
        QPF::trace($files, 'config');
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
        $this->debug = $this->env->get('app_debug', $this->debug);
        $this->runenv = $this->env->get('app_runenv', $this->runenv);
        $this->error->register();
        
        if(!$this->debug) {
            ini_set('display_errors', 'Off');
        } elseif (!$this->isConsole()) {
            if (ob_get_level() > 0) {
                $output = ob_get_clean();
            }
            ob_start();
            if (!empty($output)) {
                echo $output;
            }
        }
    }
    
    /**
     * 是否运行在命令行
     * @return bool
     */
    public function isConsole()
    {
        return PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg';
    }
}
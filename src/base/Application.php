<?php
namespace qpf\base;

use qpf\bootstrap\ErrorBootstrap;

class Application extends Service
{
    use PathPropertyTrait;
    
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
    protected $debug = false;
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
        ErrorBootstrap::class
    ];
    
    /**
     * 初始化应用程序
     * @return $this
     */
    public function init()
    {
        if ($this->isInit) {
            return;
        }
        
        $this->bootstrap();
        
        
        $this->isInit = true;
        
        return $this;
    }
    
    /**
     * 引导程序
     */
    protected function bootstrap()
    {
        $this->instance(static::class, $this);
        $this->scriptName = realpath($this->getScriptName());
        $this->qpfPath = dirname(__DIR__);
        $this->vendorPath = strchr(__DIR__, 'qpfsoft', true);
        $this->rootPath = dirname($this->vendorPath);
        $this->qpfsoftPath = $this->vendorPath . DIRECTORY_SEPARATOR . 'qpfsoft';
        $this->zonePath = $this->rootPath . DIRECTORY_SEPARATOR . $this->zonename;
        $this->webPath = dirname($this->scriptName);
        parent::bootstrap();
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
}
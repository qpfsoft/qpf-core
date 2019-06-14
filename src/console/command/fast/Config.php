<?php
namespace qpf\console\command\fast;

use qpf\builder\template\PhpFile;
use qpf\builder\code\ArrayCode;
use qpf\base\Application;

/**
 * 快速的应用配置初始化
 */
class Config
{
    /**
     * 应用程序
     * @var Application
     */
    protected $app;
    /**
     * 当前要处理的模块
     * @var string
     */
    protected $module;
    /**
     * 配置目录路径
     * @var string
     */
    private $configPath;
    /**
     * app配置目录
     * @var string
     */
    private $appConfigPath;
    /**
     * 配置扩展名
     * @var string
     */
    private $configExt;
    /**
     * 运行目录路径
     * @var string
     */
    private $runtimePath;
    /**
     * 应用程序目录
     * @var string
     */
    private $appPath;
    
    /**
     * 构造函数
     * @param Application $app 应用程序
     * @param string $module 模块名
     */
    public function __construct(Application $app, $module = null)
    {
        $this->app = $app;
        $this->module = $module;
        $this->init();
    }
    
    protected function init()
    {
        $this->configPath = $this->app->getConfigPath();
        if(!empty($this->module)) {
            $this->appConfigPath = $this->configPath . '/' . $this->module;
        }
        $this->configExt = $this->app->getConfigExt();
        $this->runtimePath = empty($this->module) ? $this->app->getRuntimePath() :
        $this->app->getRuntimePath() . '/' . $this->module;
        
        
        $this->appPath = empty($this->module) ? $this->app->getZonePath() :
        $this->app->getZonePath() . '/' . $this->module;
    }

    /**
     * 加载配置文件
     * @return array
     */
    protected function loadConfigs(): array
    {
        // 配置列表
        $files = [];
        
        if (empty($this->module)) {
            // 框架散配置模式 - 目录下所有文件
            if (is_dir($this->configPath)) {
                $files = glob($this->configPath . '/*' . $this->configExt);
            }
        } else {
            if (is_dir($this->appPath . '/config')) {
                $files = array_merge($files, glob($this->appPath . '/config/*' . $this->configExt));
            } elseif (is_dir($this->appConfigPath)) {
                $files = array_merge($files, glob($this->appConfigPath . '/*' . $this->configExt));
            }
        }

        $conf = [];
        foreach ($files as $file) {
            $conf[pathinfo($file, PATHINFO_FILENAME)] = include $file;
        }
        
        return $conf;
    }
    
    /**
     * 事件
     * 加载@app/event.php | @app/module/event.php
     * @return array
     */
    protected function loadEventConfig(): array
    {
        $file = $this->appPath . '/event.php';
        if (is_file($file)) {
            return include $file;
        }
        
        return [];
    }
    
    /**
     * 服务提供商
     * 加载@app/provider.php | @app/module/provider.php
     * @return array
     */
    protected function loadProvider(): array
    {
        $file = $this->appPath . '/provider.php';
        if (is_file($file)) {
            return include $file;
        }
        
        return [];
    }
    
    /**
     * 公共函数库
     * 加载@app/common.php | @app/module/common.php
     * @return string
     */
    protected function loadCommon(): string
    {
        $file = $this->appPath . '/common.php';
        $content = '';
        
        if (is_file($file)) {
            $content = php_strip_whitespace($file);
            if (strncasecmp('<?php', $content, 5) === 0){
                $content = substr($content, 5);
            }
        }
        
        return $content;
    }
    
    /**
     * 生成配置快速文件
     * @param string $filename 保存文件位置
     */
    public function build()
    {
        $tpl = new PhpFile();
        $tpl->comment = [
            '该文件由系统生成, 重新生成的命令`php qpf fast:config`'
        ];
        
        $args = [];
        
        // 加载全部配置
        $args['configs'] =  ArrayCode::build($this->loadConfigs());
        // 加载服务提供商
        $args['provider'] = ArrayCode::build($this->loadProvider());
        $args['common'] = $this->loadCommon();
        
        // 加载事件
        $event = $this->loadEventConfig();
        $args['event'] = ArrayCode::build(isset($event['event']) ? $event['event'] : []);
        $args['listen'] = ArrayCode::build(isset($event['listen']) ? $event['listen'] : []);
        $args['subscribe'] = ArrayCode::build(isset($event['subscribe']) ? $event['subscribe'] : []);
        
        
        $content = $tpl->render($this->getContent(), $args);
        
        $tpl->setContent($content);
        
        
        $result = $tpl->save($this->runtimePath . '/init.php');
        
        return $result !== false;
    }
    
    
    
    /**
     * 获取内容
     * @return string
     */
    protected function getContent(): string
    {
        return <<<tpl
{:common}
\QPF::\$app->config->set({:configs});
\QPF::\$app->binds({:provider});
\QPF::\$app->event->bindEvents({:event});
\QPF::\$app->event->addEventlisteners({:listen});
\QPF::\$app->event->subscribe({:subscribe});
tpl;
    }
}
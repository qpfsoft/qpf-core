<?php
use qpf\base\Application;
use qpf\deunit\TestUnit;

include __DIR__ . '/../boot.php';

/**
 * 应用程序测试
 */
class ApplicationTest extends TestUnit
{
    public $app;
    
    public function setUp()
    {
        // 新版应用程序将不再依赖配置
        $this->app = new Application();
    }
    
    /**
     * 打印app实例
     * 
     * 直接创建的应用程序实例, 就可以访问环境路径
     */
    public function testAppInstance()
    {
        return [
            'qpf_path'      => $this->app->getQpfPath(),
            'qpfsoft_path'  => $this->app->getQpfsoftPath(),
            'zone_name'     => $this->app->getZonename(),
            'zone_path'     => $this->app->getZonePath(),
            'root_path'     => $this->app->getRootPath(),
            'vendor_path'   => $this->app->getVendorPath(),
            'config_path'   => $this->app->getConfigPath(),
            'runtime_path'  => $this->app->getRuntimePath(),
            'route_path'    => $this->app->getRoutePath(),
        ];
    }
    
    /**
     * 应用程序初始化
     * 
     * - 注册服务类到容器
     * - 获取.env 环境参数
     * - 载入基础配置, 应用全局配置
     */
    public function testAppInit()
    {
        $this->app->init();
        $arr = get_obj_vars($this->app);
        $app = $arr[2];
        return [
            'providers' => $app['providers'],
            'binds'     => $app['binds'],
            'instance'  => $app['instance'],
            'debug'     => $app['debug'],
            'startTime' => $app['startTime'],
            'startMem'  => $app['startMem'],
            'configExt' => $app['configExt'],
            'runenv'    => $app['runenv'],
            'isInit'    => $app['isInit'],
            'booted'    => $app['booted'],
            'deferProviders'    => $app['deferProviders'],
            'asyncProviders'    => $app['asyncProviders'],
        ];
    }
    
    /**
     * 显示已加载的env变量
     */
    public function testShowEnv()
    {
        return $this->app->env->get();
    }
    
    /**
     * 显示已加载的配置
     */
    public function testShowConfig()
    {
        return $this->app->config->get();
    }
    
    /**
     * 设置当前应用程序 - 并初始化环境
     * 
     * - 注意会覆盖运行目录
     */
    public function testSetApp()
    {
        $this->app->setApp('home');
        
        return [
            'app_name' => $this->app->getName(),
            'app_namespace' => $this->app->getNamespace(),
            'app_path'  => $this->app->getAppPath(),
            'runtime_path'  => $this->app->getRuntimePath(),
        ];
    }
}

echor(ApplicationTest::runTestUnit());
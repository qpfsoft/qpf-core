<?php
namespace qpf\bootstrap;

use qpf;
use qpf\provider\ConfigProvider;
use qpf\provider\EnvProvider;
use qpf\provider\LogProvider;
use qpf\provider\LangProvider;
use qpf\error\ErrorProvider;
use qpf\web\Web;
use qpf\web\Cookie;
use qpf\web\Url;
use qpf\web\Request;
use qpf\router\Router;
use qpf\response\Response;
use qpf\base\Event;
use qpf\installer\Install;
use qpf\base\Application as App;


/**
 * 服务引导程序
 */
class ServiceBootstrap implements BootstrapInterface
{
    /**
     * 服务提供商
     * @var array
     */
    protected $providers = [
        EnvProvider::class,
        ConfigProvider::class,
        ErrorProvider::class,
        LangProvider::class,
        LogProvider::class,
    ];
    
    /**
     * 服务注册
     * @var array
     */
    protected $service = [
        'event' => Event::class,
        'request' => Request::class,
        'url'       => Url::class,
        'web'       => Web::class,
        'cookie'    => Cookie::class,
        'route'     => Router::class,
        'response'  => Response::class,
        'install'   => Install::class,
    ];
    
    /**
     * 引导程序
     */
    public function bootstrap(App $app)
    {
        // 注册服务提供商
        $app->setProviders($this->providers);
        
        // 单例服务
        $app->binds($this->service, true);
        
        // 注册别名路径
        QPF::apaths()->setAliases([
            'root'  => $app->getRootPath(),
            'qpf'   => $app->getQpfPath(),
            'qpfsoft'   => $app->getQpfsoftPath(),
            'app'       => $app->getZonePath(),
            'web'       => $app->getWebPath(),
        ]);
    }
}
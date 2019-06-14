<?php
namespace qpf\web;

use qpf;
use qpf\base\Application as app;
use qpf\error\HttpException;
use qpf\base\Module;
use qpf\base\Core;
use qpf\dispatch\Dispatch;
use qpf\dispatch\action\ActionBase;

/**
 * 网页服务处理程序
 */
class Web extends Core
{
    /**
     * 应用程序
     * @var app
     */
    protected $app;
    /**
     * 请求解析任务是否完成
     * @var string
     */
    private $isTaskEnd = false;
    /**
     * 当前加载模块
     * @var array
     */
    protected $modules = [];
    /**
     * 严格解析模式
     * @var bool
     */
    public $strictMode = true;
    /**
     * 默认模块
     * @var string
     */
    public $defaultModule = 'index';
    /**
     * 默认控制器
     * @var string
     */
    public $defaultController = 'index';
    /**
     * 默认操作
     * @var string
     */
    public $defaultAction = 'index';
    /**
     * 是否启用路由
     * @var bool
     */
    public $onRoute = false;
    /**
     * 路由严格模式
     * @var string
     */
    public $strictRoute = false;
    /**
     * 是否启用路由解析缓存
     * @var bool
     */
    public $routeCache = false;
    /**
     * 应用绑定到域名
     * @var array
     */
    public $appDomain = [];
    /**
     * 应用设置URL访问别名
     * - 设置后原名将禁止访问
     * @var array
     */
    public $appAlias = [];
    /**
     * 允许访问的APP
     * @var array
     */
    public $appAllow = [];
    /**
     * 拒绝访问的APP
     * @var array
     */
    public $appDeny = [];

    /**
     * 构造函数
     * @param Application $app
     */
    public function __construct(app $app, array $config = [])
    {
        $this->app = $app;
        
        if (empty($config)) {
            $config = $this->app->config->group('web');
        }

        parent::__construct($config);
    }
    
    /**
     * 检查应用程序是否存在
     * @param string $name 应用名称
     * @return bool
     */
    public function isApp(string $name): bool
    {
        if (is_dir($this->app->getZonePath() . '/' . $name . '/controller')) {
            return true;
        } elseif (isset($this->modules[$name])) { // 内置app模块
            return true;
        }
        
        return false;
    }

    /**
     * 应用绑定别名
     * @param string $alias 别名
     * @param string $app 应用名称
     * @return $this
     */
    public function bindAlias(string $alias, string $app)
    {
        $this->appAlias[$alias] = $app;
        
        return $this;
    }
    
    /**
     * 获取应用别名
     * @param string $name 应用名称
     * @return string|null 返回应用的别名
     */
    public function getAppAlias(string $name): string
    {
        $alias = array_search($name, $this->appAlias);
        return $alias ?: '';
    }
    
    /**
     * 绑定域名到指定应用
     * @param string $domian 完整域名, 或子域名
     * @param string $app 应用名称
     * @return $this
     */
    public function bindDomain($domian, $app)
    {
        $this->appDomain[$domian] = $app;
        
        return $this;
    }
    
    /**
     * 获取应用绑定到的域名
     * @param string $app 应用名称
     * @return string 返回完整域名或子域名
     */
    public function getAppDomain($app): string
    {
        $domain = array_search($app, $this->appDomain);
        return $domain ?: '';
    }
    
    
    
    /**
     * 运行程序
     */
    public function run()
    {
        $this->app->init();
        
        $request = $this->app->request;
        
        $dispatch = $this->parseRequest($request);

        if ($dispatch instanceof ActionBase) {
            $response = $dispatch->run();
        }
        
        $response->send();
    }
    
    /**
     * 创建模块
     * @param string $name 模块名
     * @return Module|false
     */
    public function createModule($name)
    {
        if (isset($this->modules[$name])) {
            if (is_array($this->modules[$name])) {
                return $this->modules[$name] = new Module([
                    'name'  => $name,
                    'namespace' => $name,
                    'path'  => $this->modules[$name]['path'],
                    'runtimePath'   => $this->app->getRuntimePath() . DIRECTORY_SEPARATOR . $name, 
                    'parent'    => $this->app,
                ]);
            }
            return $this->modules[$name];
        }

        if ($this->isApp($name)) {
            return $this->modules[$name] = new Module([
                'name'  => $name,
                'alias' => $this->getAppAlias($name),
                'namespace' => $this->app->getZonename() . '\\' . $name,
                'path'  => $this->app->getZonePath() . DIRECTORY_SEPARATOR . $name,
                'runtimePath'   => $this->app->getRuntimePath() . DIRECTORY_SEPARATOR . $name, 
                'parent'    => $this->app,
            ]);
        }
        
        return false;
    }
    
    /**
     * 解析请求
     * @param Request $request
     * @return array [route => 请求路线, param => 查询参数]
     */
    protected function parseRequest(Request $request)
    {
        $path = $request->path();

        // 包含点, 说明携带不允许的伪静态后缀名
        if (strpos($path, '.') !== false) {
           throw new HttpException(404);
        }
        
        // 检查URL
        $this->checkUrl($request);
  
        // 匹配路由
        if ($this->onRoute) {
            $this->app->route->cahce($this->routeCache);
            $result = $this->app->route->check($request, $path); 

            if ($result) {
                return Dispatch::create($result);
            } elseif ($this->strictRoute) {
                throw new HttpException('404', QPF::lang('qpf/web', 'strict route mode'));
            }
        }
        
        // 解析访问的应用名
        $name = $this->parseAccessApp($path);
        // 补全请求路径
        $path = explode('/', $request->path());
        if (count($path) > 2) {
            throw new HttpException(404, QPF::lang('request path error, E.g:') . '`app/controller/action`');
        }
        if (empty($path[0])) {
            $path[0] = $this->defaultController;
        }
        if (empty($path[1])) {
            $path[1] = $this->defaultAction;
        }
        array_unshift($path, $name);

        return Dispatch::create([
            'type' => 'controller',
            'action' => $path,
            'param' => $request->param(),
        ]);
    }
    
    /**
     * 检查URL地址是否允许访问
     * @param Request $request
     * @throws HttpException
     */
    private function checkUrl(Request $request)
    {
        // 禁止重复指定访问路径!
        $pathinfo = trim($request->baseUrl(), '/');
        $var_pathinfo = $request->pathinfo();
        if (!empty($var_pathinfo) && !empty($pathinfo)) {
            if ($var_pathinfo != $pathinfo) {
                throw new HttpException(404, QPF::lang('qpf/web', 'url pahtinfo repeat'));
            }
        }
    }
    
    /**
     * 解析指定路径访问的应用名称
     * @param string $path 访问路径
     * @return string
     */
    private function parseAccessApp($path)
    {
        // 开启解析任务
        $this->isTaskEnd = false;
        
        // 检查域名绑定到应用
        if (!empty($this->appDomain)) {
            $app = $this->getDomainBindApp(
                $this->app->request->host(),
                $this->app->request->subDomain());
            
            $this->isTaskEnd = $app === null ? false : true;
        }
        
        if (!$this->isTaskEnd) {
            // 域名未绑定到应用, 截取出path中的模块名
            $app = $this->subPathFirst($path);

            if (!empty($app)) {
                // 检查应用别名
                if (!empty($this->appAlias)) {
                    $name = $this->getAliasBindApp($app);

                    if ($name !== null) {
                        // 将别名替换为原名
                        $app = $name;
                    }
                    
                    // 无别名替换
                    $this->isTaskEnd = $name === null ? false : true;
                }
                
                // 检查是否允许, 是否拒绝 , 仅检查用户要访问应用
                if (!$this->isTaskEnd) {
                    $this->checkAllowApp($app);
                    $this->checkDenyApp($app);
                }
                
            } else {
                // 路径为空, 访问默认模块
                $app = $this->defaultModule;
                $this->isTaskEnd = true;
            }
        }
        
        // 访问不存在的应用
        if (!$this->isApp($app)) {
            
            if ($this->strictMode) {
                // 严格模式, 应用不存在!
                throw new HttpException(404, QPF::lang('qpf/web', 'App not found'));
            } else {
                // 兼容模式, 请求路径第一段非应用, 将重定向到默认模块, 作为控制器
                $this->app->request->setPathinfo($app . '/' . $this->app->request->pathinfo());
                $app = $this->defaultModule;
            }
            
        }
        
        return $app;
    }

    /**
     * 获取域名或子域名绑定的应用名称
     * @param string $domain 完整域名, 主机名
     * @param string $sub 子域名
     * @return string|null 返回域名绑定的应用名称, 若未绑定则为`null`
     */
    protected function getDomainBindApp(string $domain, string $sub)
    {
        $app = $this->getBindApp($domain);

        return $app === null ? $this->getBindApp($sub): null;
    }
    
    /**
     * 获取域名绑定的应用名称
     * @param string $domain 完整域名, 或子域名
     * @return string|null 返回域名绑定的应用名称, 若未绑定则为`null`
     */
    protected function getBindApp(string $domain)
    {
        if (isset($this->appDomain[$domain])) {
            return $this->appDomain[$domain];
        }
        
        return null;
    }
    
    /**
     * 截取请求路径第一段
     * @param string $path 路径信息
     * @return string 返回截取的部分
     */
    protected function subPathFirst(string $path)
    {
        $first = current(explode('/', $path));
        
        if ($first) {
            // 从URl中去除app模块的别名
            $this->app->request->setRoot($first);
            $this->app->request->setPathinfo(strpos($path, '/') ? ltrim(strstr($path, '/'), '/') : '');
        } else {
            $first = '';
        }
        
        return $first;
    }
    
    /**
     * 获取指定别名绑定的应用名称
     * @param string $alias 别名
     * @return string|null 返回应用名称
     */
    protected function getAliasBindApp(string $alias)
    {
        if (isset($this->appAlias[$alias])) {
            return $this->appAlias[$alias];
        } elseif ($alias && in_array(strtolower($alias), $this->appAlias, true)) {
            throw new HttpException(404, QPF::lang('qpf/web', 'Reject apps with aliases'));
        }
        
        return null;
    }
    
    /**
     * 检查是否为允许访问的应用
     * @param string $app 应用名称
     * @param bool $throw 是否抛出异常, 默认`true`
     * @return bool 返回true代表允许访问, false为拒绝
     * @throws HttpException
     */
    public function checkAllowApp(string $app, bool $throw = true)
    {
        // 未指定时, 允许访问所有
        if (empty($this->appAllow)) {
            return true;
        }
        
        if (in_array(strtolower($app), $this->appAllow, true)) {
            return true;
        }
        
        if ($throw) {
            throw new HttpException(404);
        }
        
        return false;
    }

    /**
     * 检查是否为拒绝访问的应用
     * @param string $app 应用名称
     * @param bool $throw 是否抛出异常, 默认`true`
     * @return bool 返回true代表允许访问, false为拒绝
     * @throws HttpException
     */
    public function checkDenyApp(string $app, bool $throw = true)
    {
        // 未指定时, 允许访问所有
        if (empty($this->appDeny)) {
            return true;
        }
        
        // 检查value键值是否存在, in_array大小写敏感
        if (in_array(strtolower($app), $this->appDeny, true)) {
            if ($throw) {
                throw new HttpException(404);
            }
            return false;
        }
        
        return true;
    }
    
    /**
     * 加载应用文件
     * @param string $appName 应用名
     * @return void
     */
    protected function loadApp(string $appName)
    {
        if (empty($appName)) return;
        $this->app->setApp($appName);
        $this->app->request->setApp($appName);
        
        // 加载APP初始化文件
        if (is_file($this->app->getRuntimePath() . '/init.php')) {
            include $this->app->getRuntimePath() . '/init.php';
        } else {
            if (is_file($this->app->getAppPath() . '/common.php')) {
                include_once $this->app->getAppPath() . '/common.php';
            }
            
            if (is_file($this->app->getAppPath() . '/event.php')) {
                $this->app->loadEvent(include $this->app->getAppPath() . '/event.php');
            }
            
            if (is_file($this->app->getAppPath() . '/provider.php')) {
                $this->app->bindProvider(include $this->app->getAppPath() . '/provider.php');
            }
            
            if (is_file($this->app->getAppPath() . '/middleware.php')) {
                $this->app->middleware->import(include $this->app->getAppPath() . '/middleware.php');
            }
            
            $files = [];
            
            if (is_dir($this->app->getAppPath() . '/config')) {
                $files = array_merge($files, glob($this->app->getAppPath() . '/config/*' . $this->app->getConfigExt()));
            } elseif (is_dir($this->app->getConfigPath() . '/' . $appName)) {
                $files = array_merge($files, glob($this->app->getConfigPath() . '/' . $appName . '/*' . $this->app->getConfigExt()));
            }
            
            foreach ($files as $file) {
                $this->app->config->load($file, pathinfo($file, PATHINFO_FILENAME));
            }
        }
    }
}
<?php
namespace qpf\base;

use qpf;
use qpf\web\Controller;
use qpf\exceptions\ParameterException;

/**
 * 应用模块
 */
class Module extends Core
{
    /**
     * 模块名
     * @var string
     */
    public $name;
    /**
     * URl访问别名
     * 设置该属性后, 将不能使用[[$name]]访问该模块
     * @var string
     */
    public $alias;
    /**
     * 模块根命名空间
     * @var string
     */
    public $namespace;
    /**
     * 模块根路径
     * @var string
     */
    public $path;
    /**
     * 前模块的runtime目录
     * @var string
     */
    public $runtimePath;
    
    /**
     * 父模块
     * @var Application
     */
    protected $parent;
    
    protected function boot()
    {
        $this->loadModule();
    }

    /**
     * 创建控制器
     * @param string $name 控制器名称
     * @return Controller
     */
    public function createController(string $name)
    {
        $class = $this->buildClassName($name, 'controller');
        if (class_exists($class)) {
            $controller =  QPF::create($class);
            return get_class($controller) === $class ? $controller : null;
        } else {
            $empty = $this->buildClassName('error', 'controller');
            if (class_exists($empty)) {
                return QPF::create($empty);
            } else {
                throw new ParameterException(\QPF::lang('qpf/web', 'Controller does not exist') . ': ' . $class);
            }
        }
    }
    
    /**
     * 生成模块内的类名
     * @param string $app 模块名
     * @param string $name 类基础名
     * @param string $layer 层名称
     * @return string
     */
    public function buildClassName(string $name, string $layer)
    {
        return $this->namespace . '\\' . $layer .'\\' . ucfirst($name) . ucfirst($layer);
    }
    
    /**
     * 执行控制器操作
     * @param string $controller 控制器名称
     * @param string $action 操作名称
     * @param string $param 请求参数
     * @return mixed 返回结果
     */
    public function action(string $controller, string $action, array $param)
    {
        $controller = $this->createController($controller);
        $action = $this->parseActionName($action);

        if (method_exists($controller, $action)) {
            $result = call_user_func_array([$controller, $action], $param);
        } else {
            $actionEmpty = $this->parseActionName('empty');
            
            if (method_exists($controller, $actionEmpty)) {
                $result = call_user_func_array([$controller, $actionEmpty], $param);
            } else {
                throw new ParameterException(QPF::lang('qpf/web', 'action does not exist')  . get_class($controller) . '::'.$action);
            }
        }
        
        return $result;
    }
    
    /**
     * 解析控制器名称
     * ```
     *  'auth' => 'AuthController',
     *  'authController' => 'AuthController',
     *  'auth-controller' => 'AuthController'
     * ```
     * @param string $name 控制器名称
     * @return string
     */
    public function parseControllerName($name)
    {
        $suffix = 'Controller';
        
        if (substr($name, -strlen($suffix)) !== $suffix) {
            $name .= $suffix;
        }
        
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $name)));
    }
    
    /**
     * 解析操作名称
     * @param string $name 操作名称
     * @return string
     */
    public function parseActionName($name)
    {
        $prefix = 'action';

        if (strncasecmp($prefix, $name, strlen($prefix)) !== 0) {
            $name = $prefix . QPF::nameFormatToClass($name);
        }
        
        return $name;
    }
    
    /**
     * 加载模块文件
     */
    public function loadModule()
    {
        // 加载APP初始化文件
        if (is_file($this->runtimePath . '/init.php')) {
            // TODO 未知内容
            include $this->runtimePath . '/init.php';
        } else {
            if (is_file($this->path . '/common.php')) {
                include_once $this->path . '/common.php';
            }
            
            if (is_file($this->path . '/event.php')) {
                $this->parent->loadEvent(include $this->appPath . '/event.php');
            }
            
            if (is_file($this->path . '/provider.php')) {
                $this->parent->bindProvider(include $this->appPath . '/provider.php');
            }
            
            if (is_file($this->path . '/middleware.php')) {
                $this->parent->middleware->import(include $this->appPath . '/middleware.php');
            }
            
            $files = [];
            
            if (is_dir($this->path . '/config')) {
                $files = array_merge($files, glob($this->path . '/config/*' . $this->parent->getConfigExt()));
            } elseif (is_dir($this->parent->getConfigPath() . '/' . $this->name)) {
                $files = array_merge($files, glob($this->parent->getConfigPath() . '/' . $this->name . '/*' . $this->parent->getConfigExt()));
            }
            
            foreach ($files as $file) {
                $this->parent->config->load($file, pathinfo($file, PATHINFO_FILENAME));
            }
        }
    }
}
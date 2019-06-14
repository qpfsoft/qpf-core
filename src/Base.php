<?php
// ╭───────────────────────────────────────────────────────────┐
// │ QPF Framework [Key Studio]
// │-----------------------------------------------------------│
// │ Copyright (c) 2016-2019 quiun.com All rights reserved.
// │-----------------------------------------------------------│
// │ Author: qiun <qiun@163.com>
// ╰───────────────────────────────────────────────────────────┘
namespace qpf;

use qpf\base\Application;
use qpf\exceptions\NotFoundException;
use qpf\exceptions\QlassException;
use qpf\web\Request;
use qpf\base\Container;
use qpf\autoload\Autoload;

class Base
{
    /**
     * 应用程序
     * @var Application
     */
    public static $app;
    
    /**
     * 版本号
     * @return string
     */
    public static function version()
    {
        return '0.1.0';
    }
    
    /**
     * 获得应用程序
     * @return Application
     */
    public static function app(Application $app = null)
    {
        if (is_object($app)) {
            static::$app = $app;
        } elseif (static::$app === null) {
            static::$app = new Application();
        }
        
        return static::$app;
    }
    
    /**
     * 是否有可用应用程序
     * @return bool
     */
    public static function hasApp()
    {
        return static::$app !== null ? true : false;
    }
    
    /**
     * 获得依赖解决容器
     * @return Container
     */
    public static function container()
    {
        return static::hasApp() ? static::$app : null;
    }
    
    /**
     * 获得当前请求对象
     * @return Request
     */
    public static function request()
    {
        return static::hasApp() ? static::$app->request : null;
    }

    /**
     * 命名格式为类 - 驼峰
     * @param string $name 名称
     * @param bool $ucfirst 首字母是否大写, 默认true
     * @return string
     */
    public static function nameFormatToClass($name, $ucfirst = true)
    {
        $name = preg_replace_callback('/_([a-zA-Z])/',
            function ($match) {
                return strtoupper($match[1]);
            }, $name);
        return $ucfirst ? ucfirst($name) : lcfirst($name);
    }
    
    /**
     * 命名格式为函数 - 小写_下划线分割
     * @param string $name 名称
     * @return string
     */
    public static function nameFormatToFunc($name)
    {
        return strtolower(trim(preg_replace('/([A-Z])/', '_\\1', $name), '_'));
    }
    
    /**
     * 创建对象实例
     * @param string|array $name 类或类定义
     * @param array $params 构造参数
     * @return object
     */
    public static function create($name, array $params = [])
    {
        if (is_string($name)) { // `qpf\Object`
            return static::container()->pull($name, $params);
        } elseif (is_array($name) && isset($name['$class'])) { 
            // ['$class'=> 'qpf\Obj', 'param'=>'val']
            $class = $name['$class'];unset($name['$class']);
            return static::container()->pull($class, $params, $name);
        } elseif (is_callable($name, true)) { // [obj|class, 'method']
            return static::container()->call($name, $params);
        } elseif (is_array($name)) {
            throw (new QlassException())->badConfig('$class');
        }
        
        throw (new QlassException())->badConfigType($name);
    }
    
    /**
     * 创建工厂对象实例
     * @param string $name 工厂类名
     * @param string $namespace 默认命名空间
     * @return mixed
     */
    public static function factory($name, $namespace = '', ...$args)
    {
        $class = false !== strpos($name, '\\') ? $name : $namespace . '\\' . ucwords($name);
        
        if (class_exists($class)) {
            return self::$app->callClass($class, $args);
        }
        
        throw new NotFoundException($class, 'class');
    }
    
    /**
     * 配置对象属性
     * @param object $object 对象实例
     * @param array $properties 属性配置数组
     * @return object
     */
    public static function qlass(&$object, $properties)
    {
        foreach ($properties as $name => $value) {
            $object->$name = $value;
        }
        
        return $object;
    }
    
    /**
     * 判定给定的参数是否可创建为对象
     * @param mixed $config
     * @return bool
     */
    public static function isQlass($config)
    {
        if (is_array($config) && isset($config['$class'])) {
            return true;
        }
        
        return false;
    }
    
    /**
     * 别名路径
     * @return \qpf\base\Apaths
     */
    public static function apaths()
    {
        return Autoload::apaths();
    }
    
    /**
     * 获得环境变量
     * @param string $name 变量名
     * @param mixed $default 默认值
     * @return mixed
     */
    public static function env($name, $default = null)
    {
        return static::$app->env->get($name, $default);
    }
    
    /**
     * 翻译消息
     * @param string $type 类别
     * @param string $message 消息
     * @return string
     */
    public static function lang($type, $message)
    {
        return static::app()->lang->translate($type, $message);
    }
    
    /**
     * 记录日志信息
     * @param string $msg 信息
     * @param string $level 级别
     * @return void
     */
    public static function log($msg, $level)
    {
        static::$app->log->log($msg, $level);
    }
    
    /**
     * 记录错误信息
     * @param string $msg
     * @param string $level
     */
    public static function error($msg)
    {
        static::$app->log->error($msg);
    }
    
    /**
     * 记录警告消息
     * @param string $msg
     * @return void
     */
    public static function warning($msg)
    {
        static::$app->log->warning($msg);
    }
    
    /**
     * 记录提示消息
     * @param string $msg
     * @return void
     */
    public static function notice($msg)
    {
        static::$app->log->notice($msg);
    }
    
    /**
     * 记录普通消息
     * @param string $msg
     * @return void
     */
    public static function info($msg)
    {
        static::$app->log->info($msg);
    }
    
    /**
     * 记录调试信息
     * @param string $msg
     * @return void
     */
    public static function debug($msg)
    {
        static::$app->log->debug($msg);
    }
    
    /**
     * 记录sql信息
     * @param string $msg
     * @return void
     */
    public static function sql($msg)
    {
        static::$app->log->sql($msg);
    }
    
    /**
     * 记录页面跟踪消息
     *
     * @param string $message 要记录的消息
     * @param string $type 消息类型, 默认`app`应用程序
     * @param boolean $log 是否记录日志, 默认false 不记录
     * @return array
     */
    static public function trace($message = null, $type = 'app', $log = false)
    {
        static $_trace = [];
        
        if ($message === null) {
            return $_trace;
        }

        $type = strtoupper($type);
        
        if (self::hasApp() && static::$app->request->isAjax() || $log) {
            return static::notice(print_r($message, true));
        }
        
        // 某类型消息大于100条将自动清除
        if (!isset($_trace[$type]) || count($_trace[$type]) > 100) {
            $_trace[$type] = [];
        }
        
        $_trace[$type][] = $message;
    }
}
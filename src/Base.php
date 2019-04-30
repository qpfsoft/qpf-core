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
    public static function app($app = null)
    {
        if (is_object($app)) {
            static::$app = $app;
        } elseif (static::$app === null) {
            static::$app = new Application();
        }
        
        return static::$app;
    }
    
    /**
     * 命名风格转换
     * ```
     * parseName('user_name', 'c'); // UserName
     * parseName('user_name', 'c', false); // userName
     * parseName('userName', 'f'); // user_name
     * ```
     * @param string $name 名称
     * @param string $type 转换成类型, `c`代表类(驼峰), 默认`f`代表函数(小写_分割)
     * @param bool $ucfirst 驼峰式首字母是否大写, 默认true.
     * @return string
     */
    public static function parseName($name, $type = 'f', $ucfirst = true)
    {
        if ($type == 'c') {
            $name = preg_replace_callback('/_([a-zA-Z])/',
                function ($match) {
                    return strtoupper($match[1]);
                }, $name);
            return $ucfirst ? ucfirst($name) : lcfirst($name);
        } elseif ($type == 'f') {
            return strtolower(trim(preg_replace('/([A-Z])/', '_\\1', $name), '_'));
        }
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
        /* if ($message === null) {
         return $_trace;
         } else {
         $info = print_r($message, true);
         $type = strtoupper($type);
         // ajax请求自动保存
         if (static::$app !== null && static::$app->request->isAjax() || $log) {
         static::notice($info);
         } else {
         // 某类型消息大于100条将自动清除
         if (!isset($_trace[$type]) || count($_trace[$type]) > 100) {
         $_trace[$type] = [];
         }
         $_trace[$type][] = $info;
         }
         } */
    }
}
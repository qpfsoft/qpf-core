<?php
namespace qpf\base;

use qpf;

/**
 * 单例对象
 * 
 * 可通过[[instance()]]静态方法全局调用对应唯一的对象实例
 * 
 * 该抽象类是实现类可静态访问单例, 是以类名注册.
 */
abstract class Single extends Core
{
    /**
     * 单例实例列表
     * @var array
     */
    private static $_instances = [];
    
    /**
     * 获得对象实例 [此入口-会缓存实例对象]
     * @param array $config 对象配置
     * @return static
     */
    public static function instance(array $config = [])
    {
        if (!isset(self::$_instances[static::class])) {
            self::$_instances[static::class] = QPF::create(static::class, $config);
        } elseif (!empty($config)) {
            QPF::qlass(self::$_instances[static::class], $config);
        }
        return self::$_instances[static::class];
    }
    
    /**
     * 销毁对象实例
     * @return void
     */
    public static function destroy()
    {
        if (key_exists(static::class, self::$_instances)) {
            unset(self::$_instances[static::class]);
        }
    }
    
    /**
     * 静态方法调用代理
     */
    public static function __callstatic($method, $params)
    {
        return call_user_func_array([static::instance(), $method], $params);
    }

    /**
     * 防止实例被克隆复制
     */
    private function __clone()
    {
    }
}
<?php
namespace qpf\base;

/**
 * 服务外观抽象类
 */
abstract class Facade
{
    /**
     * 应用程序
     * @var Application
     */
    protected static $app;
    
    /**
     * 获得Facade绑定的类名
     * @return string
     */
    abstract protected static function getFacadeClass();
    
    /**
     * 获取对象实例
     * @return object
     */
    final public static function instance()
    {
        return static::createFacadeInstance(static::getFacadeClass());
    }

    /**
     * 设置应用程序
     * @param Application $app
     * @return void
     */
    final public static function setDependencyApp($app)
    {
        self::$app = $app;
    }
    
    /**
     * 静态方法调用代理
     */
    final public static function __callstatic($method, $params)
    {
        return call_user_func_array([static::createFacadeInstance(), $method], $params);
    }
    
    /**
     * 创建Facade实例
     * @param string $class 类名或类标识
     * @return object
     */
    final protected static function createFacadeInstance($class = '')
    {
        $class = $class ?: static::class;
        
        $facadeClass = static::getFacadeClass();
        
        if($facadeClass) {
            $class = $facadeClass;
        }
        return self::$app->make($class);
    }
}
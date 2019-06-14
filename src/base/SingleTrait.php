<?php
namespace qpf\base;

/**
 * SingleFacade 单例特性类
 * 
 * 适用于不需要依赖注入的单例类
 */
trait SingleTrait
{
    /**
     * 当前单例对象
     * @var object
     */
    protected static $single;
    
    /**
     * 返回当前单例对象
     * @return object
     */
    public static function single()
    {
        if (self::$single === null) {
            self::$single = static::createSingle();
        }
        
        return self::$single;
    }
    
    /**
     * 子类实现创建单例对象 - 子类应重写
     * @return object
     */
    protected static function createSingle()
    {
        return new static();
    }
    
    /**
     * 方法代理
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        return call_user_func_array([self::single()], $params);
    }
    
    /**
     * 静态代理
     * @param string $name
     * @param array $arg
     * @return mixed
     */
    public static function __callstatic($name, $arg)
    {
        return call_user_func_array([self::single()], $arg);
    }
    
    /**
     * 单例实例应该调用[[single]]
     */
    private function __construct()
    {
    }
    
    /**
     * 防止实例被克隆复制
     */
    private function __clone()
    {
    }
}
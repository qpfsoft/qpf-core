<?php
namespace qpf\base;

use qpf\exceptions\CallException;

/**
 * QPF Core Class
 */
class Core implements Qlass
{
    
    /**
     * 构造函数
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->useConfig($config, false);
        $this->boot();
    }
    
    /**
     * 对象初始化
     * @return void
     */
    protected function boot()
    {
        
    }
    
    /**
     * 使用配置设置对象属性
     * @param array $config 属性配置数组
     * @param bool $hasCheck 是否检查属性存在, 当配置中有额外参数时应设置为`true`
     * @return void
     */
    protected function useConfig(array $config, $hasCheck = false)
    {
        foreach ($config as $property => $value) {
            if ($hasCheck && !property_exists($this, $property)) {
                continue;
            }
            
            $this->$property = $value;
        }
    }
    
    /**
     * 将对象转换为配置数组
     */
    public function toQlass()
    {
        $config = ['class' => $this->className()];
        foreach ($this as $property => $value) {
            if (is_object($value)) {
                continue;
            } elseif ($property == 'property') {
                continue;
            }
            $config[$property] = $value;
        }
        return $config;
    }
    
    /**
     * 设置属性
     * @param string $name 属性名
     * @param mixed $val 值
     */
    public function __set($name, $value)
    {
        throw (new CallException())->badPropertyCall([$this, $name], $value);
    }
    
    /**
     * 获得属性
     * @param string $name 属性名
     */
    public function __get($name)
    {
        throw (new CallException())->badPropertyCall([$this, $name]);
    }
    
    /**
     * 调用方法
     * @param string $name 方法名
     * @param mixed $params 参数
     */
    public function __call($name, $params)
    {
        throw (new CallException())->badMethodCall([$this, $name]);
    }
    
    /**
     * 静态调用
     * @param string $name
     * @param mixed $params
     */
    public static function __callstatic($name, $params)
    {
        throw (new CallException())->badMethodCall([$this, $name]);
    }
    
    /**
     * 判断对象是否有指定属性
     * @param string $name 属性名
     * @return bool
     */
    public function hasProperty($name)
    {
        return property_exists($this, $name);
    }
    
    /**
     * 判断对象是否有指定方法
     *
     * @param string $name 方法名
     * @return bool
     */
    public function hasMethod($name)
    {
        return method_exists($this, $name);
    }
    
    /**
     * 返回当前实例的类名
     * - get_called_class();
     * @return string
     */
    public static function className()
    {
        return static::class;
    }
}
interface Qlass{}
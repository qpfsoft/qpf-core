<?php
namespace qpf\base;

use qpf\exceptions\UnknownProperty;
use qpf\exceptions\UnknownMethod;

class Objct
{
    /**
     * 属性修改器代理
     * @param string $name 属性名
     * @param mixed $val 值
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        }
        
        throw new UnknownProperty('Setting unknown property: ' . get_class($this) . '::' . $name);
    }
    
    /**
     * 属性获取器代理
     * @param string $name 属性名
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        
        if (method_exists($this, $getter)) {
            $this->$getter($getter);
        }
        
        throw new UnknownProperty('Getting unknown property: ' . get_class($this) . '::' . $name);
    }
    
    /**
     * 实现isset检查属性是否设置
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        $getter = 'get' . $name;
        
        if (method_exists($this, $getter)) {
            return $this->$getter($getter) !== null;
        }
        
        return false;
    }
    
    /**
     * 实现unset属性值为null
     * @param string $name
     */
    public function __unset($name)
    {
        $setter = 'set' . $name;
        
        if (method_exists($this, $setter)) {
            $this->$setter(null);
        }
    }
    
    /**
     * 调用方法
     * @param string $name 方法名
     * @param mixed $params 参数
     */
    public function __call($name, $params)
    {
        throw new UnknownMethod('Call unknown method: ' . get_class($this) . '::' . $name);
    }
}
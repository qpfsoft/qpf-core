<?php
namespace qpf\base;

use qpf\exceptions\CallException;

/**
 * Property 对象属性快捷访问代理基类
 * 
 * 继承该类后可直接访问对象(受保护或私有)属性, 只需设置`get`获取和设置`set`方法
 * ```
 * class Foo extends Property
 * {
 *      private $name;
 *      
 *      // get method
 *      public function getName()
 *      {
 *          return $this->name;
 *      }
 *      // set method
 *      public function setName($value)
 *      {
 *          $this->name = $vlaue;
 *      }
 * }
 * 
 * $foo = new Foo();
 * 
 * $foo->name = 'qpf';
 * echo $foo->name; // qpf
 * ```
 * 
 * 注意
 * - 仅设置get属性方法, 代表只读属性
 * - 仅设置set属性方法, 代表只写属性
 * - 由于属性获取与设置代理机制, 类本身不一定真实存在该属性
 */
class Property extends Core
{
    
    /**
     * 属性获取方法代理
     * @param string $name 属性名
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get' . $name;
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        
        if (method_exists($this, 'set' . $name)) {
            throw new CallException('Getting write-only property: ' . static::class . '::' . $name);
        }
        
        throw new CallException('Getting unknown property: ' . static::class . '::' . $name);
    }
    
    /**
     * 属性设置方法代理
     * @param string $name 属性名
     * @param mixed $value 值
     * @return void
     */
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        
        if (method_exists($this, 'get' . $name)) {
            throw new CallException('Setting read-only property: ' . static::class . '::' . $name);
        }
        
        throw new CallException('Setting unknown property: ' . static::class . '::' . $name);
    }
    
    /**
     * 检查属性是否设置, 值不为`null`
     * @param string $name 属性名
     * @return bool
     */
    public function __isset($name)
    {
        $method = 'get' . $name;
        if (method_exists($this, $method)) {
            return $this->$method() !== null;
        }
        
        return false;
    }
    
    /**
     * 将属性设置为`null`
     * @param string $name 属性名
     */
    public function __unset($name)
    {
        $method = 'set' . $name;
        if (method_exists($this, $method)) {
            return $this->$method(null);
        }
        
        throw new CallException('Unsetting unknown or read-only property' . static::class . '::' . $name);
    }
    
    /**
     * 判断对象是否有指定属性
     *
     * @param string $name 属性名
     * @param bool $isset 是否检查属性真实存在
     * @return bool
     */
    public function hasProperty($name, $isset = true)
    {
        return $this->hasGetProperty($name, $isset) || $this->hasSetProperty($name, false);
    }
    
    /**
     * 判断属性是否有代理方法
     * @param string $name 属性名
     * @param bool $isset 是否检查属性真实存在
     * @return bool
     */
    public function hasSetProperty($name, $isset = true)
    {
        if (method_exists($this, 'set' . $name) || $isset && property_exists($this, $name)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * 判断属性是否有代理获取方法
     * 
     * @param string $name 属性名
     * @param bool $isset 是否检查属性真实存在
     * @return bool
     */
    public function hasGetProperty($name, $isset = true)
    {
        if (method_exists($this, 'get' . $name) || $isset && property_exists($this, $name)) {
            return true;
        }
        
        return false;
    }
}
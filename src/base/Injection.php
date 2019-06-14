<?php
namespace qpf\base;

use qpf\exceptions\NotFoundException;
use qpf\exceptions\CallException;
use qpf\exceptions\UnknownProperty;

/**
 * 注入对象
 * 
 * 通过静态方法可提前为类绑定新方法. 或实例化后动态绑定新属性.
 * 
 * [[::hook()]] 注入方法到对象实例
 * [[::hasHook()]] 检查是否注入指定方法
 * [[bind()]] 绑定属性到对象实例
 * [[hasBind()]] 检查是否绑定指定属性
 * 
 * 当类自身存在指定的方法, 掉用类自身定义的方法. 不存在时, 才会调用注入的方法.
 * 
 * ```
class A extends \qpf\core\Injection
{
    public $id = 1;
    
    public static function name()
    {
        return __CLASS__;
    }
    public function init()
    {
        return 'A-init';
    }
}

class B extends \qpf\core\Injection
{
    public static function name()
    {
        return __CLASS__;
    }
    public function run()
    {
        return 'B-run';
    }
}

// # 绑定方法, 方法名`show`, 闭包参数1, 默认传入基础对象, 即`A`的`$this`
A::hook('show', function(A $a){
    echor($a->id+1);
});
$a = new A();
$a->show(); // 2

// # 绑定静态方法, 闭包不再传入`$this`
A::hook('show', function($str){
    echor($str);
});
A::show('ok'); // 'ok'

// # 原样返回, 当方法体不是有效的回调
A::hook('bName', ['hi!']);
A::bName(); // ['hi!']

// # 回调 静态方法
A::hook('bName', ['B::name']); // 不支持, 返回 ['B::name']
A::hook('bName', ['B', 'name']);
echor(A::bName()); // B

// # 回调 对象方法
A::hook('bRun', ['B', 'run']); // 不再支持, 返回 ['B::name'], 因为用户无法接收B对象.
$b = new B();
A::hook('bRun', [$b, 'run']);
echor(A::bRun());

// # 绑定 属性
$a = new A();
$a->bind('user', 'qpf');
echor($a->user);
 * ```
 */
abstract class Injection extends Core
{
    /**
     * 静态注入的方法列表
     * @var array ['className' => [], ...]
     * 继承该类的实例共用该方法列表
     */
    private static $in_method = [];
    /**
     * 绑定的属性列表
     * @var array
     * 继承该类的实例使用自身的属性列表
     */
    private $in_param = [];
    
    /**
     * 注入方法到对象
     *
     * ```
     * # 动态注入方法
     * InjectObject::hook('url', 'getUrl'); // url为调用的方法名
     * function getUrl($url) {
     *  return 'http://'. $url;
     * }
     *
     * 等价于
     * InjectObject::hook('url', function($url){
     *  return 'http://'. $url;
     * });
     *
     * // 使用
     * InjectObject->user('d.com');
     *
     * # 传递自身实例到回调
     * InjectObject::hook('io', function($io){
     *  return $io::className(); // InjectObject
     * });
     * ```
     *
     * @param string|array $method 方法名称, 数组格式可导入多个方法.
     * @param mixed $callback callable回调方法或函数名称或对象实例
     * @return void
     */
    public static function hook($method, $callback = null)
    {
        if (is_array($method)) {
            self::$in_method[static::className()] = array_merge(self::$in_method[static::className()], $method);
        } else {
            self::$in_method[static::className()][$method] = $callback;
        }
    }
    
    /**
     * 检查是否注入指定方法
     * @param string $method 方法名称
     * @return boolean
     */
    public static function hasHook($method)
    {
        return isset(self::$in_method[static::className()][$method]);
    }
    
    /**
     * 实现可调用注入的方法
     *
     * @param string 方法名称
     * @param array 传入的参数
     */
    public function __call($method, $params)
    {
        // 类是否注入过方法
        if (isset(self::$in_method[static::className()])) {
            if (array_key_exists($method, self::$in_method[static::className()])) {
                if (is_callable(self::$in_method[static::className()][$method])) {
                    array_unshift($params, $this); // 回调传入被注入类的实例
                    return call_user_func_array(self::$in_method[static::className()][$method], $params);
                } else {
                    return self::$in_method[static::className()][$method];
                }
            }
        }
        
        throw new CallException('Method not exist '. static::className() . '::' . $method . '();');
    }
    
    /**
     * 实现可静态调用注入的方法
     * @param string $method
     * @param array $params
     */
    public static function __callstatic($method, $params)
    {
        // 类是否注入过方法
        if (isset(self::$in_method[static::className()])) {
            if (array_key_exists($method, self::$in_method[static::className()])) {
                if (is_callable(self::$in_method[static::className()][$method], true)) {
                    return call_user_func_array(self::$in_method[static::className()][$method], $params);
                } else {
                    return self::$in_method[static::className()][$method];
                }
            }
        }
        
        throw new CallException('Method not exist '. static::className() . '::' . $method . '();');
    }
    
    
    /**
     * 绑定属性到对象
     * @param string|array $name 属性名称
     * @param mixed $value 属性值
     * @return void
     */
    public function bind($name, $value)
    {
        if (is_array($name)) {
            $this->in_param = array_merge($this->in_param, $name);
        } else {
            $this->in_param[$name] = $value;
        }
    }
    
    /**
     * 检查是否绑定指定属性
     * @param string $name 属性名称
     * @return boolean
     */
    public function hasBind($name)
    {
        return isset($this->in_param[$name]);
    }
    
    /**
     * 实现可获得绑定的属性
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->in_param[$name])) {
            return $this->in_param[$name];
        } else {
            throw new UnknownProperty(static::className() . '::' . $name);
        }
    }
    
    /**
     * 绑定属性到对象
     * @return void
     */
    public function __set($name, $value)
    {
        $this->in_param[$name] = $value;
    }
    
    /**
     * 判断是否绑定指定的属性
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->in_param[$name]);
    }
}
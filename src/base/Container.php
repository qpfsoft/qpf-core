<?php
namespace qpf\base;

use qpf;
use qpf\exceptions\NotFoundException;
use qpf\exceptions\CallException;
use qpf\exceptions\QlassException;
use qpf\exceptions\ParameterException;

class Container
{
    /**
     * 绑定依赖定义
     * @var array
     */
    public $binds = [];
    /**
     * 构造参数配置
     * @var array
     */
    public $params = [];
    /**
     * 单例实例
     * @var array
     */
    public $instance = [];
    
    /**
     * 获取对象实例
     * @param string $class 类名, 接口名, 别名
     * @param array $params 构造参数
     * @param array $option 对象属性配置
     * @param bool $reset 是否创建新对象, 默认`false`
     * @return object
     */
    public function pull($class, $params = [], $option = [], $reset = false)
    {
        return $this->make($class, $params, $option, $reset);
    }
    
    /**
     * 获取容器中的对象实例
     * @param string $name 类名, 接口名, 别名
     * @return object
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->make($name);
        }
        
        throw new NotFoundException($name, 'class');
    }
    
    /**
     * 是否存在指定的依赖关系
     * @param string $name 类名、接口名或别名
     * @return bool
     */
    public function has($name)
    {
        return $this->isBind($name);
    }
    
    /**
     * 绑定一个依赖关系
     * @param string $name 依赖名称, 即`类名, 接口名, 别名`
     * @param array $config 依赖关系, 即`类名, 对象或定义数组, 闭包`
     * @param array $params 构造参数
     * @param bool $single 是否单例
     * @return $this
     */
    public function bind($name, array $config = [], array $params = [], $single = false)
    {
        $this->binds[$name] = [$config, $single];
        $this->params[$name] = $params;
        unset($this->instance[$name]);
        return $this;
    }
    
    /**
     * 绑定一个或多个依赖关系
     * ```
     * [
     *      '类名|接口名|别名' => [0 => '类配置' , 1 => '构造参数', 2 => '是否单例'],
     *      ...
     *      '类名|接口名|别名' => [0 => '类配置' , 1 => '构造参数'],
     *      ...
     *      '类名|接口名|别名' => [0 => '类配置' , 1 => '(bool)是否单例'],
     *      ...
     *      '类名|接口名|别名' => [类配置], // 将应用[$single]设置
     *      ...
     * ]
     * ```
     *
     * @param string|array $names 依赖名称, 或多个依赖的定义数组
     * @param string
     */
    public function binds(array $names, $single = false)
    {
        foreach ($names as $name => $array) {
            if (is_array($array) && isset($array['class'])) {
                $this->bind($name, $array, $single);
            } else {
                if (count($array) === 2) {
                    // 跳过构造参数设置
                    if(is_bool($array[1])) {
                        $array[2] = $array[1];
                        $array[1] = [];
                    }
                }
                
                array_unshift($array, $name);
                call_user_func_array([$this, 'bind'], $array);
            }
        }
    }
    
    /**
     * 注册单例类
     * @param string $name 类名, 接口名, 别名
     * @param array $config
     * @return void
     */
    public function single($name, $config)
    {
        $this->bind($name, $config, true);
    }
    
    /**
     * 注册单例实例
     * @param string $name 类名, 接口名, 别名
     * @param object $object
     * @return void
     */
    public function instance($name, $object)
    {
        $this->instance[$name] = $object;
    }
    
    /**
     * 创建类的实例
     * @param string $name 类名, 接口名, 别名
     * @param array $params 构造参数, 会覆盖默认值
     * @param array $option 对象属性配置数组
     * @param bool $reset 本次是否重新创建一个新实例, 默认`false`,
     * 注意要更换依赖关系, 应该重新绑定[[bind()]], 重建新实例不影响之前缓存实例,
     * 因为新实例将被直接返回, 不覆盖之前单例缓存实例.
     * @return object
     */
    public function make($name, array $params = [], array $option = [], $reset = false)
    {
        // 不需要重新创建, 存在对象实例直接返回
        if (!$reset && isset($this->instance[$name])) {
            return $this->instance[$name];
        }
        
        // 未注册依赖关系, 直接创建类
        if (!isset($this->binds[$name])) {
            return $this->build($name, $params, $option);
        }
        
        // 获得类的定义
        list($config, $single) = $this->binds[$name];
        
        if (is_callable($config, true)) {
            $params = $this->getParams($name, $params);
            $object = call_user_func_array($config, [$this, $params, $option]);
        } elseif (is_array($config)) {
            $class = $config['class'];
            
            unset($config['class']);
            $option = array_merge($config, $option);
            $params = $this->getParams($name, $params);
            $object = $this->build($class, $params, $option);

        } else {
            throw (new QlassException())->badConfigType($config);
        }
        
        // 缓存单例对象
        if(!$reset && $single) {
            $this->instance($name, $object);
        }
        
        return $object;
    }
    
    /**
     * 获取构造参数
     * @param string $name 类名, 接口名, 别名
     * @param array $params 覆盖参数
     * @return array
     */
    protected function getParams($name, array $params = []) {
        if (empty($this->params[$name])) {
            return $params;
        } elseif (empty($params)) {
            return $this->params[$name];
        } else {
            $result = $this->params[$name];
            foreach ($params as $key => $val) {
                $result[$key] = $val;
            }
            return $result;
        }
    }
    
    /**
     * 创建对象实例 - 依赖注入
     * @param string $class 类名
     * @param array $params 构成参数
     * @param array $option 属性配置
     * @return object
     */
    public function build($class, array $params = [], array $option = [])
    {
        try {
            $reflector = new \ReflectionClass($class);
        } catch (\ReflectionException $e) {
            throw new NotFoundException($e->getMessage(), 'class');
        }

        // abstract or interface
        if (!$reflector->isInstantiable()) {
            throw new CallException('Not instantiable class : ' . $class);
        }
        
        // 获得构成函数反射
        $constructor = $reflector->getConstructor();
        // 无构造参数
        if ($constructor === null) {
            return new $class();
        }
        
        // 注入依赖
        $params = $this->parseParameters($constructor->getParameters(), $params);
        
        // 无属性配置, 直接创建
        if (empty($option)) {
            return $reflector->newInstanceArgs($params);
        }
        
        // 可配置对象, 最后一个构造参数始终可导入配置
        if ($reflector->implementsInterface('qpf\base\Qlass')) {
            $params[count($params) - 1] = $option;
            return $reflector->newInstanceArgs($params);
        }
        
        // 设置对象属性
        $object = $reflector->newInstanceArgs($params);
        foreach ($option as $name => $value) {
            $object->$name = $value;
        }
        return $object;
    }
    
    /**
     * 解决构造参数依赖
     * @param \ReflectionParameter[]  $parameters 构成参数
     * @param array $params 注入参数
     * @return array
     */
    protected function parseParameters($parameters, array $params = [])
    {
        $construct = [];
        
        if (!empty($parameters)) {
            reset($parameters); // 重设数组指针
            $type = key($parameters) === 0 ? 1 : 0; // 1序列数组, 0关联数组
            foreach ($parameters as $parameter) {
                // 检查参数是否为`...`可变类型
                if (version_compare(PHP_VERSION, '5.6.0', '>=') && $parameter->isVariadic()) {
                    $construct = $params;
                    break;
                }
                $name = $parameter->getName();
                // TODO 支持`is_debug` 设置 'isDebug'属性
                $na_me = QPF::parseName($name, 'f');
                /* @var $hintedClass \ReflectionClass|null  */
                $hintedClass = $parameter->getClass();
                
                if($hintedClass !== null) {// 参数对象约束类型
                    $construct[] = $this->make($hintedClass->name);
                } elseif ($type == 1 && !empty($params)) { // 序列数组传值
                    $construct[] = array_shift($params);
                } elseif ($type == 0 && isset($params[$name])) { // 关联数组传值
                    $construct[] = $params[$name];
                } elseif ($type == 0 && isset($params[$na_me])) { // 支持`_`分割的小写属性名
                    $construct[] = $params[$na_me];
                } elseif ($parameter->isDefaultValueAvailable()) { // 检查是否有默认值
                    $construct[] = $parameter->getDefaultValue();
                } else {
                    throw new ParameterException('method param miss:' . $name);
                }
            }
        }
        return $construct;
    }
    
    /**
     * 执行函数或闭包回调函数 - 依赖注入
     * @param string|\Closure $func 函数名或匿名函数
     * @param array $params 构造参数
     * @return mixed
     */
    public function callFunc($func, array $params = [])
    {
        try {
            $reflect = new \ReflectionFunction($func);
            $args = $this->parseParameters($reflect->getParameters(), $params);
            return $reflect->invokeArgs($args);
        } catch (\ReflectionException $e) {
            throw (new CallException())->badFunctionCall($func);
        }
    }
    
    /**
     * 调用类的方法 - 依赖注入
     * ```
     * call_user_func_array(['Base', 'init'], ['param']); // 调用Base->init(); 将报错静态方法不存在
     * $b = B();
     * call_user_func_array($b, 'init'); // ok
     *
     * callMethod(['Base', 'init'], ['param']); // 将自动创建`Base`对象实例, 并传入
     * ```
     * @param array|string $method 包含[类或对象,方法名]的数组, 或静态调用字符串
     * @param array $vars
     */
    public function callMethod($method, array $params = [])
    {
        try {
            // 类或对象的方法
            if(is_array($method)) {
                $objcet = is_object($method[0]) ? $method[0] : $this->make($method[0]);
                $reflection = new \ReflectionMethod($objcet, $method[1]);
                // 静态方法
            } else {
                $objcet = null;
                $reflection = new \ReflectionMethod($method);
            }
            /* @var $reflection \ReflectionMethod  */
            return $reflection->invokeArgs($objcet, $this->parseParameters($reflection, $params));
        } catch (\ReflectionException $e) {
            throw (new CallException())->badMethodCall($method);
        }
    }
    
    /**
     * 调用回调函数或方法
     * @param callable $callback 函数或数组
     * @param array $params 构造参数
     * @return mixed
     */
    public function call($callback, array $params = [])
    {
        if($callback instanceof \Closure) {
            return $this->callFunc($callback, $params);
        }
        return $this->callMethod($callback, $params);
    }
    
    /**
     * 返回容器绑定的依赖
     * @return array
     */
    public function getBinds()
    {
        return $this->binds;
    }
    
    /**
     * 返回绑定的依赖
     * @param string $name
     * @return array
     */
    public function getBind($name)
    {
        return isset($this->binds[$name]) ? $this->binds[$name] : [];
    }
    
    /**
     * 是否绑定了指定的依赖关系
     * @param string $name
     * @return boolean
     */
    public function isBind($name)
    {
        return isset($this->binds[$name]) || isset($this->instance[$name]);
    }
    
    /**
     * 重置容器对象
     * @return void
     */
    public function reset()
    {
        $this->binds = [];
        $this->params = [];
        $this->instance = [];
    }
}
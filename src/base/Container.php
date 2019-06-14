<?php
namespace qpf\base;

use qpf;
use qpf\exceptions\NotFoundException;
use qpf\exceptions\CallException;
use qpf\exceptions\QlassException;
use qpf\exceptions\ParameterException;

/**
 * 依赖解决容器
 * 
 * 
 * 对象定义
 * ```
 * [
 *      '类名|接口名|别名' => [
 *          '$class'  => '', // 实现类
 *          '$params'  => [], // 构造参数
 *          '$single' => false, // 是否单例, true将缓存再容器内
 *          '$options' => [ // 属性配置
 *              'property1' => 'value1',
 *          ],
 *      ],
 * ]
 * ```
 * 
 * 注意:
 * 仅在使用[[QPF::create()]]方法创建实例时, 才可使用的简写配置
 * ```
 * [
 *      '$class'  => '', // 实现类
 *      'property1' => 'value1',
 *      'property2' => 'value2',
 * ]
 * ```
 * 完整的对象定义配置, 一般仅在注册服务时才需要, 通过方法注册, 按照参数传入即可!
 * 
 * @version 1.1
 */
class Container implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * 绑定依赖定义
     * @var array
     */
    private $binds = [];
    /**
     * 单例实例
     * @var array
     */
    private $instance = [];
    
    /**
     * 获取容器中的对象实例 - 不存在则创建
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
     * 设置类依赖关系到容器
     * @param string $name 类标识
     * @param mixed $config 类定义
     * @param array $params 可选, 构造参数
     * @param array $options 可选, 对象属性
     * @param bool $single 可选, 是否单例
     * @return $this
     */
    public function set($name, $config, array $params = null, array $options = null, $single = null)
    {
        $config = $this->checkConfig($config);
        $this->setConfigParams($config, $params);
        $this->setConfigOptions($config, $options);
        $this->setConfigSingle($config, $single);
        
        $this->binds[$name] = $config;
        unset($this->instance[$name]);
        return $this;
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
     * @param mixed $config 依赖关系, 即`类名, 对象或定义数组, 闭包`
     * @param bool $single 是否单例, 默认值为`null` 即不设置,
     * 若[[$config]]参数数组存在`$single`元素, 该参数始终无效,
     * 否则, 将会添加该元素设置.
     * @return $this
     */
    public function bind($name, $config, $single = null)
    {
        $config = $this->checkConfig($config);
        $this->setConfigSingle($config, $single);

        $this->binds[$name] = $config;
        unset($this->instance[$name]);
        return $this;
    }
    
    /**
     * 绑定一个或多个依赖关系
     * ```
     * [
     *      '类名|接口名|别名' => [
     *          '$class'  => '', // 实现类
     *          '$params'  => [], // 构造参数
     *          '$single' => false, // 是否单例
     *          '$options' => [ // 属性配置
     *              'property1' => 'value1',
     *          ],
     *      ],
     *      
     *      'foo' => 'qpf\test\Foo',
     * ]
     * ```
     *
     * @param array $configs 绑定多个依赖关系
     * @param bool $single 是否单例, 该组的全局属性, 若该参数不为null,
     * 将会为依赖关系添加该属性, 已存在该属性不会进行覆盖.
     * @param string
     */
    public function binds(array $configs, $single = null)
    {
        foreach ($configs as $name => $config) {
            $this->bind($name, $config, $single);
        }
    }
    
    /**
     * 注册单例类
     * @param string $name 类名, 接口名, 别名
     * @param array $params 可选, 定义或覆盖构造参数
     * @return void
     */
    public function single($name, $config)
    {
        $this->bind($name, $config, true);
    }
    
    /**
     * 检查类定义配置
     * @param mixed $config
     * @return mixed
     */
    protected function checkConfig($config)
    {
        if (is_array($config)) {
            if (isset($config['$class'])) {
                return $config;
            }
        } else {
            return ['$class' => $config];
        }

        throw (new ParameterException())->invalidType('$config', 'Qlass');
    }
    
    /**
     * 获取指定类的定义
     * @param string $name 类标识
     * @param array $params 构造参数
     * @param array $options 属性配置
     * @return array
     */
    protected function getConfig($name, $params = null, $options = null)
    {
        $config = $this->binds[$name];
        $this->setConfigOptions($config, $options);
        $this->setConfigParams($config, $params);
        
        if (!key_exists('$params', $config)) {
            $config['$params'] = [];
        }
        
        if (!key_exists('$options', $config)) {
            $config['$options'] = [];
        }
        
        if (!key_exists('$single', $config)) {
            $config['$single'] = false;
        }
        
        return $config;
    }
    
    /**
     * 设置类定义是否单例
     * 
     * 当容器首次实例化单例类时将缓存在容器内, 再次实例化将
     * 直接返回之前缓存的对象实例.
     * 
     * @param array $config
     * @param bool $single
     * @return void
     */
    protected function setConfigSingle(&$config, $single)
    {
        if ($single === null || !is_array($config)) {
            return;
        }
        
        // 当已设置将无法修改
        if (!isset($config['$single'])) {
            $config['$single'] = $single;
        }
    }
    
    /**
     * 设置类定义的构造参数
     * 
     * 当容器实例化对象时, 会将提供的构造参数按顺序
     * 或按变量名的行式传入类的构造器
     * 
     * @param array $config
     * @param array $params
     * @return void
     */
    protected function setConfigParams(&$config, $params)
    {
        if ($params === null) {
            return;
        }
        
        if (isset($config['$params'])) {
            foreach ($params as $name => $value) {
                $config['$params'][$name] = $value;
            }
        }
        
        $config['$params'] = $params;
    }
    
    /**
     * 配置类定义的初始化属性
     * @param array $config
     * @param array $options
     * @return void
     */
    protected function setConfigOptions(&$config, $options)
    {
        if ($options === null) {
            return;
        }
        
        if (isset($config['$options'])) {
            foreach ($options as $name => $value) {
                $config['$options'][$name] = $value;
            }
        }
        
        $config['$options'] = $options;
    }
    
    /**
     * 绑定类实例到容器
     * @param string $name 类名, 接口名, 别名
     * @param object $object 对象实例
     * @return void
     */
    public function instance($name, $object)
    {
        if (!is_object($object)) {
            throw (new ParameterException())->invalidType(2, 'object');
        }
        
        $this->instance[$name] = $object;
    }
    
    /**
     * 创建类的实例 - 容器中存在将直接返回
     * @param string $name 类名, 接口名, 别名
     * @param array $params 构造参数, 会覆盖默认值
     * @param array $option 对象属性配置数组
     * @param bool $reset 本次是否重新创建一个新实例, 默认`false`,
     * 注意要更换依赖关系, 应该重新绑定[[bind()]], 重建新实例不影响之前缓存实例,
     * 因为新实例将被直接返回, 不覆盖之前单例缓存实例.
     * @return object
     */
    public function make($name, array $params = [], array $options = [], $reset = false)
    {
        // 不需要重新创建, 存在对象实例直接返回
        if (!$reset && isset($this->instance[$name])) {
            return $this->instance[$name];
        }
        
        // 未注册依赖关系, 直接创建类
        if (!isset($this->binds[$name])) {
            return $this->build($name, $params, $options);
        }
        
        // 获得类的定义
        $config = $this->getConfig($name);

        if ($config['$class'] instanceof \Closure || is_array($config['$class'])) {
            $object = call_user_func_array($config['$class'], [$this, $config['$params'], $config['$options']]);
        } elseif (is_array($config) && isset($config['$class'])) {
            $object = $this->build($config['$class'], $config['$params'], $config['$options']);
        } else {
            throw (new QlassException())->badConfigType($config);
        }
        
        // 缓存单例对象
        if(!$reset && $config['$single']) {
            $this->instance($name, $object);
        }
        
        return $object;
    }

    /**
     * 创建对象实例 - 依赖注入
     * @param string $class 类名
     * @param array $params 构成参数
     * @param array $option 属性配置
     * @return object
     */
    public function build($class, array $params = [], array $options = [])
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
        if (empty($options)) {
            return $reflector->newInstanceArgs($params);
        }
        
        // 可配置对象, 最后一个构造参数始终可导入配置
        if ($reflector->implementsInterface('qpf\base\Qlass')) {
            $params[count($params) - 1] = $options;
            return $reflector->newInstanceArgs($params);
        }
        
        // 设置对象属性
        $object = $reflector->newInstanceArgs($params);
        foreach ($options as $name => $value) {
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
                $na_me = QPF::nameFormatToClass($name, true);
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
            return $reflection->invokeArgs($objcet, $this->parseParameters($reflection->getParameters(), $params));
        } catch (\ReflectionException $e) {
            throw (new CallException())->badMethodCall($method);
        }
    }
    
    /**
     * 调用类的实例
     * @param string $class 类名
     * @param array $params 构造参数
     * @param array $option 对象属性设置
     * @return object
     */
    public function callClass($class, array $params = [], array $option = [])
    {
        return $this->build($class, $params, $option);
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
     * @param string $name 类名, 接口名, 别名
     * @return boolean
     */
    public function isBind($name)
    {
        return isset($this->binds[$name]) || isset($this->instance[$name]);
    }
    
    /**
     * 容器中是否存在指定对象实例
     * @param string $name 类名, 接口名, 别名
     * @return bool
     */
    public function exists($name)
    {
        return isset($this->instance[$name]);
    }
    
    /**
     * 删除容器内的对象实例
     * @param string $name 类名, 接口名, 别名
     */
    public function delete($name)
    {
        if (isset($this->instance[$name])) {
            unset($this->instance[$name]);
        }
    }
    
    /**
     * 获取容器内所有对象实例
     * @return array
     */
    public function all()
    {
        return $this->instance;
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
    
    public function __set($name, $value)
    {
        $this->bind($name, $value);
    }
    
    public function __get($name)
    {
        return $this->get($name);
    }
    
    public function __isset($name)
    {
        return $this->exists($name);
    }
    
    public function __unset($name)
    {
        $this->delete($name);
    }
    
    /**
     * {@inheritDoc}
     * @see Countable::count()
     */
    public function count()
    {
        return count($this->instance);
    }

    /**
     * {@inheritDoc}
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset)
    {
        return $this->exists($offset);
    }

    /**
     * {@inheritDoc}
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset)
    {
        return $this->make($offset);
    }

    /**
     * {@inheritDoc}
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value)
    {
        $this->bind($offset, $value);
    }

    /**
     * {@inheritDoc}
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }

    /**
     * {@inheritDoc}
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->instance);
    }
}
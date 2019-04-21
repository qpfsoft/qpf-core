<?php
namespace qpf\exceptions;

/**
 * 调用异常
 * 
 * 错误调用方法或函数的异常
 */
class CallException extends \LogicException
{
    /**
     * 调用类型
     * @var string
     */
    protected $invalid = '';
    
    /**
     * 调用未定义函数或缺失参数
     * @param string $func 函数名
     * @param array|string $args 可选, 缺失的参数, 多个用数组传入
     */
    public function badFunctionCall($func, $args = [])
    {
        return $this->badCall('Function', $func, $args);
    }
    
    /**
     * 调用未定义回调或缺少参数
     * - Closure : 闭包, 匿名函数
     * - Callable : 回调
     * @param callable $callable
     * @param array $args
     * @return $this
     */
    public function badCallbackCall($callable, $args = [])
    {
        return $this->badCall('Callable', $callable, $args);
    }
    
    /**
     * 调用未定义的类或缺失构造参数
     * ```
     * // 未定义
     * throw (new CallException())->badClassCall('\qpf\class\Name');
     * // 缺少构造参数
     * throw (new CallException())->badClassCall(__CLASS__, ['name', 'sex']);
     * throw (new CallException())->badClassCall($this, ['name', 'sex']);
     * ```
     * @param string|object $class 类名
     * @param array|string $params 可选, 缺失的参数, 多个用数组传入
     * @return $this
     */
    public function badClassCall($class, $params = [])
    {
        $class = is_object($class) ? get_class($class) : (string) $class;
        
        return $this->badCall('Class', $class, $params);
    }
    
    /**
     * 调用未定义的方法或缺失参数
     * ```
     * // 未定义
     * throw (new CallException())->badMethodCall(__METHOD__);
     * throw (new CallException())->badMethodCall([__CLASS__, __FUNCTION__]);
     * throw (new CallException())->badMethodCall([$this, 'getName']);
     * // 缺少参数
     * throw (new CallException())->badMethodCall(__METHOD__, 'name');
     * throw (new CallException())->badMethodCall(__METHOD__, ['name', 'sex']);
     * ```
     * @param string|array 方法名, 数组格式为`[对象名, 方法名]`
     * @param array|string $params 可选, 缺失的参数, 多个用数组传入
     * @return $this
     */
    public function badMethodCall($method, $params = [])
    {
        $method = $this->parseParamName($method);
        
        return $this->badCall('Method', $method . '()', $params);
    }
    
    /**
     * 获取未定义的属性
     * @param string|array $property 属性名
     * @return $this
     */
    public function badPropertyCall($property, $value = null)
    {
        $property = $this->parseParamName($property);
        
        $this->invalid = 'Property';
        
        if (func_num_args() == 1) {
            $message = 'Get undefined Property `'. $property .'`';
        } else {
            $message = 'Set undefined Property  `'. $property .'` = `' . get_varstr($value) . '`';
        }
        $this->message = $message;
        
        return $this;
    }
    
    /**
     * 解析参数名
     * @param array|string $array
     * @return string
     */
    private function parseParamName($array)
    {
        if(is_array($array) && isset($array[0]) && isset($array[1])) {
            $array[0] = is_object($array[0]) ? get_class($array[0]) : (string) $array[0];
            $array = $array[0] . '::' . $array[1];
        } else {
            $array = (string) $array;
        }
        
        return $array;
    }
    
    /**
     * 调用错误
     * @param string $type 调用类型
     * @param string $name 调用名称
     * @param array $args 可选, 缺失的参数
     * @return $this
     */
    private function badCall($type, $name, $args = [])
    {
        $this->invalid = $type;
        
        if (empty($args)) {
            $message = 'Call undefined ' . $type .' `'. $name .'`';
        } else {
            $message = 'Call ' . $type . ' `'. $name .'`';
            if(is_array($args)) {
                $args = implode(', ', $args);
            }
            // 缺少参数`var1, var2...`
            $message .= ' miss param `' . $args . '`';
        }
        $this->message = $message;
        
        return $this;
    }

    /**
     * 获取异常名称
     * @return string 返回字符串描述的该异常名称
     */
    public function getName()
    {
        return 'Invalid Call ' . $this->invalid;
    }
}
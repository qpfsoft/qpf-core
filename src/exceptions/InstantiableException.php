<?php
namespace qpf\exceptions;

/**
 * 对象实例化异常
 */
class InstantiableException extends Exception
{
    /**
     * 构造函数
     * @param string $class 无法实例化类的名称
     */
    public function __construct($class, $message = null, $code = 0, \Exception $previous = null)
    {
        if ($message === null) {
            $message = 'Unable to instantiate `'. $class .'` class.';
        }
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * 获取异常名称
     *
     * @return string 返回字符串描述的该异常名称
     */
    public function getName()
    {
        return 'Not instantiable';
    }
}
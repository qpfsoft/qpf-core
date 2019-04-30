<?php
namespace qpf\exceptions;

/**
 * 无效返回值异常
 * 
 * 当函数调用另一个函数并期望返回值属于某种类型或值, 若不匹配则抛出该异常. 
 */
class ReturnException extends \UnexpectedValueException
{
    /**
     * 获取异常名称
     * @return string
     */
    public function getName()
    {
        return 'Unknown Return Value';
    }
}
<?php
namespace qpf\exceptions;

/**
 * 未知属性
 * 
 * 调用未定义对象属性的异常
 */
class UnknownProperty extends Exception
{
    /**
     * 获取异常名称
     * @return string
     */
    public function getName()
    {
        return 'Unknown Property';
    }
}
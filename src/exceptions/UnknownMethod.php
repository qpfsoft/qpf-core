<?php
namespace qpf\exceptions;

/**
 * 未知方法
 * 
 * 调用对象中不存在方法的异常
 */
class UnknownMethod extends Exception
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
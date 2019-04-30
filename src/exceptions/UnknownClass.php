<?php
namespace qpf\exceptions;

/**
 * 未知类
 * 
 * 调用不存在类的异常
 */
class UnknownClass extends Exception
{
    /**
     * 获取异常名称
     * @return string
     */
    public function getName()
    {
        return 'Unknown Class';
    }
}
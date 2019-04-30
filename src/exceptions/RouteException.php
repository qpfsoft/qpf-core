<?php
namespace qpf\exceptions;

/**
 * 无效路由异常
 */
class RouteException extends Exception
{
    /**
     * 获取异常名称
     * @return string
     */
    public function getName()
    {
        return 'Invalid Route';
    }
}
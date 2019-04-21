<?php
namespace qpf\exceptions;

/**
 * 路由异常
 */
class RouteException extends Exception
{
    /**
     * 获取异常名称
     * @return string
     */
    public function getName()
    {
        return 'Route Not Found';
    }
}
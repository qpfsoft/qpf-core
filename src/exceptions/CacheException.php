<?php
namespace qpf\exception;

/**
 * 缓存异常
 */
class CacheException extends \InvalidArgumentException
{
    public function getName()
    {
        return 'Cache Exception';
    }
}
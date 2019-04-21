<?php
namespace qpf\exceptions;

/**
 * 配置异常
 */
class ConfigException extends Exception
{
    /**
     * 获取异常名称
     */
    public function getName()
    {
        return 'Invalid Config';
    }
}
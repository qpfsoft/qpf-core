<?php
namespace qpf\exceptions;

/**
 * 验证异常
 */
class ValidateException extends \RuntimeException
{
    /**
     * 获取异常名称
     *
     * @return string 返回字符串描述的该异常名称
     */
    public function getName()
    {
        return 'Validate Exception';
    }
}
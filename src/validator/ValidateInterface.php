<?php
namespace qpf\validator;

/**
 * 验证接口
 */
interface ValidateInterface
{
    /**
     * 验证规则定义
     * ```
     * [
     *      '字段名' => '规则',
     * ]
     * ```
     * @var array
     */
    protected $rule = [];
}
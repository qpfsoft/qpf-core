<?php
namespace qpf\validator;

/**
 * 验证规则接口
 */
interface ValidateRuleInterface
{
    /**
     * 设置字段验证规则
     */
    public function rule($rule);
    
    /**
     * 追加字段验证规则
     */
    public function append($item);
    
    /**
     * 移除字段验证规则
     */
    public function remove($item);
}
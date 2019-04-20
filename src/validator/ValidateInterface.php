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
    
    /**
     * 数据验证
     * @param array $date 数据
     * @param array $rules 验证规则
     */
    public function check(array $date, array $rules = [])
    {
        
    }
}
<?php
namespace qpf\htmc\input;

/**
 * Input元素`hidden`隐藏类型
 * 
 * 定义隐藏的输入字段, 用于实现隐式提交参数.
 * @author qiun
 *
 */
class Hidden extends InputBase
{
    /**
     * 规定input元素的类型
     * @var string
     */
    protected $inputType = 'hidden';

    /**
     * 输入隐藏参数的值
     * @param string $value
     * @return $this
     */
    public function value($value)
    {
        $this->attr(['value' => $value]);
        return $this;
    }
}
<?php
namespace qpf\htmc\input;

/**
 * Input元素`color`按钮类型, 颜色选择器
 * 
 * @author qiun
 *        
 */
class Color extends InputBase
{
    /**
     * 规定input元素的类型
     * @var string
     */
    protected $inputType = 'color';
    
    /**
     * 是否启用自动完成功能 - h5
     *
     * - 根据用户历史输入, 会进行提示.
     * @param string $value 可能的值:
     * - `on` : 默认, 启用自动完成功能.
     * - `off`: 禁用自动完成功能.
     * @return $this
     */
    public function autocomplete($value)
    {
        $this->attr(['autocomplete' => $value]);
        return $this;
    }
    
    /**
     * 关闭原生输入验证 - h5
     *
     * - 覆盖form元素的`novalidate`属性
     * @return $this
     */
    public function formNovalidate()
    {
        $this->attr(['formnovalidate' => 'formnovalidate']);
        return $this;
    }
}
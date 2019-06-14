<?php
namespace qpf\htmc\input;

/**
 * Input元素`range`类型, 滑动输入块
 * 
 * - html5 新输入类型
 * # 滑动块
 * < input type="range" name="points" min="1" max="10">
 * 
 * @author qiun
 *        
 */
class Range extends InputBase
{
    /**
     * 规定input元素的类型
     * @var string
     */
    protected $inputType = 'range';
    
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
    
    /**
     * 输入允许的最大值 - h5
     * 
     * - max属性与min属性配合, 可创建合法值范围
     * @param string|integer $value 数字值,日期
     * @return $this
     */
    public function max($value)
    {
        $this->attr(['max' => $value]);
        return $this;
    }
    
    /**
     * 输入运行的最小值 - h5
     * - min属性与max属性配合, 可创建合法值范围
     * @param string|integer $value 数字值,日期
     * @return $this
     */
    public function min($value)
    {
        $this->attr(['max' => $value]);
        return $this;
    }
    
    /**
     * 规定输入的合法数值间隔 - h5
     *
     * 例`step="3"`, 合法范围[..-3、0、3、6..];
     *
     * - step 属性可以与 max 以及 min 属性配合使用，以创建合法值的范围。
     * @param string $value 间隔值
     * @return $this
     */
    public function step($value)
    {
        $this->attr(['step' => $value]);
        return $this;
    }
}
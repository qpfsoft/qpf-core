<?php
namespace qpf\htmc\input;

/**
 * Input元素`number`类型, 数值输入框
 *
 * - html5新输入类型
 * 
 * # 通过上下箭头滚动固定的1~5值
 * < input type="number" name="quantity" min="1" max="5">
 * 
 * @author qiun
 *        
 */
class Number extends InputBase
{
    /**
     * 规定input元素的类型
     * @var string
     */
    protected $inputType = 'number';
    
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
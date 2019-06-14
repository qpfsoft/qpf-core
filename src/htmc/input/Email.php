<?php
namespace qpf\htmc\input;

/**
 * Input元素`email`类型, 邮箱地址输入框
 * 
 * - html5 新输入类型
 * type="email" 在手机上会出现带@和.com符号的全键盘
 * 同时需要设置pattern="正则"来验证输入。
 * 
 * @author qiun
 *        
 */
class Email extends InputBase
{
    /**
     * 规定input元素的类型
     * @var string
     */
    protected $inputType = 'email';
    
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
     * 可接收多个值 - h5
     * @return $this
     */
    public function multiple()
    {
        $this->attr(['multiple' => 'multiple']);
        return $this;
    }
    
    /**
     * 规定验证输入的正则 - h5
     *
     * - 请使用标准的`title`属性来描述规则
     * @param string $value 例`[A-Za-z]{3}`
     * @return $this
     */
    public function pattern($value)
    {
        $this->attr(['pattern' => $value]);
        return $this;
    }
    
    /**
     * 未输入时文本框内显示的灰色提示 - h5
     *
     * @param string $value 提示文字
     * @return $this
     */
    public function placeholder($value)
    {
        $this->attr(['placeholder' => $value]);
        return $this;
    }
}
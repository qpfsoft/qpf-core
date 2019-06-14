<?php
namespace qpf\htmc\input;

/**
 * Input元素`password`密码类型
 * 
 * 定义密码字段。该字段中的字符被掩码
 * @author qiun
 */
class Password extends InputBase
{
    /**
     * 规定input元素的类型
     * @var string
     */
    protected $inputType = 'password';
    
    /**
     * 输入内容的初始值
     * @param string $value
     * @return $this
     */
    public function value($value)
    {
        $this->attr(['value' => $value]);
        return $this;
    }
    
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
     * 输入最大的长度 - h5
     * @param string|integer $value 字符数
     * @return $this
     */
    public function maxlength($value)
    {
        $this->attr(['maxlength' => $value]);
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
    
    /**
     * 规定文本框内容只读
     *
     * - 不可修改的状态
     * @return $this
     */
    public function readonly()
    {
        $this->attr(['readonly' => 'readonly']);
        return $this;
    }
}
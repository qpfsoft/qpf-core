<?php
namespace qpf\htmc\input;

/**
 * Input元素`text`文本类型
 *
 * 定义单行的输入字段，用户可在其中输入文本。默认宽度为 20 个字符。
 * 
 * # 字符
 * 1个汉字 = 1个字 = 1个字符
 * 1个字符 = 1个字节 = 8bit（ACSII码下）
 * 1个字符 = 2个字节 = 16bit（Unicode码下）
 * 
 * @author qiun
 */
class Text extends InputBase
{
    /**
     * 规定input元素的类型
     * @var string
     */
    protected $inputType = 'text';

    /**
     * 输入字段的最大长度, 以字符个数计 - h5
     * 
     * - 中文 : 1个汉字战1个字符
     * - abc123 : 每个数字和字母占1个字符
     * @param string|integer $value 字数
     * @return $this
     */
    public function maxLength($value)
    {
        $this->attr(['maxlength' => $value]);
        return $this;
    }
    
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
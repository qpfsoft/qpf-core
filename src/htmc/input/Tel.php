<?php
namespace qpf\htmc\input;

/**
 * 电话号码输入框
 *
 * type="tel" 在手机上会打开电话键盘，电话键盘还包括*和#。
 * 同时需要设置pattern="正则"来验证输入。
 *
 * # 区号-7~8位数字的电话号码
 * pattern="^(0|86|17951)?(13[0-9]|15[012356789]|17[0678]|18[0-9]|14[57])[0-9]{8}"
 *
 * # 手机号码的格式应该是 1开头，一共11位数字
 * ^1([23578])\d{9}$
 *
 * @author qiun
 */
class Tel extends inputBase
{

    public $type = 'tel';
    
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
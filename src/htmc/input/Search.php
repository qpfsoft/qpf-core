<?php
namespace qpf\htmc\input;

/**
 * Input元素`search`类型, 搜索框
 *
 * - html5新输入类型
 * 
 * 与文本输入框没什么区别，虽然外观无影响，但是对交互是有影响的。
 * <!-- google: -->
 * <input maxlength="2048" name="q" autocomplete="off" title="Search" type="text" value="" aria-label="Search" aria-haspopup="false" role="combobox" aria-autocomplete="both" dir="ltr" spellcheck="false">
 *
 * <!-- bing: -->
 * <input name="q" title="Enter your search term" type="search" value="" maxlength="100" autocapitalize="off" autocorrect="off" autocomplete="off" spellcheck="false" role="combobox" aria-autocomplete="both" aria-expanded="false" aria-owns="sa_ul">
 *
 * <!-- baidu: -->
 * <input type="text" name="wd" maxlength="100" autocomplete="off">
 *
 * <!-- sou: -->
 * <input type="text" name="q" suggestwidth="528px" autocomplete="off">
 *
 * <!-- sogou: -->
 * <input type="text" name="query" size="47" maxlength="100" autocomplete="off">
 */
class Search extends inputBase
{
    /**
     * 规定input元素的类型
     * @var string
     */
    protected $inputType = 'search';
    
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
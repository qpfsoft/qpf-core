<?php
namespace qpf\htmc\input;

use qpf\htmc\html\TagAttr;

/**
 * input元素基类
 * 
 * - 通过设置[$inputType]属性来指定当前input类型, 统一使用
 * [getHtml()]方法来获得html代码.
 * 
 * step、max 以及 min 属性适用于以下 < input > 类型：
 * number, range, date, datetime, datetime-local,
 *  month, time 以及 week。
 * 
 * @author qiun
 *
 */
class InputBase extends TagAttr
{
    /**
     * 规定input元素的类型
     * @var string
     */
    protected $inputType;
    
    /**
     * 标签ID
     * @param string $value
     * @return $this
     */
    public function id($value)
    {
        $this->attr(['id' => $value]);
        return $this;
    }
    
    /**
     * 规定input元素的名称, 提交后的变量名
     * 
     * - 只有设置了name属性的表单元素才会被提交
     * @param string $value
     * @return $this
     */
    public function name($value)
    {
        $this->attr(['name' => $value]);
        return $this;
    }
    
    /**
     * 元素class属性
     * @param string $value 应用的样式名称
     * @return $this
     */
    public function classAttr($value)
    {
        $this->attr(['class' => $value]);
        return $this;
    }
    
    /**
     * 页面加载时自动获得焦点 - h5
     * 
     * - 不适用于 type="hidden"
     * @return $this
     */
    public function autofocus()
    {
        $this->attr(['autofocus' => 'autofocus']);
        return $this;
    }
    
    /**
     * 禁用input元素
     * 
     * - 不适用于 type="hidden"
     * @return $this
     */
    public function disabled()
    {
        $this->attr(['disabled' => 'disabled']);
        return $this;
    }
    
    /**
     * 规定input元素所属表单 - h5
     * @param string $value 表单ID, 多个用逗好分割
     * @return $this
     */
    public function form($value)
    {
        $this->attr(['form' => $value]);
        return $this;
    }
    
    /**
     * list属性引用数据列表 - h5
     * 
     * 示例
     * ~~~
     * < input type="url" list="url_list" name="link" />
     * < datalist id="url_list" >
     * < option label="W3Schools" value="http://www.w3s.com" />
     * ...
     * </datalist>
     * ~~~
     * 
     * @param string $value datalist标签的ID值
     * @return $this
     */
    public function listAttr($value)
    {
        $this->attr(['list' => $value]);
        return $this;
    }
    
    /**
     * 提交前必须输入字段 - h5
     * 
     * - 适用于text, search, url, telephone, 
     * email, password, date pickers, number, 
     * checkbox, radio 以及 file。
     * @return $this
     */
    public function required()
    {
        $this->attr(['required' => 'required']);
        return $this;
    }
    
    /**
     * 规定输入字段的宽度
     *
     * - `text` 和 `password` : 属性值为可见的字符数
     * - 其他 : 以像素为单位的输入字段宽度
     *
     * 提示:size会改变元素的宽度大小, 建议使用css`width`来设计
     * @param string $value
     * @return \qpf\htmc\input\Text
     */
    public function size($value)
    {
        $this->attr(['size' => $value]);
        return $this;
    }
    
    /**
     * 设置tab键切换序号
     *
     * @param integer $value 可能的值:
     * - `-1` : 该元素不能用tag键获得焦点.
     * - `0` : 该元素tag键可获得焦点, 文档自动顺序
     * - 大于1 : 手动设置焦点顺序, 范围1到32767之间. 
     */
    public function tabIndex($value)
    {
        $this->attr(['tabindex' => $value]);
        return $this;
    }
    
    /**
     * 鼠标经过input的提示
     * @param string $value
     * @return $this
     */
    public function title($value)
    {
        $this->attr(['title' => $value]);
        return $this;
    }
    
    /**
     * 获得Input元素的Html代码
     * @return string
     */
    public function getHtml()
    {
        $this->attr(['type'=>$this->inputType]);
        $code = '<input' . $this->parseAttr() . '/>';
        // 清空标签属性设置
        $this->resetAttr();
        return $code;
    }
}
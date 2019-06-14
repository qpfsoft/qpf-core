<?php
namespace qpf\htmc\input;

/**
 * Input元素`image`图片按钮类型
 * 
 * 定义图像形式的提交按钮。
 * @author qiun
 *
 */
class Image extends InputBase
{
    /**
     * 规定input元素的类型
     * @var string
     */
    protected $inputType = 'image';
    
    /**
     * 设置图片地址
     * @param string $value
     * @return $this
     */
    public function src($value)
    {
        $this->attr(['src' => $value]);
        return $this;
    }
    
    /**
     * 指定图片按钮的高度 - h5
     * @param string $value 例`100px`或`50%`
     * @return $this
     */
    public function height($value)
    {
        $this->attr(['height' => $value]);
        return $this;
    }
    
    /**
     * 设置图片替代文本
     * @param string $value
     * @return \qpf\htmc\input\Image
     */
    public function alt($value)
    {
        $this->attr(['alt' => $value]);
        return $this;
    }
    
    /**
     * 图片按钮对齐方式
     * 
     * - html4.01中, 不赞成使用, 请使用css替代`float:right`.
     * @param string $value 可能的值:
     * - `left` 和 `right` : 只有它们2个所有游览器支持.
     * @return \qpf\htmc\input\Image
     */
    public function align($value)
    {
        $this->attr(['align' => $value]);
        return $this;
    }
    
    /**
     * 按钮提交地址 - h5
     *
     * - 覆盖form元素的`action`属性
     * @return $this
     */
    public function formAction($url)
    {
        $this->attr(['formaction' => $url]);
        return $this;
    }
    
    /**
     * 按钮提交的编码类型 - h5
     *
     * - 覆盖form元素的`enctype`属性
     * - 仅适用于 method="post" 的表单
     * @param string $val 可能的值:
     * - `def` : 默认, 即`application/x-www-form-urlencoded`, 发送前编码所有字符串
     * - `up` : 包含文件上传的表单, 即`multipart/form-data`, 不对字符编码
     * - `txt` : 纯文本格式, 空格转换为`+`加号, 但不对特殊字符编码 `text/plain`
     * @return \qpf\htmc\Form
     */
    public function formEnctype($val = 'def')
    {
        $list = [
            'def' => 'application/x-www-form-urlencoded',
            'up'  => 'multipart/form-data',
            'txt' => 'text/plain',
        ];
        $this->attr(['enctype' => $val]);
        return $this;
    }
    
    /**
     * 按钮提交的方式 - h5
     *
     * - 覆盖form元素的`method`属性
     * @param string $value 提交方式`get`/`post`
     * @return $this
     */
    public function formMethod($value)
    {
        $this->attr(['formaction' => $url]);
        return $this;
    }
    
    /**
     * 按钮在何处打开提交页面 - h5
     *
     * - 覆盖form元素的`target`属性
     * @param string $value 可能的值:
     * - `_blank` : 在新窗口/选项卡中打开
     * - `_self` : 在同一框架中打开, 默认
     * - `_parent` : 在父框架中打开
     * - `_top` : 在整个窗口中打开
     * - <framename> : 在指定的框架中打开
     * @return $this
     */
    public function formtarget($value)
    {
        $this->attr(['formtarget' => $value]);
        return $this;
    }
    
    /**
     * 选项关联的参数值
     *
     * @param string $value
     * @return $this
     */
    public function value($value)
    {
        $this->attr(['value' => $value]);
        return $this;
    }
}
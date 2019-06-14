<?php
namespace qpf\htmc\input;

/**
 * Input表单输入控件
 * @author qiun
 *
 */
class Input
{
    /**
     * 当前创建的实例对象
     * @var array
     */
    private $inputMap = [];
    
    /**
     * 文本框
     * @return Text
     */
    public function text()
    {
        if (!isset($this->inputMap['text'])) {
            $this->inputMap['text'] = new Text();
        }
        
        return $this->inputMap['text'];
    }
    
    /**
     * 密码框
     * @return Password
     */
    public function password()
    {
        if (!isset($this->inputMap['password'])) {
            $this->inputMap['password'] = new Password();
        }
        
        return $this->inputMap['password'];
    }
    
    /**
     * 单选
     * @return Radio
     */
    public function radio()
    {
        if (!isset($this->inputMap['radio'])) {
            $this->inputMap['radio'] = new Radio();
        }
        
        return $this->inputMap['radio'];
    }
    
    /**
     * 多选
     * @return CheckBox
     */
    public function checkbox()
    {
        if (!isset($this->inputMap['checkbox'])) {
            $this->inputMap['checkbox'] = new CheckBox();
        }
        
        return $this->inputMap['checkbox'];
    }
    
    /**
     * 上传
     * @return File
     */
    public function file()
    {
        if (!isset($this->inputMap['file'])) {
            $this->inputMap['file'] = new File();
        }
        
        return $this->inputMap['file'];
    }
    
    /**
     * 隐藏
     * @return Hidden
     */
    public function hidden()
    {
        if (!isset($this->inputMap['hidden'])) {
            $this->inputMap['hidden'] = new Hidden();
        }
        
        return $this->inputMap['hidden'];
    }
    
    /**
     * 点击按钮
     * @return Button
     */
    public function button()
    {
        if (!isset($this->inputMap['button'])) {
            $this->inputMap['button'] = new Button();
        }
        
        return $this->inputMap['button'];
    }
    
    /**
     * 重置表单按钮
     * @return Reset
     */
    public function reset()
    {
        if (!isset($this->inputMap['reset'])) {
            $this->inputMap['reset'] = new Reset();
        }
        
        return $this->inputMap['reset'];
    }
    
    /**
     * 提交表单按钮
     * @return Submit
     */
    public function submit()
    {
        if (!isset($this->inputMap['submit'])) {
            $this->inputMap['submit'] = new Submit();
        }
        
        return $this->inputMap['submit'];
    }
    
    /**
     * 图片按钮
     * @return Image
     */
    public function image()
    {
        if (!isset($this->inputMap['image'])) {
            $this->inputMap['image'] = new Image();
        }
        
        return $this->inputMap['image'];
    }
    
    // h5 新类型
    
    /**
     * 邮箱 - h5
     * @return Email
     */
    public function email()
    {
        if (!isset($this->inputMap['email'])) {
            $this->inputMap['email'] = new Email();
        }
        
        return $this->inputMap['email'];
    }
    
    /**
     * 网址 - h5
     * @return Url
     */
    public function url()
    {
        if (!isset($this->inputMap['url'])) {
            $this->inputMap['url'] = new Url();
        }
        
        return $this->inputMap['url'];
    }
    
    /**
     * 数值 - h5
     * @return Number
     */
    public function number()
    {
        if (!isset($this->inputMap['number'])) {
            $this->inputMap['number'] = new Number();
        }
        
        return $this->inputMap['number'];
    }
    
    /**
     * 滑块 - h5
     * @return Range
     */
    public function range()
    {
        if (!isset($this->inputMap['range'])) {
            $this->inputMap['range'] = new Range();
        }
        
        return $this->inputMap['range'];
    }
    
    /**
     * 日期时间 - h5
     * @return DatePickers
     */
    public function datePickers()
    {
        if (!isset($this->inputMap['datePickers'])) {
            $this->inputMap['datePickers'] = new DatePickers();
        }
        
        return $this->inputMap['datePickers'];
    }
    
    /**
     * 搜索 - h5
     * @return Search
     */
    public function search()
    {
        if (!isset($this->inputMap['search'])) {
            $this->inputMap['search'] = new Search();
        }
        
        return $this->inputMap['search'];
    }
    
    /**
     * 拾色器 - h5
     * @return Color
     */
    public function color()
    {
        if (!isset($this->inputMap['color'])) {
            $this->inputMap['color'] = new Color();
        }
        
        return $this->inputMap['color'];
    }
    
    
}
<?php
namespace qpf\htmc\html;

/**
 * 标签事件属性
 * 
 * 该类让html元素支持添加'onXX=""'事件属性.
 * @author qiun
 *
 */
class TagEvent extends TagAttr
{
    /**
     * 设置点击事件
     * @param string $js 脚本
     * @return $this
     */
    public function onClick($js)
    {
        $this->attr(['onClick' => $js]);
        return $this;
    }
    
    /**
     * 设置双击事件
     *
     * @param string $js 脚本
     * @return $this
     */
    public function onDblClick($js)
    {
        $this->attr(['onDblClick' => $js]);
        return $this;
    }
    
    /**
     * 设置鼠标按下事件
     *
     * @param string $js 脚本
     * @return $this
     */
    public function onMouseDown($js)
    {
        $this->attr(['onMouseDown' => $js]);
        return $this;
    }
    
    /**
     * 设置鼠标移动事件
     *
     * @param string $js 脚本
     * @return $this
     */
    public function onMouseMove($js)
    {
        $this->attr(['onMouseMove' => $js]);
        return $this;
    }
    
    /**
     * 设置鼠标从某元素移开事件
     *
     * @param string $js 脚本
     * @return $this
     */
    public function onMouseOut($js)
    {
        $this->attr(['onMouseOut' => $js]);
        return $this;
    }
    
    /**
     * 设置鼠标移到某元素之上事件
     *
     * @param string $js 脚本
     * @return $this
     */
    public function onMouseOver($js)
    {
        $this->attr(['onMouseOver' => $js]);
        return $this;
    }
    
    /**
     * 设置鼠标移到某元素之上事件
     *
     * @param string $js 脚本
     * @return $this
     */
    public function onMouseUp($js)
    {
        $this->attr(['onMouseUp' => $js]);
        return $this;
    }
    
    /**
     * 设置元素失去焦点时触发事件
     *
     * @param string $js 脚本
     * @return $this
     */
    public function onBlur($js)
    {
        $this->attr(['onBlur' => $js]);
        return $this;
    }
    
    /**
     * 设置元素值改变时触发事件
     *
     * @param string $js 脚本
     * @return $this
     */
    public function onChange($js)
    {
        $this->attr(['onChange' => $js]);
        return $this;
    }
    
    /**
     * 设置元素获得焦点时触发事件
     *
     * @param string $js 脚本
     * @return $this
     */
    public function onFocus($js)
    {
        $this->attr(['onFocus' => $js]);
        return $this;
    }
    
    /**
     * 设置键盘按键时事件
     *
     * @param string $js 脚本
     * @return $this
     */
    public function onKeyDown($js)
    {
        $this->attr(['onKeyDown' => $js]);
        return $this;
    }
    
    /**
     * 设置键盘按键被按下并松开事件
     *
     * @param string $js 脚本
     * @return $this
     */
    public function onKeyPress($js)
    {
        $this->attr(['onKeyPress' => $js]);
        return $this;
    }
    
    /**
     * 设置键盘按键被松开事件
     *
     * @param string $js 脚本
     * @return $this
     */
    public function onKeyUp($js)
    {
        $this->attr(['onKeyUp' => $js]);
        return $this;
    }
    
    /**
     * 设置文本被选中事件
     *
     * @param string $js 脚本
     * @return $this
     */
    public function onSelect($js)
    {
        $this->attr(['onSelect' => $js]);
        return $this;
    }
}
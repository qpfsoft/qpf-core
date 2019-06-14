<?php
namespace qpf\htmc;

use qpf\htmc\html\TagAttr;

/**
 * EventBuild 事件属性生成类
 *
 * 该类让html元素支持添加'onXX=""'事件属性，值支持元素可被添加的事件类型
 *
 * @author qiun
 *        
 */
class EventBuild extends TagAttr
{

    /**
     * 生成属性字符串
     * 
     * @param string $eventName 事件属性名
     * @param string $js 覆盖设置的js
     * @return string
     */
    private function buildString($eventName, $js = null)
    {
        $val = $js === null ? $this->{$eventName} : $js;
        
        if ($val !== null) {
            return ' ' . $eventName . '="' . $val . '"';
        }
    }

    /**
     * 点击事件触发js
     * 
     * @var string
     */
    public $onClick;

    /**
     * 设置点击事件
     * 
     * @param string $js
     *            触发脚本
     * @return $this
     */
    public function onClick($js)
    {
        $this->onClick = $js;
        return $this;
    }

    /**
     * 解析onclick属性
     * 
     * @param string $js            
     * @return string 事件Html属性字符串
     */
    public function parseOnClick($js = null)
    {
        return $this->buildString('onClick', $js);
    }

    /**
     * 双击事件
     * 
     * @var string
     */
    public $onDblClick;

    /**
     * 设置双击事件
     * 
     * @param string $js
     *            触发脚本
     * @return $this
     */
    public function onDblClick($js)
    {
        $this->onDblClick = $js;
        return $this;
    }

    /**
     * 解析双击事件属性
     * 
     * @param string $js
     *            不提供时采用对象属性
     */
    public function parseOnDblClick($js = null)
    {
        return $this->buildString('onDblClick', $js);
    }

    /**
     * 鼠标按下事件
     * 
     * @var string
     */
    public $onMouseDown;

    /**
     * 设置鼠标按下事件
     * 
     * @param string $js
     *            触发脚本
     * @return $this
     */
    public function onMouseDown($js)
    {
        $this->onMouseDown = $js;
        return $this;
    }

    /**
     * 解析鼠标按下事件属性
     * 
     * @param string $js
     *            不提供时采用对象属性
     */
    public function parseonMouseDown($js = null)
    {
        return $this->buildString('onMouseDown', $js);
    }

    /**
     * 鼠标移动事件
     * 
     * @var string
     */
    public $onMouseMove;

    /**
     * 设置鼠标移动事件
     * 
     * @param string $js
     *            触发脚本
     * @return $this
     */
    public function onMouseMove($js)
    {
        $this->onMouseMove = $js;
        return $this;
    }

    /**
     * 解析鼠标移动事件属性
     * 
     * @param string $js
     *            不提供时采用对象属性
     */
    public function parseonMouseMove($js = null)
    {
        return $this->buildString('onMouseMove', $js);
    }

    /**
     * 鼠标从某元素移开事件
     * 
     * @var string
     */
    public $onMouseOut;

    /**
     * 设置鼠标从某元素移开事件
     * 
     * @param string $js
     *            触发脚本
     * @return $this
     */
    public function onMouseOut($js)
    {
        $this->onMouseOut = $js;
        return $this;
    }

    /**
     * 解析鼠标从某元素移开事件属性
     * 
     * @param string $js
     *            不提供时采用对象属性
     */
    public function parseonMouseOut($js = null)
    {
        return $this->buildString('onMouseOut', $js);
    }

    /**
     * 鼠标移到某元素之上事件
     * 
     * @var string
     */
    public $onMouseOver;

    /**
     * 设置鼠标移到某元素之上事件
     * 
     * @param string $js
     *            触发脚本
     * @return $this
     */
    public function onMouseOver($js)
    {
        $this->onMouseOver = $js;
        return $this;
    }

    /**
     * 解析鼠标移到某元素之上事件属性
     * 
     * @param string $js
     *            不提供时采用对象属性
     */
    public function parseonMouseOver($js = null)
    {
        return $this->buildString('onMouseOver', $js);
    }

    /**
     * 鼠标移到某元素之上事件
     * 
     * @var string
     */
    public $onMouseUp;

    /**
     * 设置鼠标移到某元素之上事件
     * 
     * @param string $js
     *            触发脚本
     * @return $this
     */
    public function onMouseUp($js)
    {
        $this->onMouseUp = $js;
        return $this;
    }

    /**
     * 解析鼠标移到某元素之上事件属性
     * 
     * @param string $js
     *            不提供时采用对象属性
     */
    public function parseonMouseUp($js = null)
    {
        return $this->buildString('onMouseUp', $js);
    }

    /**
     * 元素失去焦点时触发事件
     * 
     * @var string
     */
    public $onBlur;

    /**
     * 设置元素失去焦点时触发事件
     * 
     * @param string $js
     *            触发脚本
     * @return $this
     */
    public function onBlur($js)
    {
        $this->onBlur = $js;
        return $this;
    }

    /**
     * 解析元素失去焦点时触发事件属性
     * 
     * @param string $js
     *            不提供时采用对象属性
     */
    public function parseonBlur($js = null)
    {
        return $this->buildString('onBlur', $js);
    }

    /**
     * 元素值改变时触发事件
     * 
     * @var string
     */
    public $onChange;

    /**
     * 设置元素值改变时触发事件
     * 
     * @param string $js
     *            触发脚本
     * @return $this
     */
    public function onChange($js)
    {
        $this->onChange = $js;
        return $this;
    }

    /**
     * 解析元素值改变时触发属性
     * 
     * @param string $js
     *            不提供时采用对象属性
     */
    public function parseonChange($js = null)
    {
        return $this->buildString('onChange', $js);
    }

    /**
     * 元素获得焦点时触发事件
     * 
     * @var string
     */
    public $onFocus;

    /**
     * 设置元素获得焦点时触发事件
     * 
     * @param string $js
     *            触发脚本
     * @return $this
     */
    public function onFocus($js)
    {
        $this->onFocus = $js;
        return $this;
    }

    /**
     * 解析元素获得焦点时触发属性
     * 
     * @param string $js
     *            不提供时采用对象属性
     */
    public function parseonFocus($js = null)
    {
        return $this->buildString('onFocus', $js);
    }

    /**
     * 键盘按键时事件
     * 
     * @var string
     */
    public $onKeyDown;

    /**
     * 设置键盘按键时事件
     * 
     * @param string $js
     *            触发脚本
     * @return $this
     */
    public function onKeyDown($js)
    {
        $this->onKeyDown = $js;
        return $this;
    }

    /**
     * 解析键盘按键时属性
     * 
     * @param string $js
     *            不提供时采用对象属性
     */
    public function parseonKeyDown($js = null)
    {
        return $this->buildString('onKeyDown', $js);
    }

    /**
     * 键盘按键被按下并松开事件
     * 
     * @var string
     */
    public $onKeyPress;

    /**
     * 设置键盘按键被按下并松开事件
     * 
     * @param string $js
     *            触发脚本
     * @return $this
     */
    public function onKeyPress($js)
    {
        $this->onKeyPress = $js;
        return $this;
    }

    /**
     * 解析键盘按键被按下并松开属性
     * 
     * @param string $js
     *            不提供时采用对象属性
     */
    public function parseonKeyPress($js = null)
    {
        return $this->buildString('onKeyPress', $js);
    }

    /**
     * 键盘按键被松开事件
     * 
     * @var string
     */
    public $onKeyUp;

    /**
     * 设置键盘按键被松开事件
     * 
     * @param string $js
     *            触发脚本
     * @return $this
     */
    public function onKeyUp($js)
    {
        $this->onKeyUp = $js;
        return $this;
    }

    /**
     * 解析键盘按键被松开属性
     * 
     * @param string $js
     *            不提供时采用对象属性
     */
    public function parseonKeyUp($js = null)
    {
        return $this->buildString('onKeyUp', $js);
    }

    /**
     * 文本被选中事件
     * 
     * @var string
     */
    public $onSelect;

    /**
     * 设置文本被选中事件
     * 
     * @param string $js
     *            触发脚本
     * @return $this
     */
    public function onSelect($js)
    {
        $this->onSelect = $js;
        return $this;
    }

    /**
     * 解析文本被选中属性
     * 
     * @param string $js
     *            不提供时采用对象属性
     */
    public function parseonSelect($js = null)
    {
        return $this->buildString('onSelect', $js);
    }
}
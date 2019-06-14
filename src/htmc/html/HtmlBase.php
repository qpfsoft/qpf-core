<?php
namespace qpf\htmc\html;

/**
 * HTML标签对象基类
 * 
 * 
 * 公共属性设置方法:
 * [[content]] : 设置标签中间的内容.
 * [[classAttr]] : 设置class属性
 * [[id]] : id值
 * [[style]] : 行内样式
 * [[hidden]] : 隐藏
 * [[title]] : 提示信息
 * [[tabindex]] : tag键次序
 * 
 * @author qiun
 *
 */
class HtmlBase extends TagEvent
{
    /**
     * 标签名称
     * @var string
     */
    protected $tagName = '';
    
    /**
     * 标签内容
     * @var string 纯文本内容
     */
    protected $tagContent;
    
    /**
     * 设置标签内容
     * 
     * @param string $value
     * @return $this
     */
    public function content($value)
    {
        $this->tagContent = $value;
        return $this;
    }

    /**
     * 获得HTML代码
     * 
     * - 有不同需求的标签, 进行覆盖重写
     * @return string
     */
    public function getHtml()
    {
        $code = "<{$this->tagName}{$this->parseAttr()}>{$this->tagContent}</{$this->tagName}>";
        $this->reset();
        return $code;
    }
    
    /**
     * 重设标签对象
     */
    public function reset()
    {
        $this->resetAttr();
        $this->tagContent = null;
    }
    
    // ---------------HTML 全局属性-------------------------------
    
    /**
     * 设置激活元素的快捷键
     * 
     * ~~~示例:
     * # 使用Alt + accessKey (或者 Shift + Alt + accessKey) 来访问带有指定快捷键的元素。
     * <a href="http://www.domain.com.cn/" accesskey="c">快捷键访问</a>
     * ~~~
     * - 只有opera游览器不支持
     * - 以下元素支持 accesskey 属性：
     * <a>, <area>, <button>, <input>, <label>, 
     * <legend> 以及 <textarea>。
     * @param string $value 按键名
     * @return $this
     */
    public function accesskey($value)
    {
        $this->attr(['accesskey' => $value]);
        return $this;
    }
    
    /**
     * 设置标签class属性
     * 
     * - 类名不能以数组开头, 除了IE支持.
     * - 不支持以下标签: base, head, html, meta, param, script, style 以及 title。
     * - 可赋予多个类, 用空格分割, 即把多个类合并.
     * @param string $value 样式类名
     * @return $this
     */
    public function classAttr($value)
    {
        $this->attr(['class' => $value]);
        return $this;
    }
    
    /**
     * 设置元素内容是否可编辑 - h5
     * @param string $value 默认`true`可编辑, 可能的值:
     * - `true` : 可编辑
     * - `false`: 不可编辑
     * @return $this
     */
    public function contentEditable($value = 'true')
    {
        $this->attr(['contenteditable' => $value]);
        return $this;
    }
    
    /**
     * 元素右键菜单 - h5
     * 
     * ~~~示例
     * < div contextmenu="mymenu" >
     *  <menu type="context" id="mymenu">
     *      <menuitem label="刷新"></menuitem>
     *      <menuitem label="分享"></menuitem>
     *  </menu>
     * < /div >
     * ~~~
     * 
     * - 目前只有火狐支持.
     * @param string $value 元素绑定的菜单ID
     * @return $this
     */
    public function contextMenu($value)
    {
        $this->attr(['contextmenu' => $value]);
        return $this;
    }
    
    /**
     * 设置自定义存储数据data-*属性 - h5
     * 
     * - 存储的数据能被页面js中利用, 以创建更快用户体验,
     * 即不进行ajax调用或服务端数据库查询.
     * - 属性名不能大写, 并且在前缀`data-`之后最少1个字符
     * - 属性值可以是任意字符
     * @param string $name 属性名, 自动前缀`data-`.
     * @param string $value 属性值
     * @return $this
     */
    public function dataAttr($name, $value)
    {
        $this->attr(['data-' . $name  => $value]);
        return $this;
    }
    
    /**
     * 设置元素内容的文本方向
     * 
     * - 在以下标签中无效：<base>, <br>, <frame>, 
     * <frameset>, <hr>, <iframe>, <param> 以及 <script>
     * 
     * @param string $value 可能的值:
     * - `ltr` : 默认, 从左向右. 即文本`左对齐`.
     * - `rtl` : 从右向左, 即文本`右对齐`.
     * @return $this
     */
    public function dir($value = 'ltr')
    {
        $this->attr(['dir' => $value]);
        return $this;
    }
    
    /**
     * 设置元素是否可拖动 - h5
     * 
     * - 链接和图片默认是可拖动的.
     * - 只是拖动效果.内容副本跟随指针.
     * - IE9以下不支持
     * 
     * @param string $value 可能的值:
     * - `true` : 有拖动效果
     * - `false`: 不可拖动
     * - `auto` : 使用游览器默认行为
     * @return $this
     */
    public function draggable($value)
    {
        $this->attr(['draggable' => $value]);
        return $this;
    }
    
    /**
     * 设置元素在拖动时, 是否拷贝,移动,链接. - h5
     * 
     * - 目前没有游览器支持
     * @param string $value 可能的值:
     * - `copy` : 拖动数据会产生被拖动数据的副本。
     * - `move` : 拖动数据会导致被拖动数据被移动到新位置。
     * - `link` : 拖动数据会产生指向原始数据的链接。
     * @return $this
     */
    public function dropzone($value)
    {
        $this->attr(['dropzone' => $value]);
        return $this;
    }
    
    /**
     * 设置元素为隐藏 - h5
     * 
     * - 仅IE不支持.
     * @return $this
     */
    public function hidden()
    {
        $this->attr(['hidden' => 'hidden']);
        return $this;
    }
    
    /**
     * 设置元素ID
     * 
     * - html中id只必须是唯一.
     * @param string $value ID名称
     * @return $this
     */
    public function id($value)
    {
        $this->attr(['id' => $value]);
        return $this;
    }
    
    /**
     * 设置元素内容的语言
     * 
     * 在以下标签中无效：<base>, <br>, <frame>, <frameset>, 
     * <hr>, <iframe>, <param> 以及 <script>
     * 
     * - 一般设置给<html>标签
     * 
     * @param string $value 语言代码:
     * - zh : 中文
     * 更多参考地址:
     * http://www.w3school.com.cn/tags/html_ref_language_codes.asp
     * @return $this
     */
    public function lang($value)
    {
        $this->attr(['lang' => $value]);
        return $this;
    }
    
    /**
     * 设置元素是否进行拼写和语法检查 - h5
     * 
     * 可以对以下内容进行拼写检查:
     * - input元素中的文本, 非密码
     * - textarea 标签中的文本
     * - 可编辑元素中的文本
     * 
     * @param string $value 可能的值:
     * - `true` : 进行拼写检查.
     * - `false` : 不检查.
     * @return $this
     */
    public function spellcheck($value)
    {
        $this->attr(['spellcheck' => $value]);
        return $this;
    }
    
    /**
     * 设置元素行内样式
     * @param string $value css样式
     * @return $this
     */
    public function style($value)
    {
        $this->attr(['style' => $value]);
        return $this;
    }
    
    /**
     * 设置元素的tag键次序
     * 
     * 以下元素支持 tabindex 属性：<a>, <area>, <button>, 
     * <input>, <object>, <select> 以及 <textarea>。
     * 
     * @param string $value 数值, 第一个是1.
     * @return $this
     */
    public function tabindex($value)
    {
        $this->attr(['tabindex' => $value]);
        return $this;
    }
    
    /**
     * 设置元素的额外信息
     * 
     * - 鼠标移动到元素上才显示
     * @param string $value 工具提示文本
     * @return $this
     */
    public function title($value)
    {
        $this->attr(['title' => $value]);
        return $this;
    }
    
    /**
     * 设置元素内容是否应该翻译 - h5
     * 
     * - 目前无游览器支持.
     * @param string $value 可能的值:
     * - `yes` : 可翻译元素内容
     * - `no`  : 不可翻译元素内容
     * @return $this
     */
    public function translate($value)
    {
        $this->attr(['translate' => $value]);
        return $this;
    }
}
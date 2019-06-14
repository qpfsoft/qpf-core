<?php
namespace qpf\htmc\html;

/**
 * Form 类代表了一个HTML的form标签包含可设置的属性.
 *
 * 表单对象中可调用以下类入口:
 * group (即Fieldset) : 将表单中的不同分类作用的input进行分组.
 *
 * 创建表单的方法:
 * ```
 * $form = new Form();
 * $form->action('post.php')->method('post');
 * // 在开始标签之前,设置的属性才有效.
 * $form->tagStart();
 * //... 这里调用input管理器
 * $form->tagEnd();
 * ```
 * 
 * 规定在向服务器发送表单数据之前如何对其进行编码。
 * （适用于 method="post" 的情况）
 * - application/x-www-form-urlencoded : 默认，发送到服务器之前，
 * 所有字符都会进行编码（空格转换为 "+" 加号，特殊符号转换为 ASCII HEX 值）
 * - multipart/form-data ： 不对字符编码。在使用包含文件上传控件的表单时，必须使用该值。
 * - text/plain : 不对特殊字符进行编码，只会将空格转为加号
 *
 * 注释：autocomplete 属性适用于 <form>，以及下面的
 * <input> 类型：text, search, url, telephone, email, password, datepickers, range 以及 color。
 *
 * @author qiun
 */
class Form extends TagAttr
{
    /**
     * 表单分组对象
     * 
     * @var \qpf\htmc\Fieldset
     */
    private $fieldset;

    /**
     * 标签对象
     * 
     * @var \qpf\htmc\html\Label
     */
    private $label;

    /**
     * 设置表单id
     * 
     * # 在 form 元素之外，但仍然是表单的一部分
     * <form action="demo_form.asp" method="get" id="user_form">
     * First name:<input type="text" name="fname" />
     * </form>
     * Last name: <input type="text" name="lname" form="user_form" />
     * 
     * @param string $val            
     * @return $this
     */
    public function id($val)
    {
        $this->attr(['id' => $val]);
        return $this;
    }

    /**
     * 设置表单name属性值
     * 
     * 在 XHTML 中，name 属性已被废弃。使用全局 id 属性代替。
     * 
     * @param string $val            
     * @return $this
     */
    public function name($val)
    {
        $this->attr(['name' => $val]);
        return $this;
    }

    /**
     * 表单是否记录历史输入 - h5
     * 
     * 语法：
     * <form autocomplete="on|off">
     * - on 默认。规定启用自动完成功能。
     * - off 规定禁用自动完成功能。
     * 
     * @param boolean $val 默认`false`, 即不记录
     * @return $this
     */
    public function autoComplete($val = false)
    {
        $val = ((bool) $val ? 'on' : 'off');
        $this->attr(['autocomplete' => $val]);
        return $this;
    }

    /**
     * 不对表单进行html5原生验证 - h5
     *
     * 用法：
     * # 禁止HTML5执行原生的效验
     * <form action="/search" method="get" novalidate>
     * @return $this
     */
    public function novalidate()
    {
        $this->attr('novalidate');
        return $this;
    }

    /**
     * 设置表单提交页面地址
     * 
     * @param string $val            
     * @return $this
     */
    public function action($url)
    {
        $this->attr(['action' => $url]);
        return $this;
    }

    /**
     * 设置表单提交方式
     * 
     * @param string $val 默认POST
     * @return \qpf\htmc\Form
     */
    public function method($val = 'post')
    {
        $arr = [
            'get',
            'post'
        ];
        if (in_array(strtolower($val), $arr)) {
            $this->attr(['method' => $val]);
        } else {
            $this->attr(['method' => 'post']);
        }
        
        return $this;
    }
    
    /**
     * 数据编码类型
     * 
     * - 上传必须使用`post`
     * @param string $val 可能的值:
     * - `def` : 默认, 即`application/x-www-form-urlencoded`, 发送前编码所有字符串
     * - `up` : 包含文件上传的表单, 即`multipart/form-data`, 不对字符编码
     * - `txt` : 纯文本格式, 空格转换为`+`加号, 但不对特殊字符编码 `text/plain`
     * @return \qpf\htmc\Form
     */
    public function enctype($val = 'def')
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
     * 规定如何打开提交URL
     * @param string $val 可能的值:
     * - `_blank` : 在新窗口/选项卡中打开
     * - `_self` : 在同一框架中打开, 默认
     * - `_parent` : 在父框架中打开
     * - `_top` : 在整个窗口中打开
     * - <framename> : 在指定的框架中打开
     * @return \qpf\htmc\Form
     */
    public function target($val)
    {
        $this->attr(['target' => $val]);
        return $this;
    }
    
    /**
     * 设置服务器可处理的字符集
     * @param string $val 可能的值:
     * - `UTF-8` : Unicode 字符编码
     * - `gb2312` : 简体中文字符集
     * @return \qpf\htmc\Form
     */ 
    public function accept_charset($val)
    {
        $this->attr(['accept-charset' => $val]);
        return $this;
    }

    /**
     * 表单开始标签
     * 
     * @return string
     */
    public function tagStart()
    {
        $code = '<form' . $this->parseAttr() . '>';
        $this->reset();
        return $code;
    }

    /**
     * 表单结束标签
     */
    public function tagEnd()
    {
        return '</form>' . PHP_EOL;
    }

    /**
     * 获取表单HTMl
     *
     * @param string $input
     *            要放入表单中的input元素html代码
     * @return string
     */
    public function getHtml($input = null)
    {
        $code = $this->tagStart() . PHP_EOL . $input . PHP_EOL . $this->tagEnd();
        $this->reset();
        return $code;
    }

    /**
     * 获取对象描述
     */
    public function __toString()
    {
        return $this->getHtml();
    }
    
    /**
     * 重置当前对象
     */
    public function reset()
    {
        $this->resetAttr();
    }
    
    //-------------------外链对象------------------------

    /**
     * 表单分组管理器
     *
     * @param string $legend
     *            描述表单的标签名称
     * @return \qpf\htmc\Fieldset
     */
    public function group()
    {
        if ($this->fieldset === null) {
            $this->fieldset = new \qpf\htmc\form\Fieldset();
        }
        
        return $this->fieldset;
    }

    /**
     * 表单元素标签管理器
     *
     * @return \qpf\htmc\html\Label
     */
    public function label()
    {
        if ($this->label === null) {
            $this->label = new Label();
        }
        
        return $this->label;
    }
}
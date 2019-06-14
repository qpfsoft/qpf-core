<?php
namespace qpf\htmc;

use qpf\exception\InvalidParamException;

/**
 * input 对象封装规整了各种表单元素，进行统一了设置方式。
 *
 * ~~~php
 *
 * # select 下拉列表的创建
 * $htm->htmCore[] = '<br> 选择2： ' . $input->select('type2', [
 * ['a' => '苹果'],
 * ['b' => '香蕉', true],
 * ['c' => '菠萝'],
 * ]);
 * # 等价于
 * $htm->htmCore[] = '<br> 选择3： ' . $input->select('type3', [
 * ['key'=> 'a', 'value' => '苹果'],
 * ['key'=> 'b', 'value' => '香蕉', true],
 * ['key'=> 'c', 'value' => '菠萝'],
 * ]);
 * # 等价于
 * $htm->htmCore[] = '<br> 选择3： ' . $input->select('type3', [
 * 'a' => '苹果',
 * 'b' => '香蕉',
 * 'c' => '菠萝',
 * 'true' => 'b',
 * ]);
 * ~~~
 *
 * [[Input]]一共了可直接调用的元素方法，但是需要常用属性可使用[[option]]来创建配置数组或手动书写配置数组。
 * [[getTypeObj]] 提供了创建指定类型对象的方法
 *
 * @author qiun
 *        
 */
class Input
{

    /**
     * 元素的名称
     *
     * 只有设置了 name 属性的表单元素才能在提交表单时传递它们的值
     * 
     * @var string
     */
    public $name;

    /**
     * 规定通过文件上传来提交的文件的类型。
     *
     * accept 属性只能与 <input type="file"> 配合使用
     * ['audio/*', 'video/*', 'image/*']
     * 请避免使用该属性。应该在服务器端验证文件上传。
     *
     * # 可以接受 GIF 和 JPEG 两种图像
     * <input type="file" name="pic" id="pic" accept="image/gif, image/jpeg" />
     * # 如果不限制图像的格式，可以写为：accept="image/*"
     *
     * @var string
     */
    public $accept;

    /**
     * 允许用户输入到 <input> 元素的多个值
     *
     * 用于以下 input 类型：email 和 file
     *
     * # 尝试选取一张或者多种图片
     * <input type="file" name="img" multiple>
     * 
     * @var string
     */
    public $multiple;

    /**
     * 定义图像输入的替代文本
     * 只针对<input type="image">
     * 
     * @var string
     */
    public $alt;

    /**
     * 规定显示为提交按钮的图像的 URL
     *
     * 只针对<input type="image">
     * 
     * @var string
     */
    public $src;

    /**
     * 规定 <input>元素的高度
     * 只针对<input type="image">
     * 
     * @var string
     */
    public $height;

    /**
     * 属性规定input元素的宽度 - html5
     *
     * 只针对type="image"
     * 
     * @var String 像素
     */
    public $width;

    /**
     * 规定元素输入字符个数
     *
     * <input size="number">
     * 适用于下面的 input 类型：text、search、tel、url、email 和 password。
     * 元素中允许的最大字符数，请使用 maxlength 属性。
     * 
     * @var string
     */
    public $size;

    /**
     * 元素输入字段是否应该启用自动完成功能/即记录历史输入。
     * 
     * @var string on/off
     */
    public $autocomplete;

    /**
     * 规定当页面加载时 input 元素应该自动获得焦点 - 状态属性
     * 不适用于<input type="hidden" autofocus>
     * <input autofocus="autofocus">
     * 
     * @var string
     */
    public $autofocus;

    /**
     * 规定在页面加载时应该被预先选定的input元素 - 状态属性
     * (只针对 type="checkbox" 或者 type="radio")
     * 
     * @var string
     */
    public $checked;

    /**
     * 规定应该禁用的input元素- 状态属性
     *
     * 表单中被禁用的 <input disabled> 元素不会被提交
     * 
     * @var string
     */
    public $disabled;

    /**
     * 指定当前元素所属表单，指定后元素可放在form元素外
     * <input form="form_id"> HTML5 中的新属性
     * 
     * @var string
     */
    public $form;

    /**
     * 规定当表单提交时处理输入控件的文件的 URL
     * <input type="submit" formaction="URL" value="提交">
     * <input type="image" formaction="URL">
     * 
     * @var string
     */
    public $formaction;

    /**
     * 规定当表单数据提交到服务器时如何编码
     *
     * - 仅适用于 method="post" 的表单
     * - 覆盖 <form> 元素的 enctype 属性
     * - application/x-www-form-urlencoded ：默认，发送到服务器之前，
     * 所有字符都会进行编码（空格转换为 "+" 加号，特殊符号转换为 ASCII HEX 值）
     * - multipart/form-data ： 不对字符编码。在使用包含文件上传控件的表单时，必须使用该值。
     * - text/plain ：不对特殊字符进行编码，只会将空格转为加号
     *
     * # 实例
     * <input type="submit" formenctype="multipart/form-data"
     * value="以 Multipart/form-data 提交">
     * 
     * @var string
     */
    public $formenctype;

    /**
     * 定义发送表单数据到 action URL 的 HTTP 方法
     *
     * (只适合 type="submit" 和 type="image")
     * 
     * @var string get/post
     */
    public $formmethod;

    /**
     * 覆盖 <form> 元素的 novalidate 属性 - 状态属性
     * 不启用原生表单验证
     * 
     * @var string
     */
    public $formnovalidate;

    /**
     * 规定表示提交表单后在哪里显示接收到响应的名称或关键词
     * _blank _self _parent _top framename
     * (只适合 type="submit" 和 type="image")
     * 
     * @var string
     */
    public $formtarget;

    /**
     * 属性引用 <datalist> 元素，其中包含 <input> 元素的预定义选项。
     * HTML5 中的新属性
     * ~~~
     * <input list="browsers">
     *
     * <datalist id="browsers">
     * <option value="Internet Explorer">
     * <option value="Firefox">
     * <option value="Google Chrome">
     * <option value="Opera">
     * <option value="Safari">
     * </datalist>
     * ~~~
     * 
     * @var string
     */
    public $list;

    /**
     * 规定 <input> 元素的最大值 - HTML5
     *
     * max="1979-12-31"
     *
     * #数量 (在1和5之间)
     * <input type="number" name="quantity" min="1" max="5">
     * 
     * @var string 数字或日期
     */
    public $max;

    /**
     * 元素中允许的最大字符数。
     *
     * <input maxlength="10">
     * 
     * @var int
     */
    public $maxlength;

    /**
     * 元素的最小值 - HTML5
     *
     * min="2000-01-02"
     * 
     * @var string 数字或日期
     */
    public $min;

    /**
     * 规定用于验证 <input> 元素的值的正则表达式 - HTML5
     *
     * <input type="text" name="country_code" pattern="[A-Za-z]{3}" title="三个字母的国家代码">
     * 
     * @var unknown
     */
    public $pattern;

    /**
     * 文本框未输入值时的提示文字 - HTML5
     *
     * <input placeholder="text">
     * 属性适用于下面的 input 类型：text、search、url、tel、email 和 password。
     * 
     * @var string
     */
    public $placeholder;

    /**
     * 规定输入字段是只读的
     *
     * <input readonly>
     * 
     * @var string
     */
    public $readonly;

    /**
     * 规定必需在提交表单之前填写输入字段 - HTML5
     *
     * <input required>
     * 属性适用于下面的 input 类型：text、search、url、tel、email、
     * password、date pickers、number、checkbox、radio 和 file
     * 
     * @var string
     */
    public $required;

    /**
     * 设置输入框为有数字间隔类型 - HTML5
     *
     * <input type="number" name="points" step="3">
     * 
     * @var int 规定输入字段的合法数字间隔
     */
    public $step;

    /**
     * input元素的类型
     * 
     * @var array
     */
    public $typeMap = [
        'button',
        'checkbox',
        'color',
        'date',
        'datetime',
        'datetime-local',
        'email',
        'file',
        'hidden',
        'image',
        'month',
        'number',
        'password',
        'radio',
        'range',
        'reset',
        'search',
        'submit',
        'tel',
        'text',
        'time',
        'url',
        'week'
    ];

    public $type;

    /**
     * 创建Input元素对象
     * 
     * @var \qpf\htmc\CreateInput
     */
    public $createInput;

    /**
     * 支持静态实例对象
     * 
     * @return $this
     */
    static public function this()
    {
        return new self();
    }

    /**
     * 设置input类型
     * 
     * @param string $type            
     * @return \qpf\htmc\Input
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * 获取设置input类型字符串
     * 
     * @param string $type
     *            该设置类型可覆盖属性设置，未设置采用对象属性
     * @return string
     */
    public function getType($type = '')
    {
        if (empty($type)) {
            $type = $this->type;
        }
        return ' type="' . $type . '"';
    }

    /**
     * 设置input元素name属性
     * 
     * @param string $name            
     * @return \qpf\htmc\Input
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * 获取设置input元素name属性字符串
     * 
     * @param string $name
     *            该设置类型可覆盖属性设置，未设置采用对象属性
     * @return string
     */
    public function getName($name = '')
    {
        if (empty($name)) {
            $name = $this->name;
        }
        return ' name="' . $name . '"';
    }

    /**
     * 获取设置文本框初始内容的字符串
     * 
     * @param string $value            
     * @return string
     */
    public function getValue($value)
    {
        if ($value === null) {
            return '';
        }
        return ' value="' . $value . '"';
    }

    /**
     * 获得input创建对象
     *
     * @return \qpf\htmc\CreateInput
     */
    public function createInput()
    {
        if (! is_object($this->createInput)) {
            $this->createInput = new \qpf\htmc\input\CreateInput();
        }
        
        return $this->createInput;
    }

    /**
     * 创建指定元素的选项配置数组
     *
     * @param string $type
     *            根据元素类型过滤无效属性，
     *            默认为`null`不启用有效性过滤。
     * @return \qpf\htmc\input\Option
     */
    public function option($type = null)
    {
        static $opt = null;
        
        if ($type === null) {
            $opt = new \qpf\htmc\input\Option();
        } elseif ($type !== null && in_array(strtolower($type), $this->typeMap)) {
            $opt = new \qpf\htmc\input\Option();
            $opt->isType($type);
        } else {
            throw new InvalidParamException('Input元素类型错误：' . $type);
        }
        
        return $opt;
    }

    /**
     * 创建指定元素对象
     * 
     * @param string $type
     *            元素类型
     */
    public function getTypeObj($type)
    {
        if (in_array($type, $this->typeMap)) {
            $class = '\\qpf\\htmc\\input\\' . ucwords($type);
            if (class_exists($class)) {
                return new $class();
            }
        }
    }

    /**
     * 文本框
     *
     * @param string $name
     *            name属性，数据提交对应的名称
     * @param string $value
     *            预输入的值，null时不设置，''空字符串会设置该属性
     * @param array $option
     *            标签其他属性
     * @return string 文本框Html代码
     */
    public function text($name, $value = null, $option = [])
    {
        $option['name'] = $name;
        $option['value'] = $value;
        
        return $this->createInput()->create('text', $option);
    }

    /**
     * 密码框
     * 
     * @param string $name
     *            name属性，数据提交对应的名称
     * @param string $value
     *            预输入的值，null时不设置，''空字符串会设置该属性
     * @param string $title
     *            文本框的描述
     * @param string $option
     *            标签其他属性
     * @return string 文本框Html代码
     */
    public function password($name, $value = null, $option = [])
    {
        $option['name'] = $name;
        $option['value'] = $value;
        
        return $this->createInput()->create('password', $option);
    }

    /**
     * 普通按钮
     * 
     * @param string $name
     *            元素名称
     * @param string $value
     *            按钮文字
     * @param array $option
     *            标签其他属性
     * @return string 元素html代码
     */
    public function button($name, $value, $option = [])
    {
        $option['name'] = $name;
        $option['value'] = $value;
        
        return $this->createInput()->create('button', $option);
    }

    /**
     * 重置表单按钮
     * 
     * @param string $value
     *            按钮文字
     * @param array $option
     *            其他属性
     * @return string 元素html代码
     */
    public function reset($value = null, $option = [])
    {
        // $option['name'] = $name;
        $option['value'] = $value;
        
        return $this->createInput()->create('reset', $option);
    }

    /**
     * 提交按钮
     * 
     * @param string $name
     *            元素名称
     * @param string $value
     *            按钮文字
     * @param array $option
     *            标签其他属性
     * @return string 元素html代码
     */
    public function submit($name, $value = null, $option = [])
    {
        $option['name'] = $name;
        $option['value'] = $value;
        
        return $this->createInput()->create('submit', $option);
    }

    /**
     * 隐藏元素
     * 
     * @param string $name
     *            名称
     * @param string $value
     *            值
     * @param array $option
     *            其他属性
     * @return string 元素html代码
     */
    public function hidden($name, $value = null, $option = [])
    {
        $option['name'] = $name;
        $option['value'] = $value;
        
        return $this->createInput()->create('hidden', $option);
    }

    /**
     * 上传文件
     *
     * ['audio/*', 'video/*', 'image/*']
     * 
     * @param string $name            
     * @param array $option            
     * @return string 元素html代码
     */
    public function file($name, $option = [])
    {
        $option['name'] = $name;
        
        return $this->createInput()->create('file', $option);
    }

    /**
     * 单选按钮
     * 
     * @param string $name
     *            单选组名称
     * @param string $value
     *            选项值
     * @param array $option
     *            其它属性
     * @return string 元素html代码
     */
    public function radio($name, $value, $option = [])
    {
        $option['name'] = $name;
        $option['value'] = $value;
        
        return $this->createInput()->create('radio', $option);
    }

    /**
     * 多选框
     * 
     * @param string $name
     *            单选组名称
     * @param string $value
     *            选项值
     * @param array $option
     *            其它属性
     * @return string 元素html代码
     */
    public function checkbox($name, $value, $option = [])
    {
        $option['name'] = $name . '[]';
        $option['value'] = $value;
        
        return $this->createInput()->create('checkbox', $option);
    }

    /**
     * 下拉列表选项
     *
     * ~~~php
     * 二维数组格式：
     * [
     * ['选项值' => '选项描述', true, ...]
     * ]
     * - true ： 表示选中显示该选项
     * - ... ： 其它option对象属性配置，格式'属性名=>值'
     * 一维数组格式：
     * [
     * '选项值' => '选项描述',
     * '选项值2' => '选项描述2',
     * ...
     * 'true' => '选项值2', // 设置要选中的选项
     * ]
     * 对象配置格式：
     * [
     * [
     * 'key' => '',
     * 'value' => '',
     * 'selected' => '',
     * ],
     * ...
     * 同上格式
     * ]
     * ~~~
     * 
     * @param string $name
     *            元素名称
     * @param array $select
     *            列表数组
     * @param array $option
     *            其它属性
     * @return string 元素html代码
     */
    public function select($name, $select = [], $option = [])
    {
        $option['name'] = $name;
        return Select::this($option)->addArray($select)->gethtml();
    }
}
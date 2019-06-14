<?php
namespace qpf\htmc\input;

use qpf;
use qpf\htmc\input\Text;

/**
 * CreateInput 类用于解析input元素对象的公共属性，并会调用
 * input元素对象自身特有属性解析方法，来生成html代码
 *
 * - $testModel 设置true后可以单页进行测试类
 *
 * ~~~php
 * $o = new CreateInput();
 * # 可创建所有类型的input元素，内置单例加载依赖。
 * $o->create('text', $config);
 *
 * # 创建文本输入框，通过对象配置数组来实现
 * $o->create('text', [
 * 'name' => 'user',
 * 'class' => 't-class',
 * 'title' => 'input-text-user',
 * 'size' => '10',
 * 'maxLength' => '30',
 * 'value' => 'Hi-QPf',
 * 'form' => 'form1',
 * 'disabled' => false,
 * 'autofocus' => false,
 * 'readonly' => false,
 * 'autocomplete' => true,
 * 'required' => true,
 * 'tabindex' => 1,
 * 'pattern' => '[A-Za-z]{3}',
 * 'placeholder' => 'username/email',
 * ]);
 *
 * # 创建文本框输入框，不熟悉属性可通过对象直接来生成
 * Text::this()->name('pwd')->getHtml();
 * # 创建文本框，静态方法直接根据配置生成
 * Text::this(['name'=>'email']);
 * ~~~
 * 
 * @author qiun
 *        
 */
class CreateInput
{

    /**
     * 测试模式
     *
     * 单页执行模式,无法使用框架依赖载入，需自动载入所需元素文件
     * 框架模式需要设置为false，否则相对路径无法加载，造成报错。
     * 
     * @var boolean
     */
    private $testModel = false;

    /**
     * 内置载入依赖文件
     * 
     * @param string $tag
     *            Input元素类型
     */
    public function loadInputFile($tag)
    {
        if ($this->testModel) {
            require_once './input/' . strtoupper($tag) . '.php';
        }
    }

    /**
     * input元素的类型
     * 
     * @var array
     */
    private $typeMap = [
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

    /**
     * 解析Name属性为标签属性
     * 
     * @param unknown $val            
     */
    public function parseName($val)
    {
        return ' name="' . $val . '"';
    }

    /**
     * 解析Type属性
     *
     * 如果不属于input标签元素，就原样返回
     * 
     * @param string $val
     *            元素类型
     */
    public function parseType($val)
    {
        if (in_array($val, $this->typeMap)) {
            return 'input type="' . $val . '"';
        }
        return $val;
    }

    /**
     * 解析Class属性
     * 
     * @param string $val
     *            多个用空格分开
     */
    public function parseClass($val)
    {
        if ($val !== null) {
            return ' class="' . $val . '"';
        }
    }

    /**
     * 解析Title属性
     * 
     * @param string $val
     *            鼠标经过元素的提示
     */
    public function parseTitle($val)
    {
        if ($val !== null) {
            return ' title="' . $val . '"';
        }
    }

    /**
     * 解析Form属性
     * 
     * @param string $val
     *            元素所属form表单
     */
    public function parseForm($val)
    {
        if ($val !== null) {
            return ' form="' . $val . '"';
        }
    }

    /**
     * 解析disabled属性
     * 
     * @param boolean $val
     *            是否禁用元素,禁用的元素不会提交
     */
    public function parseDisabled($val)
    {
        if ($val !== null) {
            return $val ? ' disabled' : '';
        }
    }

    /**
     * 解析autofocus属性
     * 
     * @param boolean $val
     *            是否自动获得焦点
     */
    public function parseAutofocus($val)
    {
        if ($val !== null) {
            return $val ? ' autofocus' : '';
        }
    }

    /**
     * 解析readonly属性
     * 
     * @param boolean $val
     *            元素是否只读
     */
    public function parseReadonly($val)
    {
        if ($val !== null) {
            return $val ? ' readonly' : '';
        }
    }

    /**
     * 解析autocomplete属性
     * 
     * @param boolean $val
     *            元素是否只读
     */
    public function parseAutocomplete($val)
    {
        if ($val !== null) {
            return $val ? ' autocomplete="on"' : ' autocomplete="off"';
        }
    }

    /**
     * 解析required属性
     * 
     * @param boolean $val
     *            元素是否使用HTML5必填验证
     */
    public function parseRequired($val)
    {
        if ($val !== null) {
            return $val ? ' required' : '';
        }
    }

    /**
     * 解析pattern属性
     * 
     * @param string $val
     *            自定义原生表单验证的正则
     */
    public function parsePattern($val)
    {
        if ($val !== null) {
            return ' pattern="' . $val . '"';
        }
    }

    /**
     * 解析tabindex属性
     * 
     * @param integer $val
     *            tab键切换序列值
     */
    public function parseTabindex($val)
    {
        if ($val !== null) {
            return ' tabindex="' . $val . '"';
        }
    }

    /**
     * 解析placeholder属性
     * 
     * @param string $val
     *            未输入内容时显示的提示
     * @return string
     */
    public function parsePlaceHolder($val)
    {
        if ($val !== null) {
            return ' placeholder="' . $val . '"';
        }
    }

    /**
     * 可创建input所有类型的html
     *
     * @param string $inputType
     *            Input元素类型
     * @param array $config
     *            元素配置
     * @return string|false 返回元素HTML,错误类型返回false
     */
    public function create($inputType, $config)
    {
        if (!in_array($inputType, $this->typeMap)) {
            return false;
        }
        
        $this->loadInputFile($inputType);
        $class = '\\qpf\\htmc\\input\\' . $inputType;
        $object = new $class();
        foreach ($config as $name => $value) {
            $object->$name = $value;
        }
        
        return $this->getHtml($object);
    }

    /**
     * 获取HTMl代码
     * 
     * @param \qpf\htmc\Input $object            
     */
    public function getHtml($object)
    {
        $htm = '<';
        
        foreach ($object as $name => $value) {
            $parseFunc = 'parse' . $name;
            if ($value === null) { // 未设置的属性
                continue;
            } elseif (method_exists($this, $parseFunc)) { // 优先使用当前解析器
                $htm .= $this->$parseFunc($value);
            } elseif (method_exists($object, $parseFunc)) { // 后使用自身解析器
                $htm .= $object->$parseFunc($value);
            } elseif ($value !== null) { // 未实现的解析器
                echo 'htmc未实现的解析器提示：位置`\qpf\htmc\CreateInput::NotParse-属性名`:' . $name . ', 值:' . $value . ', 值类型:' . gettype($value) . '<br>';
            } else {
                echo 'error:' . $name;
            }
        }
        
        $htm .= '>';
        
        return $htm;
    }
}
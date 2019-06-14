<?php
namespace qpf\htmc;

use qpf\core\Config;

/**
 * 下拉列表元素
 *
 * Select 对象代表一个下拉列表对象，可添加下拉元素option对象。
 *
 * 该元素目前只支持onchange，选择下拉选项改变时触发事件。
 *
 * ~~~php
 * # 创建一个下拉列表，并选中指定的选项
 * Select::this()->name('type')
 * ->add(0, '请选择')
 * ->add(1, '选项1')
 * ->add(2, '选项2', true) // 会被选中显示
 * ->add(3, '选项3')
 * ->getHtml(); // 生成html
 * ~~~
 * 
 * @author qiun
 *        
 */
class Select
{

    /**
     * 元素名称
     * 
     * @var string
     */
    public $name;

    /**
     * 样式类
     * 
     * @var string
     */
    public $class;

    /**
     * 下拉列表中的可见行数
     * 
     * @var string
     */
    public $size;

    /**
     * 鼠标经过描述
     * 
     * @var string
     */
    public $title;

    /**
     * 元素所属表单
     * 
     * @var string
     */
    public $form;

    /**
     * 列表选项管理器
     * 
     * @var \qpf\htmc\SelectOption
     */
    public $option;

    /**
     * Tab键序列号
     * 
     * @var int
     */
    public $tabIndex;

    /**
     * 元素是否禁用
     * 
     * @var boolean
     */
    public $disabled;

    /**
     * 是否自动获得焦点
     * 
     * @var boolean
     */
    public $autofocus;

    /**
     * 原生验证 - 必须输入值
     * 
     * @var boolean
     */
    public $required;

    /**
     * 允许用户选择多个选项
     * 
     * @var boolean
     */
    public $multiple;

    /**
     * 下拉选择事件
     *
     * 元素值改变时触发事件
     * 
     * @var string 保存触发程序
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
    private function parseonChange($js = null)
    {
        $val = $js === null ? $this->onChange : $js;
        
        if ($val !== null) {
            return ' onChange="' . $val . '"';
        }
    }

    /**
     * 创建自身实例
     *
     * @param array $config
     *            对象配置数组
     * @return \qpf\htmc\Select
     */
    static public function this($config = [])
    {
        if (empty($config)) {
            return new self();
        }
        
        if (is_array($config)) {
            $object = new self();
            Config::setObject($object, $config);
            
            return $object;
        }
    }

    /**
     * 获得下拉列表Html
     *
     * @return string
     */
    public function gethtml()
    {
        $htm = '<select' . $this->parseName() . $this->parseClass($this->class) . $this->parseAutofocus($this->autofocus) . $this->parseRequired($this->required) . $this->parseDisabled($this->disabled) . $this->parseForm($this->form) . $this->parseTabindex($this->tabIndex) . $this->parseTitle($this->title) . $this->parseMultiple($this->multiple) . $this->parseSize($this->size) . $this->parseonChange() . '>';
        $htm .= $this->option()->getHtmls();
        $htm .= '</select>';
        
        return $htm;
    }

    /**
     * 设置元素名称
     * 
     * @param string $val            
     * @return $this
     */
    public function name($val)
    {
        $this->name = $val;
        return $this;
    }

    /**
     * 设置元素样式类
     * 
     * @param string $val            
     * @return $this
     */
    public function classes($val)
    {
        $this->class = $val;
        return $this;
    }

    /**
     * 设置鼠标经过提示
     * 
     * @param string $val            
     * @return \qpf\htmc\Select
     */
    public function title($val)
    {
        $this->title = $val;
        return $this;
    }

    /**
     * 设置下拉可显示选项的个数
     * 
     * @param integer $val
     *            默认值为1，设置时要大于1
     * @return \qpf\htmc\Select
     */
    public function size($val)
    {
        $this->size = $val;
        return $this;
    }

    /**
     * 设置元素所属表单
     * 
     * @param string $val            
     * @return \qpf\htmc\Select
     */
    public function form($val)
    {
        $this->form = $val;
        return $this;
    }

    /**
     * 返回下拉选项管理器
     * 
     * @return \qpf\htmc\SelectOption
     */
    public function option()
    {
        if ($this->option === null) {
            $this->option = new \qpf\htmc\SelectOption();
        }
        
        return $this->option;
    }

    /**
     * 添加一条选项
     *
     * @param string $key
     *            选项值
     * @param string $value
     *            选项描述
     * @param boolean $select
     *            是否选中显示
     * @return $this
     */
    public function add($key, $value, $select = false)
    {
        $this->option()->addOption($key, $value, $select);
        
        return $this;
    }

    /**
     * 添加一组选项
     *
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
     * 
     * @param array $array            
     * @return \qpf\htmc\Select
     */
    public function addArray($array)
    {
        $this->option()->setOptions($array);
        
        return $this;
    }

    /**
     * 设置tab键盘切换序号
     * 
     * @param integer $val            
     * @return \qpf\htmc\Select
     */
    public function tabIndex($val)
    {
        $this->tabIndex = $val;
        return $this;
    }

    /**
     * 是否禁用元素
     * 
     * @param boolean $val
     *            默认true禁用
     * @return \qpf\htmc\Select
     */
    public function disabled($val = true)
    {
        $this->disabled = $val;
        return $this;
    }

    /**
     * 设置元素自动获得焦点
     * 
     * @param boolean $val
     *            默认true获得焦点
     * @return \qpf\htmc\Select
     */
    public function autoFocus($val = true)
    {
        $this->autofocus = $val;
        return $this;
    }

    /**
     * 设置元素启用必填验证
     * 
     * @param boolean $val
     *            默认true启用验证
     * @return \qpf\htmc\Select
     */
    public function required($val = true)
    {
        $this->required = $val;
        return $this;
    }

    /**
     * 设置下拉列表可多选
     * 
     * @param string $val            
     * @return \qpf\htmc\Select
     */
    public function multiple($val = true)
    {
        $this->multiple = $val;
        return $this;
    }

    /**
     * 解析name属性
     * 
     * @return string
     */
    private function parseName()
    {
        if ($this->multiple === null) {
            return ' name="' . $this->name . '"';
        } elseif ($this->multiple == true) {
            return ' name="' . $this->name . '[]"';
        }
    }

    /**
     * 解析tabindex属性
     * 
     * @param integer $val
     *            tab键切换序列值
     */
    private function parseTabindex($val)
    {
        if ($val !== null) {
            return ' tabindex="' . $val . '"';
        }
    }

    /**
     * 解析required属性
     * 
     * @param boolean $val
     *            元素是否使用HTML5必填验证
     */
    private function parseRequired($val)
    {
        if ($val !== null) {
            return $val ? ' required' : '';
        }
    }

    /**
     * 解析autofocus属性
     * 
     * @param boolean $val
     *            是否自动获得焦点
     */
    private function parseAutofocus($val)
    {
        if ($val !== null) {
            return $val ? ' autofocus' : '';
        }
    }

    /**
     * 解析disabled属性
     * 
     * @param boolean $val
     *            是否禁用元素,禁用的元素不会提交
     */
    private function parseDisabled($val)
    {
        if ($val !== null) {
            return $val ? ' disabled' : '';
        }
    }

    /**
     * 解析Form属性
     * 
     * @param string $val
     *            元素所属form表单
     */
    private function parseForm($val)
    {
        if ($val !== null) {
            return ' form="' . $val . '"';
        }
    }

    /**
     * 解析Class属性
     * 
     * @param string $val
     *            多个用空格分开
     */
    private function parseClass($val)
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
    private function parseTitle($val)
    {
        if ($val !== null) {
            return ' title="' . $val . '"';
        }
    }

    /**
     * 解析Size属性
     * 
     * @param integer $val
     *            下拉显示个数
     * @return string
     */
    private function parseSize($val)
    {
        if ($val !== null) {
            return ' size="' . $val . '"';
        }
    }

    /**
     * 解析multiple属性
     * 
     * @param boolean $val            
     * @return string
     */
    private function parseMultiple($val)
    {
        if ($val !== null) {
            return $val ? ' multiple' : '';
        }
    }
}
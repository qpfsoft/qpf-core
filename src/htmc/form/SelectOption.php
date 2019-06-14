<?php
namespace qpf\htmc;

use qpf\core\Config;

/**
 * 下拉选项对象
 *
 * SelectOption 对象表示一个下拉选项对象，并且支持批量添加生成下拉选项的html。
 *
 * ~~~php
 * $s = new SelectOption();
 * # 设置选项属性、选中显示并生成html
 * $s->key(1)->value('选项描述')->selected()->gethtml();
 * # 等价于
 * $s->add(1, '选项描述')->selected()->gethtml()
 *
 * # 批量创建3个下拉选项，并选择2选项显示
 * $s->addOption('1', '测试1', false);
 * $s->addOption('2', '测试2', true);
 * $s->addOption('3', '测试3');
 * $s->getHtmls(); // 返回html
 *
 *
 * ~~~
 * 
 * @author qiun
 *        
 */
class SelectOption
{

    /**
     * 元素id
     * 
     * @var string
     */
    public $id;

    /**
     * 内镶样式
     * 
     * @var string
     */
    public $style;

    /**
     * 选项值
     * 
     * @var string
     */
    public $key = '';

    /**
     * 选项描述
     * 
     * @var string
     */
    public $value = '';

    /**
     * 是否选中选项
     * 
     * @var boolean
     */
    public $selected;

    /**
     * 要被选中的下拉选项的key值
     *
     * 该属性用于批量处理时，来标记
     * 
     * @var string
     */
    public $selectKey;

    /**
     * 保存下来列表对象
     * 
     * @var array
     */
    private $option = [];

    /**
     * 设置下拉选项值
     * 
     * @param string $val            
     * @return $this
     */
    public function key($val)
    {
        $this->key = $val;
        return $this;
    }

    /**
     * 设置下拉选项的描述
     * 
     * @param string $val            
     * @return $this
     */
    public function value($val)
    {
        $this->value = $val;
        return $this;
    }

    /**
     * 添加下拉选项
     * 
     * @param string $key
     *            选项值
     * @param string $value
     *            选项描述
     * @return $this
     */
    public function add($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
        
        return $this;
    }

    /**
     * 解析selected属性
     * 
     * @param boolean $val            
     * @return string
     */
    public function parseSelected()
    {
        return $this->selected ? ' selected' : '';
    }

    /**
     * 设置选项为选中状态
     * 
     * @param boolean $val
     *            默认true选择
     */
    public function selected($val = true)
    {
        $this->selected = $val;
        return $this;
    }

    /**
     * 获得当前一条的选项HTML
     *
     * @return string
     */
    public function gethtml()
    {
        $htm = '<option value="' . $this->key . '"' . $this->parseSelected() . '>' . $this->value . '</option>';
        
        return $htm;
    }

    /**
     * 设置要被选中的下拉选项的key值
     * 
     * @param string $key            
     * @return $this
     */
    public function selectKey($key)
    {
        $this->selectKey = $key;
        return $this;
    }

    /**
     * 添加下拉选项
     * 
     * @param string $key
     *            选项值
     * @param string $value
     *            选项描述
     * @param boolean $selected
     *            是否选中显示，默认false.
     * @return \qpf\htmc\SelectOption
     */
    public function addOption($key, $value, $selected = false)
    {
        $this->option[] = (new self())->add($key, $value)->selected($selected);
        
        return $this;
    }

    /**
     * 导入数组为下拉选项列表
     *
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
     * 不管是哪种类型，都会被转换为对象配置格式进行使用。
     *
     * @param array $array
     *            选项配置数组
     * @return $this
     */
    public function setOptions($array = [])
    {
        if (empty($array) && ! is_array($array)) {
            return $this;
        }
        
        foreach ($array as $i => $arr) {
            
            if (is_array($arr)) {
                if (! isset($arr['value'])) {
                    $arr['value'] = current($arr);
                }
                if (! isset($arr['key'])) {
                    $arr['key'] = array_search($arr['value'], $arr);
                }
                unset($arr[$arr['key']]);
                
                if (! isset($arr['selected']) && isset($arr[0])) {
                    $arr['selected'] = $arr[0];
                }
                unset($arr[0]);
            } elseif (is_string($arr)) {
                
                // true为功能属性，不进行处理
                if ($i === 'true') {
                    continue;
                }
                // 判断当前选项是否被true选中
                if (isset($array['true'])) {
                    $select = $array['true'] === $i ? true : null;
                }
                $arr = [
                    'key' => $i,
                    'value' => $arr,
                    'selected' => $select
                ];
            }
            
            $this->option[] = $this->createOption($arr);
        }
        
        return $this;
    }

    /**
     * 根据配置创建option对象
     *
     * @param array $config
     *            对象创建配置
     * @return \qpf\htmc\SelectOption
     */
    private function createOption($config)
    {
        $object = new self();
        
        Config::setObject($object, $config);
        
        return $object;
    }

    /**
     * 批量解析生成下拉选项
     *
     * @return string
     */
    public function getHtmls($lineTag = '')
    {
        $htm = [];
        
        /** @var $option \qpf\htmc\SelectOption **/
        
        foreach ($this->option as $i => $option) {
            $htm[] = $option->gethtml();
        }
        
        return implode($lineTag, $htm);
    }
}
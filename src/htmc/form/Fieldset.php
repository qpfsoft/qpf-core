<?php
namespace qpf\htmc\form;

/**
 * Fieldset 标签是对表单中的信息进行分组归类，一个form表单中可有多个分组，
 * 每个分组可设置一个标签标题
 *
 * ~~~php
 *
 * # 创建多个分组
 * $form->fieldset()->tagStart('账号信息');
 * $form->fieldset()->tagEnd();
 * $form->fieldset()->tagStart('个人信息');
 * $form->fieldset()->tagEnd();
 * 接收返回的html即可
 * ~~~
 *
 * 犹豫本来采用重复使用，建议不保存对象属性，直接赋值生成html即可。
 * 
 * @author qiun
 *        
 */
class Fieldset
{

    /**
     * 组的内容描述标题
     * 
     * @var string
     */
    private $legend;

    /**
     * 是否禁用组内所有元素
     * 
     * @var boolean
     */
    private $disabled;

    /**
     * 所属表单
     * 
     * @var string
     */
    private $form;

    /**
     * 指定名称
     * 
     * @var string
     */
    private $name;

    /**
     * 设置分组Name属性
     * 
     * @param string $val            
     * @return \qpf\htmc\Fieldset
     */
    public function name($val)
    {
        $this->name = $val;
        return $this;
    }

    /**
     * 解析name属性
     * 
     * @param string $val            
     * @return string
     */
    private function parseName($val = null)
    {
        $val = $val === null ? $this->name : $val;
        if ($val !== null) {
            return ' name="' . $val . '"';
        }
    }

    /**
     * 设置分组所属表单
     * 
     * @param string $val
     *            表单ID
     * @return \qpf\htmc\Fieldset
     */
    public function form($val)
    {
        $this->form = $val;
        return $this;
    }

    /**
     * 解析form属性
     * 
     * @param string $val            
     * @return string
     */
    private function parseForm($val = null)
    {
        $val = $val === null ? $this->form : $val;
        if ($val !== null) {
            return ' form="' . $val . '"';
        }
    }

    /**
     * 是否禁用分组中所有元素
     * 
     * @param boolean $val
     *            默认true禁用
     * @return \qpf\htmc\Fieldset
     */
    public function disabled($val = true)
    {
        $this->disabled = $val;
        return $this;
    }

    /**
     * 解析disabled属性
     * 
     * @param boolean $val
     *            覆盖对象属性
     * @return string
     */
    private function parseDisabled($val = null)
    {
        $val = $val === null ? $this->name : $val;
        if ($val !== null) {
            return $val ? ' disabled' : '';
        }
    }

    /**
     * 设置分组标题
     * 
     * @param string $legend            
     * @return $this
     */
    public function title($legend = null)
    {
        $this->legend = $legend;
        
        return $this;
    }

    /**
     * 设置分组标题
     * 
     * @param string $legend            
     * @return $this
     */
    public function legend($legend = null)
    {
        $this->legend = $legend;
        
        return $this;
    }

    /**
     * 解析legend属性
     *
     * @param string $legend
     *            覆盖对象属性
     * @return string 返回分组描述标题html
     */
    private function parseLegend($legend = null)
    {
        $legend = ($legend === null ? $this->legend : $legend);
        
        if ($legend === null) {
            return '';
        }
        
        return '<legend>' . $legend . '</legend>';
    }

    /**
     * 表单分组开始标签
     *
     * @param string $legend
     *            分组标题
     * @param array $option
     *            标签其他属性配置
     * @return string
     */
    public function tagStart($title = null, $option = [])
    {
        return '<fieldset' . $this->parseDisabled(isset($option['disabled']) ? $option['disabled'] : null) . $this->parseForm(isset($option['form']) ? $option['form'] : null) . $this->parseName(isset($option['name']) ? $option['name'] : null) . '>' . $this->parseLegend($title);
    }

    /**
     * 表单分组结束标签
     * 
     * @return string
     */
    public function tagEnd()
    {
        return '</fieldset>';
    }
}
<?php
namespace qpf\htmc\input;

/**
 * Input元素`checkbox`多选类型
 * 
 * 定义复选框。
 * @author qiun
 *        
 */
class CheckBox extends InputBase
{
    /**
     * 规定input元素的类型
     * @var string
     */
    protected $inputType = 'checkbox';
    
    /**
     * 规定input元素的名称, 提交后的变量名
     *
     * - 只有设置了name属性的表单元素才会被提交
     * - 复选框重写,自动为name值添加`[]`方便接收多选值
     * @param string $value
     * @return $this
     */
    public function name($value)
    {
        return parent::name($value . '[]');
    }
    
    /**
     * 页面加载时被预先选中
     * @return $this
     */
    public function checked()
    {
        $this->attr(['checked' => 'checked']);
        return $this;
    }
    
    /**
     * 选项关联的参数值
     *
     * @param string $value
     * @return $this
     */
    public function value($value)
    {
        $this->attr(['value' => $value]);
        return $this;
    }
}
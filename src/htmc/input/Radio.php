<?php
namespace qpf\htmc\input;

/**
 * Input元素`radio`单选类型
 * 
 * 定义单选按钮.
 * @author qiun
 *        
 */
class Radio extends InputBase
{
    /**
     * 规定input元素的类型
     * @var string
     */
    protected $inputType = 'radio';

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
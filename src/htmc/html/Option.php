<?php
namespace qpf\htmc\html;

/**
 * Option 选项标签
 * 
 * 该类代表下拉选项中的一个选项标签, 是select的内部标签.
 * 
 * - optgroup 分组标签
 * @author qiun
 *
 */
class Option extends HtmlBase
{
    /**
     * 标签名称
     * @var string
     */
    protected $tagName = 'option';
    
    /**
     * 设置选项提交到服务器的值
     * @param string $value
     * @return $this
     */
    public function value($value)
    {
        $this->attr(['value' => $value]);
        return $this;
    }
    
    /**
     * 设置预先选定该选项
     * @return $this
     */
    public function selected()
    {
        $this->attr(['selected' => 'selected']);
        return $this;
    }
    
    /**
     * 设置更短版本的选项
     * @param string $value
     * @return $this
     */
    public function label($value)
    {
        $this->attr(['label' => $value]);
        return $this;
    }
    
    /**
     * 禁用当前选项值
     * @return $this
     */
    public function disabled()
    {
        $this->attr(['disabled' => 'disabled']);
        return $this;
    }
}
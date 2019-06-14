<?php
namespace qpf\htmc\html;

/**
 * Select 选项列表
 * @author qiun
 *
 */
class Select extends HtmlBase
{
    /**
     * 标签名称
     * @var string
     */
    protected $tagName = 'select';
    
    public function getHtml()
    {
        
    }
    
    
    // -----------------标签方法-----------------------------
    
    /**
     * 选项集合
     * @var array
     */
    protected $_option = [];
    
    /**
     * 快速添加1个选项
     * @param string $value 选项值
     * @param string $text 选项描述
     * @param boolean $selected 是否选中
     */
    public function add($value, $text, $selected = false)
    {
        $this->_option[$value] = [$value, $text, $selected];
        return $this;
    }
    
    
    // ----------------标签属性-----------------------
    
    /**
     * 自动获得焦点 - h5
     * @return $this
     */
    public function autofocus()
    {
        $this->attr(['accesskey' => 'accesskey']);
        return $this;
    }
    
    /**
     * 引用下拉列表 - h5
     * @return $this
     */
    public function disabled()
    {
        $this->attr(['disabled' => 'disabled']);
        return $this;
    }
    
    /**
     * 设置下拉列表所属表单 - h5
     * @param string $value 表单ID
     * @return $this
     */
    public function form($value)
    {
        $this->attr(['form' => $value]);
        return $this;
    }
    
    /**
     * 设置可选择多个选项
     * 
     * - windows : 按`ctrl`来多选
     * - mac : 按commmand来多选
     * - 配合size属性使用, 设置可见选项数量
     * 
     * @return $this
     */
    public function multiple()
    {
        $this->attr(['multiple' => 'multiple']);
        return $this;
    }
    
    /**
     * 设置下拉列表提交的变量名
     * @param string $value 变量名
     * @return $this
     */
    public function name($value)
    {
        $this->attr(['name' => $value]);
        return $this;
    }
    
    /**
     * 设置必填项 - h5
     * @return $this
     */
    public function required()
    {
        $this->attr(['required' => 'required']);
        return $this;
    }
    
    /**
     * 设置下拉列表可见项目数
     * @param string $value 整数
     * @return $this
     */
    public function size($value)
    {
        $this->attr(['size' => $value]);
        return $this;
    }
}
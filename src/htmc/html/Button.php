<?php
namespace qpf\htmc\html;

/**
 * Button 按钮标签
 * 
 * 注意: 
 * - 普通装饰按钮可用.
 * 表单按钮问题:
 * - IE 会提交<button></button>之间的内容.
 * - 其他只会提交`value`的属性值.
 * - 表单提交还是使用input元素按钮.
 * 
 * type属性问题:
 * - IE默认类型是 "button"
 * - w3c和其他游览器: 默认值是 "submit"
 * 
 * 用法:
 * - 与input type="button"相比 button标签中的内容, 比如文本或图像, 都是按钮的内容.
 * 
 * 
 * @author qiun
 *
 */
class Button extends HtmlBase
{
    /**
     * 标签名称
     * @var string
     */
    protected $tagName = 'button';
    
    /**
     * 按钮的类型
     * @param string $value 可能的值:
     * - button : 普通按钮
     * - reset : 重设表单
     * - submit : 提交表单
     * @return $this
     */
    public function type($value = 'button')
    {
        $this->attr(['type' => $value]);
        return $this;
    }
    
    /**
     * 按钮的名称
     * @param string $value 表单提交时按钮的变量名
     * @return $this
     */
    public function name($value)
    {
        $this->attr(['name' => $value]);
        return $this;
    }
    
    /**
     * 按钮的初始值
     *
     * - 按钮提交的初始值, IE只会提交标签之间的内容.
     * @param string $value
     * @return $this
     */
    public function value($value)
    {
        $this->attr(['value' => $value]);
        return $this;
    }
    
    /**
     * 页面加载时自动获得焦点 - h5
     *
     * - 不适用于 type="hidden"
     * @return $this
     */
    public function autofocus()
    {
        $this->attr(['autofocus' => 'autofocus']);
        return $this;
    }
    
    /**
     * 禁用input元素
     *
     * - 不适用于 type="hidden"
     * @return $this
     */
    public function disabled()
    {
        $this->attr(['disabled' => 'disabled']);
        return $this;
    }
    
}
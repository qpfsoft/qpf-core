<?php
namespace qpf\htmc\html;

/**
 * HTML标签 基础类
 * 
 * 
 * - 适用于无复杂嵌套的标签, 继承使用
 * @author qiun
 *
 */
class Tag
{
    /**
     * 标签名称
     * @var string
     */
    protected $tagName;
    /**
     * 标签属性集合
     * @var array
     */
    protected $attr = [];
    /**
     * 标签内容
     * @var string
     */
    protected $content;
    
    /**
     * 构造函数
     * @param string $name
     */
    public function __construct($name = null)
    {
        if (!is_null($name)) {
            $this->tagName = $name;
        }
    }
    
    /**
     * 标签内容
     * @param string $val
     * @return \qpf\htmc\html\Tag
     */
    public function content($val)
    {
        $this->content = $val;
        return $this;
    }
    
    /**
     * 添加属性
     * @param array|string $value 参数格式:
     * - string : 'id="attr1" name="attr2"' 字符串会被原样拼接
     * - array : ['id' => 'attr1', 'check'] => 转换为 `'id="attr1" check'`字符串
     */
    public function attr($value)
    {
        if (is_string($value)) {
            $this->attr[] = $value;
        } elseif (is_array($value)) {
            $this->attr = $value;
        }
        return $this;
    }
    
    /**
     * 解析属性集合转换为字符串
     * @return string
     */
    private function parseAttr()
    {
        if (empty($this->attr)) return '';

        $attr = '';
        foreach ($this->attr as $i => $val) {
            if (is_numeric($i)) {
                $attr .= ' ' . $val;
            } else {
                $attr .= ' ' . $i . '=' . '"'. $val .'"';
            }
        }
        
        return ' ' . trim($attr, ' ');
    }
    
    /**
     * 生成标签html代码
     * 
     * @param boolean $closure 是否有对称闭合标签, 默认`true`
     * @return string
     */
    public function getHtml($closure = true)
    {
        $str = '<' . $this->tagName . $this->parseAttr() . '>';
        
        if ($closure) {
            $str .= $this->content .'</' . $this->tagName . '>';
        }
        return $str;
    }
    
    /**
     * 自动生成代码
     * @return string
     */
    public function __toString()
    {
        return $this->getHtml();
    }
}
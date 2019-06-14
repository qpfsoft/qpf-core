<?php
namespace qpf\htmc\html;

/**
 * 标签自由属性基础类
 * 
 * 该类让标签可自由添加属性.
 * @author qiun
 *
 */
class TagAttr
{
    /**
     * 标签属性集合
     * @var array
     */
    private $_tagAttr = [];
    
    /**
     * 添加属性
     * @param strig|array $value 可能的值:
     * - string : `id="div-demo"` 属性字符串
     * - array : `['id'=>'div-demo', 'check']` 数组可省略等号和引号, 关键字属性无需键名.
     * @return $this 返回当前对象
     */
    public function attr($value)
    {
        if (is_string($value)) {
            $this->_tagAttr[] = $value;
        } elseif (is_array($value)) {
            $this->_tagAttr = array_merge($this->_tagAttr, $value);
        }
        return $this;
    }

    /**
     * 解析属性集合转换为字符串 
     * - 前面自带空格
     * @return string
     */
    protected function parseAttr()
    {
        if (empty($this->_tagAttr)) return '';
        
        $attr = '';
        foreach ($this->_tagAttr as $i => $val) {
            if (is_numeric($i)) {
                $attr .= ' ' . $val;
            } else {
                $attr .= ' ' . $i . '=' . '"'. $val .'"';
            }
        }
        
        return ' ' . trim($attr, ' ');
    }
    
    /**
     * 清空自定义属性集合
     */
    protected function resetAttr()
    {
        $this->_tagAttr = [];
    }
     
    /**
     * 返回一个新对象
     *
     * @return $this
     */
    static public function this()
    {
        return new static();
    }
}
<?php
namespace qpf\htmc\base;

/**
 * 元素生成器
 */
class ElementBuilder
{
    const EOL = "\n";
    const BR = '<br />;';
    
    /**
     * 生成元素
     * @param Element $element
     * @return string
     */
    public function build($element = null)
    {
        $e = is_null($element) ? $this : $element;
        
        return $this->buildTag($e->getName(), $e->getAttrs(), $e->getCountents() ,$e->isEnd());
    }
    
    /**
     * 生成html标签
     * @param string $name 标签名
     * @param array $attr 属性
     * @param string|array $content 内容
     * @param bool $end 是否需要闭合标签, 默认`true`
     * @return string
     */
    private function buildTag($name, $attr, $content = '', $end = true)
    {
        $htm = '<' . $name;
        
        if (!empty($attr)) {
            if(is_array($attr)) {
                $htm .= ' ' . $this->parseAttr($attr);
            } else {
                $htm .= ' ' . $attr;
            }
        }
        
        
        if($end) {
            if(is_array($content)) {
                $contxt = '';
                // 内容行大于1时, 启用换行.
                $eol = count($content) > 1 ? self::EOL : '';
                
                if (!empty($content)) {
                    foreach ($content as $i => $line) {
                        if($line instanceof Element) {
                            $contxt .= $line->build() . $eol;
                        } else {
                            $contxt .= $line . $eol;
                        }
                    }
                }
                
                $htm .= '>' . $eol . $contxt;
            } elseif(!empty($content)) {
                // 内容包含<标签时, 启用换行
                $eol = strpos($content, '<') !== false ? self::EOL : '';
                $htm .= '>' . $eol . $content;
            } else {
                $htm .= '>';
            }
            
            $htm .= '</' . $name . '>';
        } else {
            $htm .= '/>';
        }
        
        return $htm;
    }
    
    
    /**
     * 生成元素属性部分
     * @param array $attr
     */
    private function parseAttr(array $attr)
    {
        $result = [];
        foreach ($attr as $name => $value) {
            // 空属性
            if($value === '') {
                $result[] = $name . '=""';
            } elseif ($value === true || $value === false) {
                // 布尔属性
                if($value) {
                    $result[] = $name;
                }
            } else {
                $result[] = $name . '=' . $this->quote($value, false);
            }
        }
        
        return implode(' ', $result);
    }
    
    /**
     * 双引号
     * @param string $value
     * @return string
     */
    private function quoteDouble($value)
    {
        return '"' . $value .  '"';
    }
    
    /**
     * 单引号
     * @param string $value
     * @return string
     */
    private function quoteSingle($value)
    {
        return '\'' . $value .  '\'';
    }
    
    /**
     * 添加引号
     * @param string $value 属性值
     * @param bool $single 是否单引号包裹, 默认`false`代表采用双引号包裹属性值.
     * - true : 使用单引号包裹属性值, 属性值中的单引号自动进行转义
     * - false : 使用双引号包裹属性值, 属性值中的双引号自动进行转义
     */
    private function quote($value, $single = false)
    {
        $result = '';
        
        if($single) {
            // 包含单引号
            if(strpos($value, '\'') !== false) {
                $result = $this->quoteSingle(addslashes($value));
            } else {
                $result = $this->quoteSingle($value);
            }
        } else {
            
            // 包含双引号
            if(strpos($value, '"') !== false) {
                $result = $this->quoteDouble(addslashes($value));
            } else {
                $result = $this->quoteDouble($value);
            }
        }
        
        return $result;
    }

}
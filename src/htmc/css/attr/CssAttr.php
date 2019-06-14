<?php
namespace qpf\htmc\css\attr;

/**
 * css 属性基类
 * 
 * @author qiun
 *        
 */
class CssAttr
{

    /**
     * 空格间隙左
     * 
     * @var string
     */
    const spaceL = '';

    /**
     * 空格间隙右
     * 
     * @var string
     */
    const spaceR = ' ';

    /**
     * 结束符
     * 
     * @var string
     */
    const end = ';';

    /**
     * css属性与值的分割方法
     * - 该方法用于简写冒号`:`部分.
     * 
     * @return string 返回空格+冒号+空格, 是否空格根据设置判定.
     */
    static protected function space()
    {
        return self::spaceL . ':' . self::spaceR;
    }

    /**
     * 解析上下左右的参数设置为字符串
     * 
     * @param string $top 上
     * @param string $right 右
     * @param string $bottom 下
     * @param string $left 左
     * @return string 返回合并或简写的上下左右设置字符串
     */
    protected function parseTRBL($top, $right, $bottom, $left)
    {
        $trbl = '';
        // 规则一: 上下相同, 左右相同
        if ($top == $bottom && $right == $left) {
            // 规则二: 上下左右的值相同
            if ($top == $right) {
                $trbl = $top;
            } else {
                // 规则三: 确定只有上下相同, 左右相同
                $trbl = $top . ' ' . $right;
            }
        } else {
            // 规则四: 四个位置的值不同, 并且不能合并简写
            $trbl = $top . ' ' . $right . ' ' . $bottom . ' ' . $left;
        }
        
        return $trbl;
    }

    /**
     * 解析CSS中远程资源为url(``)格式字符串
     * 
     * @param string $value
     *            资源路径
     * @return string
     */
    protected function parseUrl($value)
    {
        return 'url(' . $value . ')';
    }
    
    /**
     * css属性前缀 - 追加多个兼容前缀给css属性名或属性值
     * 
     * - -moz- : 火狐
     * - -ms- ： 微软
     * - -o- ： open
     * - -webkit- ： 谷歌，苹果，安卓
     * 
     * ~~~实例
     * # 生成3条css属性
     * $this->cssPrefix(
     *      'column-gap' . self::space() . $value . self::end,
     *      ['-moz-', '-webkit-']);
     * # 原样返回
     * $this->cssPrefix('column-gap' . self::space() . $value . self::end);
     * ~~~
     * 
     * @param string $css css字符串
     * @param array $prefixArray 要追加的前缀集合,
     * 如果值为`null`将原样返回
     * @param boolean $setVal 前缀附加给css属性的值，默认false，即附加给css属性名
     * @return string 返回多条带前缀的css，尾部是原始css
     */
    protected function cssPrefix($css, $prefixArray = null, $setVal = false)
    {
        if (is_null($prefixArray) || !is_array($prefixArray)) {
            return $css;
        }
        $return = '';
        
        // 为css属性值追加兼容前缀
        if ($setVal) {
            list($cssname, $cssval) = explode(self::space(), $css, 2);
            foreach ($prefixArray as $i => $prefix) {
                $return .= $cssname . self::space() . $prefix . $cssval;
            }
        } else {
            // 为css属性名追加兼容前缀
            foreach ($prefixArray as $i => $prefix) {
                $return .= $prefix . $css;
            }
        }
        
        // 尾部追加原始css
        return $return . $css;
    }
    
    /**
     * 用空格合并多个css属性参数
     * @param string $attr 可接收多个参数
     * @return string
     */
    protected function mergeAttr($attr)
    {
        return implode(' ', array_filter(func_get_args()));
    }
}
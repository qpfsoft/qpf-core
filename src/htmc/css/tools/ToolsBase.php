<?php
namespace qpf\htmc\css\tools;

use qpf\htmc\css\CssBuilder;
use qpf\htmc\Htmc;

/**
 * css工具类基础
 * 
 * 推荐使用方式:
 * - 子类通过{$this->css()->?}的方式来调用css属性集合
 * - 子类可通过{$this->createClass('.demo', $this->css()->?);}的方式来创建样式类.
 * 
 * 
 * @author qiun
 *
 */
class ToolsBase
{
    /**
     * css属性生成器
     * @var \qpf\htmc\css\CssBuilder
     */
    private $cssBuilder;
    
    /**
     * css属性
     * @return \qpf\htmc\css\CssBuilder
     */
    protected function css()
    {
        if (is_null($this->cssBuilder)) {
            $this->cssBuilder = new CssBuilder();
        }
        return $this->cssBuilder;
    }
    
    /**
     * htmc对象
     * @var \qpf\htmc\Htmc
     */
    private $htmc;
    
    /**
     * htmc对象
     * @return \qpf\htmc\Htmc
     */
    protected function htmc()
    {
        if (is_null($this->htmc)) {
            $this->htmc = new Htmc();
        }
        return $this->htmc;
    }
    
    /**
     * 创建class格式css
     * @param string $name css样式字符串, 多个参数时第一参数作为class名称
     * @return string
     */
    protected function createClass($name)
    {
        $arr = func_get_args();
        $string = '';
        
        // 判断1: 如果参数超过1个,第一个参数作为class名称
        if (func_num_args() > 1) {
            $string .= $arr[0] . '{' . PHP_EOL;
            unset($arr[0]);
        }
        
        // 从第二个参数开始后面的都认定为css属性
        foreach ($arr as $i => $val) {
            $string .= $val . PHP_EOL;
        }
        
        $string .= '}' . PHP_EOL;
        
        return $string;
    }
}
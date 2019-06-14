<?php
namespace qpf\htmc\input;

use qpf\htmc\EventBuild;

/**
 * input元素对象生成基类
 *
 *
 * 让继承的元素支持以下两种生成HTML方式：
 *
 * # 创建文本框输入框，不熟悉属性可通过对象直接来生成
 * Text::this()->name('pwd')->getHtml();
 * # 创建文本框，静态方法直接根据配置生成
 * Text::this(['name'=>'email']);
 * 
 * 其他:
 * - 标签公共属性 和属性设置方法
 * 
 * @author qiun
 *        
 */
class InputBuild extends EventBuild
{
    /**
     * 设置标签ID属性
     * 
     * @param string $val            
     * @return $this
     */
    public function id($val)
    {
        $this->attr(['id' => $val]);
        return $this;
    }
    
    /**
     * 获取HTML代码
     * 
     * @return string
     */
    public function getHtml()
    {
        return (new CreateInput())->getHtml($this);
    }
}
<?php
namespace qpf\htmc\base;

/**
 * Script 元素
 *
 */
class Script extends Element
{
    /**
     * 异步执行脚本
     * @param string $value
     * @return $this
     */
    public function async($value = true)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 声明脚本的字符编码 - 不推荐使用的属性
     * @param string $value 其值必须是`utf-8`
     * @return $this
     */
    public function charset($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 元素如何处理跨源请求
     * @param string $value 可能的值:
     * - `anonymous` : 匿名
     * - `use-credentials` : 使用的凭据
     * @return $this
     */
    public function crossorigin($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 表示在解析页面后应执行脚本
     * @param bool $value
     * @return $this
     */
    public function defer($value = true)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 指示资源的相对提取优先级
     * @param string $value 可能的值:
     * - `auto` : 表示没有首选项。浏览器可以使用其自己的启发式来确定资源的优先级
     * - `high` : 向浏览器指示资源具有高优先级
     * - `low` : 向浏览器指示资源具有低优先级
     * @return $this
     */
    public function importance($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 安全功能，允许浏览器验证他们获取的内容
     * @param string $value
     * @return $this
     */
    public function integrity($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 定义元素中使用的脚本语言
     * @param string $value
     * @return $this
     */
    public function language($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 元素的宽度, 以像素为单位
     * @param string $value
     * @return $this
     */
    public function size($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 可嵌入内容的URL
     * @param string $value
     * @return $this
     */
    public function src($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
}
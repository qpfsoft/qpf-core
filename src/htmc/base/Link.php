<?php
namespace qpf\htmc\base;

/**
 * Link 元素
 */
class Link extends Element
{
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
     * 链接资源的URL
     * @param string $value
     * @return $this
     */
    public function href($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 指定链接资源的语言
     * @param string $value
     * @return $this
     */
    public function hreflang($value)
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
     * 指定目标对象与链接对象的关系
     * @param string $value
     * @return $this
     */
    public function rel($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 定义资源中包含的可视媒体的图标大小
     * @param string $value
     * @return $this
     */
    public function sizes($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
}
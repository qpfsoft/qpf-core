<?php
namespace qpf\htmc\base;

/**
 * Img 图片元素
 */
class Img extends Element
{
    /**
     * 图片的替代文本
     * @param string $value 代替文字
     * @return $this
     */
    public function alt($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 边框宽度 - 遗留属性, 用css代替
     * @param string $value 宽度
     * @return $this
     */
    public function border($value)
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
     * 解码图像的首选方法
     * @param string $value 可能的值:
     * - `sync` : 同步解码图像以便与其他内容进行原子演示
     * - `async` : 异步解码图像以减少呈现其他内容的延迟
     * - `auto` : 默认模式，表示没有解码模式的偏好。浏览器决定什么对用户最有利。
     * @return $this
     */
    public function decoding($value)
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
     * 表示图像是服务器端图像映射的一部分
     * @param bool $value
     * @return $this
     */
    public function ismap($value = true)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 指示是否应该懒惰地加载元素
     * @param string $value
     * @return $this
     */
    public function lazyload($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 允许的最小值
     * @param string $value
     * @return $this
     */
    public function min($value)
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
    
    /**
     * 一个或多个响应图像候选
     * @param string $value
     * @return $this
     */
    public function srcset($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 元素的宽度
     * @param string $value
     * @return $this
     */
    public function width($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
}
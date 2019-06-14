<?php
namespace qpf\htmc\base;

/**
 * Area 图片上的热点
 */
class A extends Element
{
    protected $events = ['onclick'];
    
    /**
     * 超链接用于下载资源
     * @param string $value 资源地址
     * @return $this
     */
    public function download($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 链接资源的URL
     * @param string $value URL地址
     * - 邮件地址格式: `mailto:nowhere@mozilla.org` 
     * - 电话格式: `tel:+491570156`
     * - 防止刷新: `javascript:void(0)` 或 `#`
     * @return $this
     */
    public function href($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 指定链接资源的语言 - 纯粹是建议性
     * @param string $value 允许值由BCP47确定
     * @return $this
     */
    public function hreflang($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
 
    /**
     * 包含以空格分隔的URL列表
     * 
     * - 当遵循超链接时，浏览器将（在后台）发送POST与正文的请求PING。通常用于跟踪
     * @param string $value
     * @return $this
     */
    public function ping($value)
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
     * 显示链接URL的位置
     * @param string $value 可能的值:
     * - `_self` : 默认, 当前页面
     * - `_blank` : 新页面, 推荐
     * - `_parent` : 父页面
     * - `_top` : 顶级窗口
     * @return $this
     */
    public function target($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
}
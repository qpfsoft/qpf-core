<?php
namespace qpf\htmc\base;

/**
 * Video 视频元素
 */
class Video extends Element
{
    /**
     * 开启自动播放
     * @return $this
     */
    public function autoplay($value = true)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 包含已缓冲媒体的时间范围
     * - yes : Edge, Firefox(4), Opera
     * @param string $value
     * @return $this
     */
    public function buffered($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 浏览器是否应向用户显示播放控件
     * @param bool $value
     * @return $this
     */
    public function controls($value = true)
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
     * 指示媒体是否应在完成时从头开始播放
     * @param bool $value
     * @return $this
     */
    public function loop($value = true)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 指示音频在页面加载时是否最初静音
     * @param bool $value
     * @return $this
     */
    public function muted($value = true)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 指示在用户播放或搜索之前显示的海报框架的URL
     * @param string $value
     * @return $this
     */
    public function poster($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 指示是否应预先加载整个资源，部分资源或任何内容
     * @param string $value
     * @return $this
     */
    public function preload($value)
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
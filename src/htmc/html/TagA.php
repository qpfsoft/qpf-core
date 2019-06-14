<?php
namespace qpf\htmc\html;

/**
 * A标签
 * @author qiun
 *
 */
class TagA extends Tag
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->tagName = 'a';
    }
    
    /**
     * 设置链接
     * @param string $url 默认值`Javascript::void(0)`
     */
    public function url($url = 'Javascript::void(0)')
    {
        $this->attr['href'] = $url;
        return $this;
    }
    
    /**
     * 设置链接地址
     * @param string $href 默认`#`
     */
    public function href($href = '#')
    {
        $this->attr['href'] = $href;
        return $this;
    }
}
<?php
namespace qpf\htmc\html;

/**
 * Abbr 简写标签
 * 
 * - 通过title设置全称, 鼠标经过事显示.
 * 
 * ~~~示例
 * <abbr title="QIUN PHP Frame">QPF</abbr> 是一款框架
 * ~~~
 * 
 * @author qiun
 *
 */
class Abbr extends HtmlBase
{
    /**
     * 标签名称
     * @var string
     */
    protected $tagName = 'abbr';
    
    /**
     * 设置abbr标签简写的完整字符串
     * @param string $value
     * @return $this
     */
    public function title($value)
    {
        $this->attr(['title' => $value]);
        return $this;
    }
}
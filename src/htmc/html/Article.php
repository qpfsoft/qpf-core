<?php
namespace qpf\htmc\html;

/**
 * Article 标签规定独立的自包含内容 - h5
 * 
 * - Internet Explorer 8 以及更早的版本不支持 article 标签。
 * 
 * -  article元素代表文档、页面或应用程序中独立的、完整的、可以独自被外部引用的内容。
 * 它可以是一篇博客或报刊中的文章、一篇论坛帖子、一段用户评论或独立的插件，或其他任何独立的内容。
 * 除了内容部分，一个article元素通常有它自己的标题（一般放在一个header元素里面），有时还有自己的脚注。
 * 
 * ~~~示例: 代表一段内容的包裹, 方便外部引用
 * <article>
 * <header>
 *  <h1>标题</h1>
 *  <p>发表日期：<time pubdate="pubdate">2011年7月10号</time></p>
 * </header>
 * <footer>
 *    <p>w3cmm 版权所有</p>
 * </footer>
 * </article>
 * ~~~
 * article元素是可以嵌套使用的，内层的内容在原则上需要与外层的内容相关联。
 * 例如，一篇博客文章中，针对该文章的评论就可以使用嵌套article元素的方式；
 * 用来呈现评论的article元素被包含在表示整体内容的article元素里面。
 * 
 * @author qiun
 *
 */
class Article extends HtmlBase
{
    /**
     * 标签名称
     * @var string
     */
    protected $tagName = 'article';
}
<?php
namespace qpf\htmc\html;

/**
 * Aside 标签定义其所处内容之外的内容 - h5
 *
 * - Internet Explorer 8 以及更早的版本不支持该标签。
 * - aside 的内容应该与附近的内容相关
 * - aside 的内容可用作文章的侧栏。
 *
 * 
 * 通常用来描述与文档主体内容不相关的内容
 * 比如，博客文章用atricle标签，而博客旁边的文章信息栏(作者头像、博文分类、作者等级等于博客正文内容无关的)用aside.
 * 
 * 示例
 * ~~~
 * # 作为主要内容的附属信息部分, 当前文章有关的相关资料、名次解释，等等
 * <article>
 * <h1>…</h1>
 * <p>…</p>
 * <aside>…</aside>
 * </article>
 * 
 * # 侧边栏 , 可以使友情链接，博客中的其它文章列表、广告单元等。
 * <aside>
 *<h2>…</h2>
 *<ul>
 *  <li>…</li>
 *  <li>…</li>
 *</ul>
 *<h2>…</h2>
 *<ul>
 *  <li>…</li>
 *  <li>…</li>
 *</ul>
 *</aside>
 * ~~~
 * @author qiun
 *
 */
class Aside extends HtmlBase
{
    /**
     * 标签名称
     * @var string
     */
    protected $tagName = 'aside';
}
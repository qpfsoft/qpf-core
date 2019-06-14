<?php
namespace qpf\htmc\html;

/**
 * Address 标签定义文档或文章作者/拥有者的联系信息
 * 
 * - 如果 <address> 元素位于 <body> 元素内，则它表示文档联系信息。
 * - 如果 <address> 元素位于 <article> 元素内，则它表示文章的联系信息。
 * - <address> 标签不应该用于描述通讯地址，除非它是联系信息的一部分。
 * - <address> 元素通常连同其他信息被包含在 <footer> 元素中。
 * 
 * address字面理解为"地址", 注意，这里放的不是字面上理解的"地址"，而是指"联系信息",
 * 可以包括文档创建者的名字、站点链接、电子邮箱、真实地址、电话号码等各类联系信息。
 * 
 * 示例
 * ~~~
 * <address>
 * 此文档的作者：<a href="mailto:bill@microsoft.com">Bill Gates</a>
 * </address>
 * ~~~
 * 
 * 示例
 * ~~~
 * <body>
 * <address>
 * 作者： www.xxx.com<br />
 * <a href="mailto:x@xxx.com">给我们发邮件</a><br />
 * 地址: 门牌号564，上海普陀<br />
 * 手机号: +12 34 56 78
 * </address>
 * </body>
 * ~~~
 * 
 * @author qiun
 *
 */
class Address extends HtmlBase
{
    /**
     * 标签名称
     * @var string
     */
    protected $tagName = 'address';
}
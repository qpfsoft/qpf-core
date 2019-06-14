<?php
namespace qpf\htmc\html;

/**
 * A 超链接
 * 
 * 
 * - 图片链接: a标签包裹一张图片即可.
 * - 锚点: 其他元素可指定name值来创建锚点.
 * @author qiun
 *
 */
class A extends HtmlBase
{
    /**
     * 标签名称
     * @var string
     */
    protected $tagName = 'a';
    
    /**
     * 设置超链接目标URL
     * @param string $value url地址, 默认值`#`. 
     * - 绝对url : 即`http://` , 不确定htpps|http, 写为`//`开头
     * - 相对url : 即`index.html`站内文件
     * - 锚url : 即`#top`页面锚
     * - 不做任何事情 ： 使用2个到4个#，见的大多是"####"，也有使用"#all"等其他的
     * @return $this
     */
    public function href($value = '#')
    {
        $this->attr(['href' => $value]);
        return $this;
    }
    
    /**
     * 设置链接
     * @param string $url 默认值`javascript:;`
     * - 不做任何事情： `javascript:void(0)` 或 `javascript:;`
     */
    public function url($url = 'javascript:;')
    {
        $this->attr(['href' => $url]);
        return $this;
    }
    
    /**
     * 设置被下载的超链接目标 - h5
     * 
     * - 设置下载文件的名称
     * - 只有 Firefox 和 Chrome 支持
     * - 必须设置href属性.
     * 
     * ~~~示例
     * # 点击a标签中的图片. 就会下载名为`logo.png`文件
     * <a href="aaa.png" download="logo"><img src="aaa.png"></a>
     * ~~~
     * 
     * @param string $value 文件名, 不包含扩展名
     * @return $this
     */
    public function download($value)
    {
        $this->attr(['download' => $value]);
        return $this;
    }
    
    /**
     * 设置链接url页面的语言
     * 
     * - 主流游览器几乎都不支持
     * @param string $value 'zh', ISO 标准的双字符语言代码.
     * @return $this
     */
    public function hreflang($value)
    {
        $this->attr(['hreflang' => $value]);
        return $this;
    }
    
    /**
     * 设置如何打开URL链接
     * @param string $value 可能的值:
     * - `view_window` : 打开新窗口
     * - `view_frame` : 在框架中打开.<frame name="view_frame">
     * - _blank : 浏览器总在一个新打开、未命名的窗口中载入目标文档。
     * - _self : 默认目标, 在相同的框架或者窗口中作为源文档
     * - _parent : 载入父窗口或者包含来超链接引用的框架的框架集
     * - _top : 目标将会清除所有被包含的框架并将文档载入整个浏览器窗口。
     * @return $this
     */
    public function target($value)
    {
        $this->attr(['target' => $value]);
        return $this;
    }
    
    /**
     * 设置链接URL的MIME 类型 - h5
     * @param string $value 例`text/html`
     * 参考值: http://www.w3school.com.cn/tags/att_a_type.asp
     * @return $this
     */
    public function type($value)
    {
        $this->attr(['type' => $value]);
        return $this;
    }
    
    /**
     * 设置链接URL的关系
     * 
     * - 游览器不会有特殊效果, 但可能利于搜索引擎.
     * 
     * @param string $value 可能的值:
     * - alternate : 文档的可选版本（例如打印页、翻译页或镜像）。
     * - stylesheet : 文档的外部样式表。
     * - start : 集合中的第一个文档。
     * - next : 集合中的下一个文档。
     * - prev : 集合中的前一个文档。
     * - contents : 文档目录。
     * - index : 文档索引。
     * - glossary : 文档中所用字词的术语表或解释。
     * - copyright : 包含版权信息的文档。
     * - chapter : 文档的章。
     * - section : 文档的节。
     * - subsection : 文档的子段。
     * - appendix : 文档附录。
     * - help : 帮助文档。
     * - bookmark : 相关文档。
     * - nofollow : Google 使用 "nofollow"，用于指定 Google 搜索引擎不要跟踪链接。
     * - licence
     * - tag
     * - friend
     * @return $this
     */
    public function rel($value)
    {
        $this->attr(['rel' => $value]);
        return $this;
    }
    
    /**
     * 设置链接URL是为什么设备或媒介进行优化的 - h5
     * @param string $value
     * 运算符: `and`/`not`/`,` and/not/or 运算符
     * 设备:
     * - all : 默认, 适合所有设备
     * - aural : 语音合成器
     * - braille : 盲文反馈装置
     * - handheld : 手持设备（小屏幕、有限的带宽）
     * - projection : 投影机
     * - print : 打印预览模式/打印页面
     * - screen : 计算机屏幕
     * - tty : 电传打字机以及使用等宽字符网格的类似媒介
     * - tv : 电视类型设备（低分辨率、有限的分页能力）
     * 值:
     * # 可使用 "min-" 和 "max-" 前缀。
     * - width : 规定目标显示区域的宽度
     * - height : 规定目标显示区域的高度
     * - device-width : 规定目标显示器/纸张的宽度
     * - device-height : 规定目标显示器/纸张的高度
     * - orientation : 规定目标显示器/纸张的取向
     * - aspect-ratio : 规定目标显示区域的宽度/高度比
     * - device-aspect-ratio : 规定目标显示器/纸张的 device-width/device-height 比率
     * - color : 规定目标显示器的 bits per color
     * - color-index : 规定目标显示器能够处理的颜色数
     * - monochrome : 规定在单色帧缓冲中的每像素比特
     * - resolution : 规定目标显示器/纸张的像素密度 
     * # 无前缀
     * - scan : 规定 tv 显示器的扫描方法, 可能的值是："progressive" 和 "interlace"。
     * - grid : 规定输出设备是网格还是位图, 可能的值："1" 代表网格，"0" 是其他。
     * @return $this
     */
    public function media($value)
    {
        $this->attr(['media' => $value]);
        return $this;
    }
}
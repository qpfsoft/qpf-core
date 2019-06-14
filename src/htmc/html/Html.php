<?php
namespace qpf\htmc\html;

/**
 * HTML 标签集合
 * @author qiun
 *
 */
class Html
{
    /**
     * html标签实例池
     * @var array
     */
    protected $htmlPool = [];
    
    /**
     * 超链接标签
     * @return \qpf\htmc\html\A
     */
    public function a()
    {
        if (!isset($this->htmlPool['a'])) {
            $this->htmlPool['a'] = new A();
        }
        
        return $this->htmlPool['a'];
    }
    
    /**
     * 缩写或简称标签
     * @return \qpf\htmc\html\Abbr
     */
    public function abbr()
    {
        if (!isset($this->htmlPool['abbr'])) {
            $this->htmlPool['abbr'] = new Abbr();
        }
        
        return $this->htmlPool['abbr'];
    }
    
    /**
     * 联系信息标签
     * @return \qpf\htmc\html\Address
     */
    public function address()
    {
        if (!isset($this->htmlPool['address'])) {
            $this->htmlPool['address'] = new Address();
        }
        
        return $this->htmlPool['address'];
    }
    
    /**
     * 图像映射
     * @return \qpf\htmc\html\Area
     */
    public function area()
    {
        if (!isset($this->htmlPool['area'])) {
            $this->htmlPool['area'] = new Area();
        }
        
        return $this->htmlPool['area'];
    }
    
    /**
     * 文章外部内容标签
     * @return \qpf\htmc\html\Article
     */
    public function article()
    {
        if (!isset($this->htmlPool['article'])) {
            $this->htmlPool['article'] = new Article();
        }
        
        return $this->htmlPool['article'];
    }
    
    /**
     * 信息栏标签
     * - 附属信息, 侧边栏
     * @return \qpf\htmc\html\Aside
     */
    public function aside()
    {
        if (!isset($this->htmlPool['aside'])) {
            $this->htmlPool['aside'] = new Aside();
        }
        
        return $this->htmlPool['aside'];
    }
    
    /**
     * DIV 标签
     * - 在结束对象使用`getHtml()`方法前, 对象不可复用.
     * - 在`tagStart`和 `tagEnd`标签对方法内部, 不能再次嵌套该对象.
     * @return \qpf\htmc\html\Div
     */
    public function div()
    {
        if (!isset($this->htmlPool['div'])) {
            $this->htmlPool['div'] = new Div();
        }
        
        return $this->htmlPool['div'];
    }
    
    /**
     * 音频标签
     * @return \qpf\htmc\html\Audio
     */
    public function audio()
    {
        if (!isset($this->htmlPool['audio'])) {
            $this->htmlPool['audio'] = new Audio();
        }
        
        return $this->htmlPool['audio'];
    }
    
    /**
     * b 粗体文本标签
     * @return \qpf\htmc\html\Tag
     */
    public function b()
    {
        if (!isset($this->htmlPool['b'])) {
            $this->htmlPool['b'] = new Tag('b');
        }
        
        return $this->htmlPool['b'];
    }
    
    /**
     * bdi 文本方向隔离标签 - h5
     * 
     * - 标签允许您设置一段文本，使其脱离其父元素的文本方向设置。
     * @return \qpf\htmc\html\Tag
     */
    public function bdi()
    {
        if (!isset($this->htmlPool['bdi'])) {
            $this->htmlPool['bdi'] = new Tag('bdi');
        }
        
        return $this->htmlPool['bdi'];
    }
    
    /**
     * bdo 覆盖默认的文本方向
     *
     * - 标签允许您设置一段文本，使其脱离其父元素的文本方向设置。
     * - 目前只有 Firefox 和 Chrome 支持 <bdi> 标签。
     * 
     * @param string $dir 可选属性, 定义文字的方向.
     * - ltr : 从左到右
     * - rtl : 从右到左
     * @return \qpf\htmc\html\Tag
     */
    public function bdo($dir = null)
    {
        if (!isset($this->htmlPool['bdo'])) {
            $this->htmlPool['bdo'] = new Tag('bdo');
        }
        if (!empty($dir)) {
            $this->htmlPool['bdo']->attr(['dir' => $dir]);
        }
        
        return $this->htmlPool['bdo'];
    }
    
    /**
     * big 大号文本标签
     * @return \qpf\htmc\html\Tag
     */
    public function big()
    {
        if (!isset($this->htmlPool['big'])) {
            $this->htmlPool['big'] = new Tag('big');
        }
        
        return $this->htmlPool['big'];
    }
    
    /**
     * blockquote 块引用文本标签
     * 
     * - 左右缩进, 文本斜体
     * - 标准规定, 必须包含块级元素, 比如`<p>`
     * - 没有浏览器能够正确地显示 cite 属性。
     * - 请使用 q 元素来标记短的引用。
     * 
     * @param string $cite 可选属性, url值, 引用的来源.
     * @return \qpf\htmc\html\Tag
     */
    public function blockquote($cite = null)
    {
        if (!isset($this->htmlPool['blockquote'])) {
            $this->htmlPool['blockquote'] = new Tag('blockquote');
        }
        
        if (!empty($cite)) {
            $this->htmlPool['blockquote']->attr(['cite' => $cite]);
        }
        
        return $this->htmlPool['blockquote'];
    }
    
    /**
     * br 换行符
     * 
     * - 写法<br> 或 <br/>
     * @param string $clear 清除浮动对齐, 可能的值:
     * - left : 左
     * - right : 右
     * - all : 两边
     * @return mixed
     */
    public function br($clear = null)
    {
        if (!isset($this->htmlPool['br'])) {
            $this->htmlPool['br'] = new Tag('br');
        }
        
        if (!empty($clear)) {
            $this->htmlPool['br']->attr(['clear' => $clear]);
        }
        
        return $this->htmlPool['br'];
    }
    
    /**
     * button 按钮标签
     * @return \qpf\htmc\html\Button
     */
    public function button()
    {
        if (!isset($this->htmlPool['button'])) {
            $this->htmlPool['button'] = new Button();
        }
        
        return $this->htmlPool['button'];
    }
    
    /**
     * 清除创建的Html实例对象
     */
    public function reset()
    {
        $this->htmlPool = [];
    }
}
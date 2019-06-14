<?php
namespace qpf\htmc\html;

/**
 * Head 对象代表 HTML中的< head >标签
 *
 * - 设置标题,描述,关键字等,
 * - 添加dns预解析
 * - webApp模式
 * - 载入外部JS,CSS.
 * 
 * 下面这些标签可用在 head 部分：
 * <base>, <link>, <meta>, <script>, <style>, 以及 <title>。
 *
 * 关键一点是头部的title，description，keyword的设置。
 * 1.title设置不宜过长，要简短，网站的名字与相关的小内容，一般为10-20个字。不能重复
 * 2.keywords设置10个关键词，每个词不能太长，简短且符合你网站的特点，不能重复
 * 3.description，50个字内描述你的网站,写原创的话，并包含2-3个关键词比较好
 * - css放在头部，js放在尾部，尽量使用外链或者工具对css和js进行压缩
 * - 减少http的请求，使页面更快加载
 * - 为图片img加上alt属性，加了alt就不必加title了
 * 
 * @author qiun
 *        
 */
class Head
{
    /**
     * base标签属性列表
     * @var array
     */
    protected $base = [];
    /**
     * link链接外部样式和资源列表
     * 
     * @var array
     */
    protected $link = [];

    /**
     * meta标签管理对象
     * 
     * @var \qpf\htmc\html\Meta
     */
    protected $meta;
    /**
     * 载入外部JS列表
     * 
     * @var array
     */
    protected $script = [];
    /**
     * 载入外部CSS列表
     *
     * @var array
     */
    protected $style = [];
    /**
     * 网页标题
     * 
     * @var string
     */
    protected $title;


    /**
     * 设置网页标题
     *
     * ~~seo
     * 首页 : 主关键词-或一句含有主关键词的描述-网站名称
     * 栏目页 : 栏目名称-网站名称
     * 分类列表页 : 分类列表页名称-栏目名称-网站名称
     * 文章页 : 内容标题-栏目名称-网站名称
     * 文章标题-网站名称
     * 内容标题-栏目名称
     * ~~
     * 
     * @param string $val 一般不超过80个字符, 词语用英文'-'隔开.
     * @return \qpf\htmc\html\Head
     */
    public function title($val)
    {
        $this->title = $val;
        return $this;
    }

    /**
     * 解析title属性
     * 
     * @param string $title            
     * @return string title标签html
     */
    private function parseTitle($title = null)
    {
        $title = $title === null ? $this->title : $title;
        
        return '<title>' . $title . '</title>';
    }

    /**
     * 获得meta标签管理对象
     * 
     * @return \qpf\htmc\html\Meta
     */
    public function meta()
    {
        if ($this->meta === null) {
            $this->meta = new \qpf\htmc\html\Meta();
        }
        
        return $this->meta;
    }

    /**
     * 链接外部CSS
     * 
     * @param string $css
     *            样式文件地址
     * @return \qpf\htmc\html\Head
     */
    public function linkCss($css)
    {
        $this->link[] = [
            'link' => 'css',
            'href' => $css
        ];
        
        return $this;
    }

    /**
     * 添加图标
     *
     * icon，指的是图标，格式可为PNG\GIF\JPEG，
     * 尺寸一般为16x16、24x24、36x36等。
     *
     * @param string $ico
     *            图标地址
     * @param string|boolean $imgType
     *            默认值为null,使用默认配置,
     *            设置为空字符串'' 时,link代码不添加type属性
     * @return \qpf\htmc\html\Head
     */
    public function linkIcon($ico, $imgType = null)
    {
        $this->link[] = [
            'link' => 'icon',
            'href' => $ico,
            'type' => $imgType
        ];
        
        return $this;
    }

    /**
     * 添加网址前的图标- 优先
     *
     * shortcut icon 特指浏览器中地址栏左侧显示的图标，
     * 一般大小为16x16，后缀名为.icon
     *
     * @param string $ico
     *            图标地址
     * @return \qpf\htmc\html\Head
     */
    public function linkIconShortcut($ico = 'favicon.ico')
    {
        $this->link[] = [
            'link' => 'shortcut_icon',
            'href' => $ico
        ];
        
        return $this;
    }

    /**
     * 添加DNS预解析
     *
     * 默认情况下浏览器会对页面中和当前域名（正在浏览网页的域名）
     * 不在同一个域的域名进行预获取，并且缓存结果，这就是隐式的DNS Prefetch。
     * 如果想对页面中没有出现的域进行预获取，那么就要使用显示的DNS Prefetch了
     * DNS Prefetch应该尽量的放在网页的前面，推荐放在<meta charset=”/>后面
     *
     * 可以通过下面的标签禁止隐式的DNS Prefetch。
     * <meta http-equiv=”x-dns-prefetch-control” content=”off”> // on|off
     *
     * @param string $domain
     *            域名地址,如果为null将返回解析结果
     *            格式:
     *            - '//app.x.com'
     *            - 'http://a1.x.com'
     */
    public function dnsPrefetch($domain)
    {
        static $dns = [];
        
        if ($domain !== null) {
            $dns[] = $domain;
            return $this;
        }
        
        // $domain = null 返回解析
        if ($domain === null && ! empty($dns)) {
            $html = '';
            foreach ($dns as $href) {
                $html .= '<link rel="dns-prefetch" href="' . $href . '">' . PHP_EOL;
            }
            return $html;
        }
    }

    /**
     * DNS Prefetch禁止或开启
     * 
     * @param string $type
     *            [on|off]状态
     * @return \qpf\htmc\html\Head
     */
    public function dnsPrefetch_status($type = 'on')
    {
        $this->meta()->dnspModel($type);
        return $this;
    }

    /**
     * 解析link属性
     *
     * @return string link的html代码
     */
    private function parseLink()
    {
        if (empty($this->link))
            return '';
        
        $conf = [
            'css' => [
                'rel' => 'stylesheet'
                // 'type' => 'text/css',// html5省略
            ],
            'icon' => [
                'rel' => 'icon',
                'type' => 'image/png'
            ],
            'shortcut_icon' => [
                'rel' => 'shortcut icon'
            ]
        ];
        
        $link = '';
        foreach ($this->link as $i => $v) {
            $link .= '<link rel="' . $conf[$v['link']]['rel'] . '"';
            
            // type的规则时传递null时使用$conf配置,如果存在的话.
            // 传递的type值为''空字符串,将不添加type属性
            
            if (isset($v['type']) && $v['type'] !== null) { // 传递了type值直接使用,但不为能空
                $link .= 'type="' . $v['type'] . '"';
            } elseif (isset($conf[$v['link']]['type'])) { // 存在默认配置时
                
                if (isset($v['type']) && $v['type'] === '') {
                    // 允许设置type为'' 空字符串来取消设置type属性
                } else {
                    $link .= 'type="' . $conf[$v['link']]['type'] . '"';
                }
            }
            
            $link .= ' href="' . $v['href'] . '"> ' . PHP_EOL;
        }
        
        return $link;
    }

    /**
     * 载入外部JS文档
     * 
     * @param string $src
     *            js文件地址
     * @return $this
     */
    public function script($src)
    {
        $this->script[] = $src;
        return $this;
    }

    /**
     * 解析script属性
     * 
     * @return string
     */
    private function parseScript()
    {
        if (empty($this->script)) {
            return '';
        }
        $html = '';
        foreach ($this->script as $i => $src) {
            $html .= '<script src="' . $src . '"></script>' . PHP_EOL;
        }
        
        return $html;
    }
    
    
    /**
     * 设置所有相对链接的默认前缀
     * 
     * - 作用于 <a>、<img>、<link>、<form> 标签中的 URL。
     * - 属性值替换了当前网址的基础url.
     * - 不会影响绝对url.
     * - base 标签必须位于 head 元素内部。
     * 
     * @param string $url 例如`http://www.x.com/public/`
     * @return $this
     */
    public function baseHref($url)
    {
        $this->base['href'] = $url;
        return $this;
    }
    
    /**
     * 设置全局打开URL链接
     * @param string $value 可能的值:
     * - _blank : 浏览器总在一个新打开、未命名的窗口中载入目标文档。
     * - _self : 默认目标, 在相同的框架或者窗口中作为源文档
     * - _parent : 载入父窗口或者包含来超链接引用的框架的框架集
     * - _top : 目标将会清除所有被包含的框架并将文档载入整个浏览器窗口。
     * @return $this
     */
    public function baseTarget($value)
    {
        $this->base['target'] = $value;
        return $this;
    }

    /**
     * 获得headn标签内所有可用标签的html代码
     *
     * @return string
     */
    public function getHtml()
    {
        return '<head>' . PHP_EOL . $this->meta()
            ->charset()
            ->getHtml($this->dnsPrefetch(null)) . $this->parseTitle() . PHP_EOL . $this->parseLink() . $this->parseScript() . '</head>';
    }
}
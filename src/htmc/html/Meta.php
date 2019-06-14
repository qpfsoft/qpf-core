<?php
namespace qpf\htmc\html;

/**
 * Meta
 *
 *
 *
 * <!-- 针对手持设备优化，主要是针对一些老的不识别viewport的浏览器，比如黑莓 --> <meta name="HandheldFriendly" content="true">
 * <!-- 微软的老式浏览器 --> <meta name="MobileOptimized" content="320">
 * <!-- uc强制竖屏 --> <meta name="screen-orientation" content="portrait">
 * <!-- QQ强制竖屏 --> <meta name="x5-orientation" content="portrait">
 * <!-- UC强制全屏 --> <meta name="full-screen" content="yes">
 * <!-- QQ强制全屏 --> <meta name="x5-fullscreen" content="true">
 * <!-- UC应用模式 --> <meta name="browsermode" content="application">
 * <!-- QQ应用模式 --> <meta name="x5-page-mode" content="app">
 * <!-- windows phone 点击无高光 --> <meta name="msapplication-tap-highlight" content="no">
 * <!-- 适应移动端end -->
 * 
 * @author qiun
 *        
 */
class Meta
{

    /**
     * 声明文档字符编码
     * 
     * @var string
     */
    private $charset = 'utf-8';

    /**
     * 把 content 属性关联到一个名称
     * 
     * @var array
     */
    private $name = [];

    /**
     * 把 content 属性关联到 HTTP 头部
     * 
     * @var array
     */
    private $http_equiv = [];

    /**
     * 设置文字编码
     *
     * - gbk : 优先使用, gbk是gb2312的扩展,并且向后兼容，具有更好的兼容性.
     * - gb2312 : 支持中文和英文
     * - gb18030 : 为了兼容某些特殊字符，时采用
     * - big5 : 面向港台
     * - utf-8 : 支持国际语言
     * 
     * @param string $val
     *            如果未设置采用程序设置
     * @return $this
     */
    public function charset($val = null)
    {
        if ($val === null) {
            // $val = \QPF::$app->charset === 'utf8' ? 'utf-8' : \QPF::$app->charset;
            $val = 'utf-8';
        }
        $this->charset = $val;
        return $this;
    }

    /**
     * 解析charset属性
     * 
     * @return string
     */
    private function parseCharset()
    {
        return '<meta charset="' . $this->charset . '">';
    }

    /**
     * 设置网页作者
     * 
     * @param string $val
     *            author,email address
     * @return $this
     */
    public function author($val)
    {
        $this->name['author'] = $val;
        return $this;
    }

    /**
     * 设置网页描述
     *
     * ~~~seo
     * 一般不超过150个字符，描述内容要和页面内容相关
     *
     * 首页 : 将首页的标题、关键词和一些特殊栏目的内容融合到里面，写成简单的介绍
     * 栏目页 : 将栏目的标题、关键字、分类列表名称融合到里面，写成简单的介绍
     * 分类列表页 : 把分类列表的标题、关键词融合在一起，写成简单的介绍
     * 文章页 : 1文章标题、文章中的重要内容和关键词融合在一起，写成简单的介绍
     * 2在文章首段和标题中加入关键词,然后直接将文章首段的内容复制到description中即可
     * ~~~
     * 
     * @param string $val            
     * @return $this
     */
    public function description($val)
    {
        $this->name['description'] = $val;
        return $this;
    }

    /**
     * 设置网页关键字
     *
     * ~~~seo
     * 每个词都要能在内容中找到相应匹配
     * 一般不超过3个
     * 尽量将重要的关键字靠前放
     *
     * 首页 : 网站名称,主要栏目名,主要关键词
     * 栏目页 : 栏目名称,栏目关键字,栏目分类列表名称
     * 分类列表页 : 栏目中的主要关键字写入即可
     * 文章页 : 提取文章中的关键词,文章中出现比较多的词
     * ~~~
     * 
     * @param string $val
     *            多个用英文逗号分隔
     * @return $this
     */
    public function keywords($val)
    {
        $this->name['keywords'] = $val;
        return $this;
    }

    /**
     * 设置网页文件是用什么工具生成
     * 
     * @param string $val
     *            说明网站的采用的什么软件制作
     * @return $this
     */
    public function generator($val)
    {
        $this->name['generator'] = $val;
        return $this;
    }

    /**
     * 设置页面的最新版本
     * 
     * @param string $val
     *            格式: "作者名字, 年/月/日"
     */
    public function revised($val)
    {
        $this->name['revised'] = $val;
        return $this;
    }

    /**
     * 设置页面搜索引擎索引方式
     * 
     * @param string $val
     *            默认index,follow
     *            - all : (默认)文件将被检索，且页面上的链接可以被查询；
     *            - none : 文件将不被检索，且页面上的链接不可以被查询；
     *            - index : 文件将被检索；
     *            - follow : 页面上的链接可以被查询；
     *            - noindex : 文件将不被检索，但页面上的链接可以被查询；
     *            - nofollow : 文件将不被检索，页面上的链接可以被查询。
     *            多个使用英文逗号分隔
     * @return $this
     */
    public function robots($val = 'index,follow')
    {
        $this->name['robots'] = $val;
        return $this;
    }

    /**
     * 设置移动端视图
     *
     * @param int|string $width
     *            宽度(数值/device-width,范围从200 到10,000，默认为980 像素)
     *            - 适配 iPhone 6 : width=375
     *            - 适配 iPhone 6plus : width=414
     *            - 4.7~5 寸安卓 : 360
     *            - 5.5 寸安卓 : 400
     * @param int|string $height
     *            高度(数值 /device-height,范围从223 到10,000）
     *            
     * @param int|string $initial_scale
     *            初始的缩放比例 （范围从>0 到10）
     *            - 即页面初始缩放程度。这是一个浮点值，是页面大小的一个乘数。
     *            - 1.0 : 分辨率的1:1来展现
     *            - 2.0 :那么这个页面就会放大为2倍
     *            
     * @param int $maximum_scale
     *            允许用户缩放到的最小比例
     *            - 这也是一个浮点值，用以指出页面大小与屏幕大小相比的最大乘数
     *            - 例如2.0 ,这个页面与target size相比，最多能放大2倍
     *            
     * @param int $minimum_scale
     *            允许用户缩放到的最大比例
     * @param string $user_scalable
     *            用户是否可以手动缩 (no,yes)
     * @param boolean $minimal_ui
     *            iOS7.1beta 2 中新增属性，可以在页面加载时最小化上下状态栏。
     *            iOS 8更新后则又取消了这个设置.
     * @return $this
     */
    public function viewport($width = 'device-width', $height = null, $initial_scale = '1.0', $maximum_scale = '1.0', $minimum_scale = '1.0', $user_scalable = 'no', $minimal_ui = null)
    {
        $conf = 'width=' . $width;
        $conf .= ($height !== null ? ',height=' . $height : '');
        $conf .= ($initial_scale !== null ? ',initial-scale=' . $initial_scale : '');
        $conf .= ($maximum_scale !== null ? ',maximum-scale=' . $maximum_scale : '');
        $conf .= ($minimum_scale !== null ? ',minimum-scale' . $minimum_scale : '');
        $conf .= ($user_scalable !== null ? ',user-scalable=' . $user_scalable : '');
        
        $conf .= ($minimal_ui === true ? ',minimal-ui' : '');
        $this->name['viewport'] = $conf;
        
        return $this;
    }

    /**
     * 启用webApp全屏模式 for IOS
     *
     * 注意:生成app时要关闭缓存<meta http-equiv="Pragma" content="no-cache">
     * 
     * @param string $appName
     *            添加到主屏后的标题,iOS 6 新增
     * @param string $statusStyle
     *            状态栏的背景颜色
     *            - default 默认值，网页内容从状态栏底部开始
     *            - black 状态栏背景是黑色，网页内容从状态栏底部开始
     *            - black-translucent 状态栏背景是黑色半透明，网页内容充满整个屏幕，顶部会被状态栏遮挡
     */
    public function appModel($appName, $statusStyle = 'defautl')
    {
        $this->name['apple-mobile-web-app-capable'] = 'yes';
        $this->name['apple-mobile-web-app-status-bar-style'] = $statusStyle;
        $this->name['apple-mobile-web-app-title'] = $appName;
        return $this;
    }

    /**
     * webApp Ios 图标
     *
     * iOS特有的图标大小，在 iPhone 6 plus上是180×180，iPhone 6 上则还是老的120×120。
     * 适配iPhone 6 plus，则需要在<head>中加上这段:
     * <link rel="apple-touch-icon-precomposed" sizes="180x180" href="retinahd_icon.png">
     *
     * rel 参数：
     * - apple-touch-icon 图片自动处理成圆角和高光等效果
     * - apple-touch-icon-precomposed 禁止系统自动添加效果，直接显示设计原图
     *
     * iPhone 和 iTouch，默认 57x57 像素:
     * <!-- iPhone 和 iTouch，默认 57x57 像素，必须有 -->
     * <link rel="apple-touch-icon-precomposed" href="/apple-touch-icon-57x57-precomposed.png" />
     *
     * Pad，72x72 像素，可以没有，但推荐有:
     * <!-- iPad，72x72 像素，可以没有，但推荐有 -->
     * <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/apple-touch-icon-72x72-precomposed.png" />
     *
     * Retina iPhone 和 Retina iTouch，114x114 像素，可以没有，但推荐有:
     * <!-- Retina iPhone 和 Retina iTouch，114x114 像素，可以没有，但推荐有 -->
     * <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/apple-touch-icon-114x114-precomposed.png" />
     *
     * Retina iPad，144x144 像素，可以没有，但推荐有:
     * <!-- Retina iPad，144x144 像素，可以没有，但推荐有 -->
     * <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/apple-touch-icon-144x144-precomposed.png" />
     */
    public function appModel_ico()
    {}

    /**
     * webApp ios 启动画面
     *
     * iPhone 6对应的图片大小是750×1294，iPhone 6 Plus 对应的是1242×2148 。
     * <link rel="apple-touch-startup-image" href="launch6.png" media="(device-width: 375px)">
     * <link rel="apple-touch-startup-image" href="launch6plus.png" media="(device-width: 414px)">
     *
     * ~~~iPad 的启动画面是不包括状态栏区域的。
     * iPad 竖屏 768 x 1004（标准分辨率）
     * <!-- iPad 竖屏 768 x 1004（标准分辨率） -->
     * <link rel="apple-touch-startup-image" sizes="768x1004" href="/splash-screen-768x1004.png" />
     *
     * iPad 竖屏 1536x2008（Retina）
     * <!-- iPad 竖屏 1536x2008（Retina） -->
     * <link rel="apple-touch-startup-image" sizes="1536x2008" href="/splash-screen-1536x2008.png" />
     *
     * iPad 横屏 1024x748（标准分辨率）
     * <!-- iPad 横屏 1024x748（标准分辨率） -->
     * <link rel="apple-touch-startup-image" sizes="1024x748" href="/Default-Portrait-1024x748.png" />
     *
     * iPad 横屏 2048x1496（Retina）
     * <!-- iPad 横屏 2048x1496（Retina） -->
     * <link rel="apple-touch-startup-image" sizes="2048x1496" href="/splash-screen-2048x1496.png" />
     * ~~~
     *
     * ~~~iPhone 和 iPod touch 的启动画面是包含状态栏区域的
     *
     * iPhone/iPod Touch 竖屏 320x480 (标准分辨率)
     * <!-- iPhone/iPod Touch 竖屏 320x480 (标准分辨率) -->
     * <link rel="apple-touch-startup-image" href="/splash-screen-320x480.png" />
     *
     * iPhone/iPod Touch 竖屏 640x960 (Retina)
     * <!-- iPhone/iPod Touch 竖屏 640x960 (Retina) -->
     * <link rel="apple-touch-startup-image" sizes="640x960" href="/splash-screen-640x960.png" />
     *
     * iPhone 5/iPod Touch 5 竖屏 640x1136 (Retina)
     * <!-- iPhone 5/iPod Touch 5 竖屏 640x1136 (Retina) -->
     * <link rel="apple-touch-startup-image" sizes="640x1136" href="/splash-screen-640x1136.png" />
     */
    public function appModel_startupScreen()
    {}

    /**
     * 添加智能 App 广告条 Smart App Banner（iOS 6+ Safari）
     *
     * 告诉游览器该网站对应的app,并显示下载提示
     * 
     * @param string $appID            
     * @param string $myAffiliateData            
     * @param string $myURL            
     * @return $this
     */
    public function appItunes($appID, $myAffiliateData, $myURL)
    {
        $conf = 'app-id=' . $appID;
        $conf .= ', affiliate-data=' . $myAffiliateData;
        $conf .= ', app-argument=' . $myURL;
        $this->name['apple-itunes-app'] = $conf;
        
        return $this;
    }

    /**
     * 关闭数字自动识别为电话号码
     *
     * @return $this
     */
    public function notPhoneNum()
    {
        $this->name['format-detection'][] = 'telephone=no';
        return $this;
    }

    /**
     * 关闭识别邮箱
     * 
     * @return $this
     */
    public function notEmail()
    {
        $this->name['format-detection'][] = 'email=no';
        return $this;
    }

    /**
     * 优先使用IE最新版本和 Chrome
     */
    public function newIeChrome()
    {
        $this->http_equiv['X-UA-Compatible'] = 'IE=edge,chrome=1';
        return $this;
    }

    /**
     * 360使用Google Chrome Frame
     */
    public function new360Chrome()
    {
        $this->name['renderer'] = 'webkit';
        // 保险期间加入
        $this->newIeChrome();
        return $this;
    }

    /**
     * 百度禁止转码
     *
     * 通过百度手机打开网页时，百度可能会对你的网页进行转码，
     * 脱下你的衣服，往你的身上贴狗皮膏药的广告
     */
    public function notBaidu()
    {
        $this->http_equiv['Cache-Control'] = 'no-siteapp';
        return $this;
    }

    /**
     * Windows 8 磁贴颜色
     * 
     * @param string $val
     *            颜色,例如'#000'
     * @return \qpf\htmc\html\Meta
     */
    public function win8_color($val)
    {
        $this->name['msapplication-TileColor'] = $val;
        return $this;
    }

    /**
     * Windows 8 磁贴图标
     * 
     * @param string $val
     *            图标图片,例'icon.png'
     * @return \qpf\htmc\html\Meta
     */
    public function win8_ico($val)
    {
        $this->name['msapplication-TileImage'] = $val;
        return $this;
    }

    /**
     * DNS预解析功能
     *
     * @param string $type
     *            [on|off]用于告诉游览器是否启用预先解析DNS,
     *            默认不设置即为开启.
     * @return \qpf\htmc\html\Meta
     */
    public function dnspModel($type)
    {
        $this->http_equiv['x-dns-prefetch-control'] = $type;
        return $this;
    }

    /**
     * 解析Name属性
     * 
     * @return string 生成所以meta-name的html
     */
    private function parseName()
    {
        if (empty($this->name)) {
            return '';
        }
        $html = '';
        foreach ($this->name as $n => $v) {
            if (is_array($v)) {
                foreach ($v as $value) {
                    $html .= '<meta name="' . $n . '" content="' . $value . '">' . PHP_EOL;
                }
            } else {
                $html .= '<meta name="' . $n . '" content="' . $v . '">' . PHP_EOL;
            }
        }
        
        return $html;
    }

    /**
     * 解析http_equiv属性
     * 
     * @return string
     */
    private function parseHttpEquiv()
    {
        if (empty($this->http_equiv)) {
            return '';
        }
        $html = '';
        foreach ($this->http_equiv as $equiv => $content) {
            $html .= '<meta http-equiv="' . $equiv . '" content="' . $content . '">' . PHP_EOL;
        }
        
        return $html;
    }

    /**
     * 获取Meta标签的html代码
     *
     * @param string $dns
     *            DNS Prefetch推荐放在sharset后面,
     *            参数用于插入dns预解析标签
     * @return string
     */
    public function getHtml($dns = null)
    {
        return $this->parseCharset() . PHP_EOL . $dns . $this->parseName() . $this->parseHttpEquiv();
    }
}
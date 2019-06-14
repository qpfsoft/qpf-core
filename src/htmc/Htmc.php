<?php
namespace qpf\htmc;

use qpf;
use qpf\htmc\html\Form;
use qpf\htmc\css\Selector;
use qpf\htmc\html\Tag;
use qpf\htmc\html\TagUl;
use qpf\htmc\html\TagA;
use qpf\htmc\input\Input;
use qpf\htmc\Element;

/**
 * htmc 类是为了快速创建一个表单页面
 *
 * htmc类中可直接调用一下元素对象.
 * - head管理器
 * - input管理器
 * - form管理器
 * - css管理器
 *
 * # 向body中写入html的方式:
 * ~~~
 * $htmc->body[] = '<br>' // 直接写入html文本;
 * ~~~
 * 
 * # 写入页面style样式
 * ~~~
 * // 1: 使用css属性构建器和手写自定义选择器。
 * $h5->addStyle('h1', CssBuilder::text()->font_family('微软雅黑'));
 * 
 * // 2：使用了构建css选择器和css属性来创建样式到页面。
 * $h5->addStyle(CssBuilder::query()->qClass('test'), 
 *          CssBuilder::css3()->column_rule('1px', 'solid', '#fff'),
 *          CssBuilder::flexBox()->display_flex()
 *          );
 * ~~~
 *
 * # 创建表单元素的方式
 * ~~~
 * // 直接创建，方便调用更多属性。
 * Text::this()->id('rbit')->placeholder('请输入内容')->getHtml();
 * // 效果相同，需要手动配置属性数组或[[Option]]生成器来创建。
 * $hc->input()->text('rbit', '', $hc->input()->option()->placeholder('请输入内容')->create());
 * // 手动配置
 * $hc->input()->text('rbit', '', ['placeholder' => '请输入内容']);
 * 注意：
 * - 直接使用元素对象实例，最后要[[getHtml()]]执行方法返回html。
 * - [[Option]] 不太推荐使用，建议直接配置属性数组。
 * ~~~
 *
 * # 按钮的使用
 * ~~~
 * // 以下两种方法效果相同
 * Button::this()->id('bit')->value('查询对应梦幻币');
 * $hc->input()->button('bit', '查询对应梦幻币');
 * ~~~
 * 
 * 样式标签:
 * - b : 加粗
 * 
 * 
 * 
 * HTML5规范
 * - <h1> - <h6> 来表示标题
 * - 使用 <em> 标签来表示强调的文本
 * - 使用 <strong> 标签来表示重要文本
 * - 使用 <mark> 标签来表示标注的/突出显示的文本
 * 
 * 
 * 
 * @author qiun
 *        
 */
class Htmc
{
    /**
     * html换行
     * @var string
     */
    const BR = '<br>';
    /**
     * html注解开始
     * @var string
     */
    const HTML_START = '<!--';
    /**
     * html注解结束
     * @var string
     */
    const HTML_END = '-->';
    
    /**
     * Html标签集合
     * @var \qpf\htmc\html\Html
     */
    private $html;
    /**
     * head对象
     * 
     * @var \qpf\htmc\html\Head
     */
    private $head;
    /**
     * input对象
     * 
     * @var \qpf\htmc\Input
     */
    private $input;
    /**
     * form对象
     * 
     * @var \qpf\htmc\Form
     */
    private $form;
    /**
     * CSS属性生成管理器
     * 
     * @var \qpf\htmc\Css
     */
    private $css;
    /**
     * CSS选择符生成器
     * @var \qpf\htmc\css\Selector
     */
    private $selector;
    

    /**
     * body标签内的html代码列表
     *
     * # 写入内容到body标签中
     * $this->body[] = '';
     * 
     * @var array
     */
    public $body = [];

    /**
     * style标签内的css样式
     * 
     * @var array
     */
    public $pageStyle = [];

    /**
     * 单例加载模式
     *
     * 设置为false时,类内置的加载方法失效
     * 
     * @var boolean
     */
    private $testModel = true;
    
    /**
     * 加载指定类 - 非框架模式
     *
     * @param string $class
     *            类名,同目录下的类名,不区分大小写,
     *            会自动转换为首字母大写.
     */
    public function loadClass($class)
    {
        if (! $this->testModel) {
            return;
        }
        $PATH = './';
        $this->loadFile($PATH . ucwords($class) . '.php');
    }

    /**
     * 载入指定文件 - 非框架模式
     *
     * 本载入方法有缓存和放重复载入机制
     *
     * @param string $file
     *            载入的文件路径和文件名
     */
    public function loadFile($file)
    {
        static $loadCache = [];
        if (isset($loadCache[$file])) {
            return $loadCache[$file];
        } elseif (is_file($file)) {
            return $loadCache[$file] = include ($file);
        } else {
            return false;
        }
    }

    /**
     * 静态创建htmc
     * @return \qpf\htmc\Htmc
     */
    public static function this()
    {
        return new static;
    }
    
    /**
     * 元素集合
     * @var Element
     */
    protected $elem;
    
    /**
     * 获取元素 v2.0
     * @return Element
     */
    public function elem()
    {
        if($this->elem === null) {
            $this->elem = new Element();
        }
        
        return $this->elem;
    }
    
    /**
     * HTMl标签集合 v1.0
     * 
     * - 返回的标签对象执行[[getHtml()]]后可重复使用.
     * @return \qpf\htmc\html\Html
     */
    public function html()
    {
        if ($this->html === null) {
            $this->html = new \qpf\htmc\html\Html();
        }
        return $this->html;
    }
    
    /**
     * 获取head对象
     * 
     * @return \qpf\htmc\html\Head
     */
    public function head()
    {
        if ($this->head === null) {
            $this->head = new \qpf\htmc\html\Head();
        }
        return $this->head;
    }

    /**
     * 获取input对象
     * 
     * @return \qpf\htmc\input\Input
     */
    public function input()
    {
        if ($this->input === null) {
            $this->input = new Input();
        }
        return $this->input;
    }

    /**
     * 获取form对象
     * 
     * @return \qpf\htmc\html\Form
     */
    public function form()
    {
        if ($this->form === null) {
            $this->form = new Form();
        }
        return $this->form;
    }

    /**
     * 返回css管理器
     * 
     * @return \qpf\htmc\Css
     */
    public function css()
    {
        if ($this->css === null) {
            $this->css = new Css();
        }
        
        return $this->css;
    }
    
    /**
     * 返回css选择符
     * @return \qpf\htmc\css\Selector
     */
    public function selector()
    {
        if ($this->selector === null) {
            $this->selector = new Selector();
        }
        
        return $this->selector;
    }

    /**
     * 设置网页标题
     *
     * @param string $string            
     * @return $this
     */
    public function title($string)
    {
        $this->head()->title($string);
        return $this;
    }

    /**
     * SEO优化部分
     * 
     * @param string $title 页面标题
     * @param string $keywords 关键词
     * @param string $description 描述
     * @param string $author 作者
     * @param string $robots 搜索引擎设置
     * @return \qpf\htmc\Htmc
     */
    public function seo($title, $keywords, $description, $author, $robots = 'index,follow')
    {
        $this->head()
            ->title($title)
            ->meta()
            ->keywords($keywords)
            ->description($description)
            ->author($author)
            ->robots($robots);
        
        return $this;
    }

    private $_lang = null;

    /**
     * 设置页面语言
     *
     * # 使用单一的 zh 和 zh-CN 均属于废弃用法。
     * ~~~基于国际标准RFC 4646:
     * zh-Hans 简体中文
     * zh-Hans-CN 大陆地区使用的简体中文
     * zh-Hans-HK 香港地区使用的简体中文
     * zh-Hans-MO 澳门使用的简体中文
     * zh-Hans-SG 新加坡使用的简体中文
     * zh-Hans-TW 台湾使用的简体中文
     * zh-Hant 繁体中文
     * zh-Hant-CN 大陆地区使用的繁体中文
     * zh-Hant-HK 香港地区使用的繁体中文
     * zh-Hant-MO 澳门使用的繁体中文
     * zh-Hant-SG 新加坡使用的繁体中文
     * zh-Hant-TW 台湾使用的繁体中文
     * ~~~
     * 
     * @param string $val            
     */
    public function lang($val = 'zh-Hans-CN')
    {
        $this->_lang = $val;
    }

    /**
     * 创建HTML标签 - 自定义
     * 
     * - 简单的标签, 属性需自己定义.
     * 
     * @param string $name 标签名称
     * @return \qpf\htmc\html\Tag
     */
    public function tag($name)
    {
        return new Tag($name);
    }
    
    /**
     * 快速创建标签
     * @param string $type 标签名称
     * @param string $content 内容
     * @param array|strig $option 标签属性
     */
    public function tagFast($type, $content, $option = null)
    {
        if(!empty($option)) {
            if (is_array($option)) {
                $arr = '';
                foreach ($option as $i => $v) {
                    $arr .= " $i=\"$v\"";
                }
                $option = $arr;
            } else {
                $option = ' ' . $option;
            }
        }
        
        return $str = "<{$type}{$option}>$content</{$type}>";
    }
    
    /**
     * ul标签
     * @var \qpf\htmc\html\TagUl
     */
    private $tagUl;
    
    /**
     * 创建ul标签
     * @return \qpf\htmc\html\TagUl
     */
    public function tagUl()
    {
        if (is_null($this->tagUl)) {
            $this->tagUl = new TagUl();
        }
        return $this->tagUl;
    }
    
    /**
     * 解析lang语言属性
     * 
     * @return string
     */
    private function buildLang()
    {
        if ($this->_lang !== null) {
            return 'lang="' . $this->_lang . '"';
        }
    }

    /**
     * 添加Class类样式到当前页面style源
     *
     * @param string|callable $class
     *            直接写入到页面样式内.
     *            - string : 完整的一组带`{}`的css样式
     *            - callable : 回调的方式来创建一组css样式:
     *            ~~~ 实例
     *            $hc->addStyle(function(\qpf\htmc\Css $css) {
     *            $css->layout()->width('100');
     *            return $css->createClass('qpf'); //返回css样式即可
     *            });
     *            ~~~
     * @return $this
     */
    public function addClass($class)
    {
        if (is_callable($class)) {
            $result = call_user_func($class, $this->css());
            $this->pageStyle[] = $result;
        } elseif (is_string($class)) {
            $this->pageStyle[] = $class;
        }
        
        return $this;
    }

    /**
     * 添加CSS样式到当前页面 - 带换行
     *
     * - 不限制参数个数
     * - 将css属性通过函数参数的方式以此传递
     * - 手写css 或 使用CssBuilder类
     * - 多参数, 每条属性都将自动换行
     * ~~~实例
     * # 多参数的方式 [推荐]
     * $htmc->addStyle('#div1',
     * CssBuilder::tools()->position_vertical_center('100px', '50px'),
     * CssBuilder::background()->background_color('#eee'),
     * CssBuilder::text()->line_height('50px'),
     * CssBuilder::text()->text_align_center()
     * );
     * # 一个字符串参数方式:
     * $h5->addStyle(
     * '#div1{'.
     * CssBuilder::tools()->position_vertical_center('100px', '50px').
     * CssBuilder::background()->background_color('#eee').
     * CssBuilder::text()->line_height('50px').
     * CssBuilder::text()->text_align_center().
     * '}'
     * );
     * ~~~
     * 
     * 伪选择器:
     * :before : 元素后
     *
     * @param string $css css样式字符串, 多个参数时第一参数作为class名称
     * - 参数1: css样式的名称, 即选择器
     * - 参数2~100 : 为css样式属性
     * @return $this
     */
    public function addStyle($css)
    {
        $this->pageStyle[] = $this->css()->classBuild(func_get_args());
        return $this;
    }

    /**
     * 添加DIV对象
     *
     * @param string|array $option 元素属性, 数组属性:
     * - id : 元素id值
     * - class : 元素应用的类
     * - style : 元素内置样式
     * @return \qpf\htmc\Div
     */
    public function div($option = [])
    {
        return $this->htmCore[] = new Div($option);
    }

    /**
     * 创建a标签对象
     * @return \qpf\htmc\html\TagA
     */
    public function tagA()
    {
        return new TagA();
    }
    
    //----------------构建页面html代码--------------------------


    /**
     * 获取当前页面Style源内容
     * 
     * @return string 当前页面内镶css源
     */
    private function buildPageStyle()
    {
        if ($this->pageStyle === []) {
            return '';
        }
        $str = '';
        $str .= '<!-- Start: build by htmc/css -->' . PHP_EOL;
        $str .= '<style type="text/css">' . PHP_EOL;
        foreach ($this->pageStyle as $i => $class) {
            $str .= $class;
        }
        $str .= '</style>' . PHP_EOL;
        $str .= '<!-- End: build by htmc/css -->';
        return $str;
    }
    
    /**
     * 解析添加的DIV对象为html
     *
     * @return string div的HTML代码
     */
    private function parseHtmCore()
    {
        $html = '';
        foreach ($this->htmCore as $index => $tag) {
            if (is_object($tag)) {
                if ($tag instanceof \qpf\htmc\Div) {
                    $html .= $tag->getHtml() . PHP_EOL;
                }
            } elseif (is_string($tag)) {
                $html .= $tag . PHP_EOL;
            }
        }
        return $html;
    }

    /**
     * 解析body内容列表
     *
     * # 向body中添加内容
     * $this->body[] = 'html';
     *
     * @param string $add 向body标签内,尾部插入内容 预留用于加入js脚本
     * @return string
     */
    private function parseBody($add = null)
    {
        if (empty($this->body)) {
            $content = '';
        } elseif (is_array($this->body)) {
            
            // 自动转换元素对象为字符串
            foreach ($this->body as $i => $line) {
                if($line instanceof \qpf\htmc\base\Element) {
                    $this->body[$i] = $line->build();
                }
            }
            
            $this->checkBodyArray();
            
            $content = implode(PHP_EOL, $this->body) . PHP_EOL;
        }
        
        return '<body>' . PHP_EOL . $content . $add . '</body>' . PHP_EOL;
    }
    
    /**
     * 检测body内容数组
     * 
     * - 为了便利, 采用数组赋值的方式来设置body内容.
     * 由于没有正确的结束对象来返回字符串. 反而传递了对象到body中并造成错误.
     */
    private function checkBodyArray()
    {
        foreach ($this->body as $i => $v) {
            if (!is_string($v)) {
                $msg = '$HTMC->body[] = Object; <br><br>';
                if ($v instanceof \qpf\htmc\html\HtmlBase) {
                    $msg .= echor($v, true) . self::BR .' - 标签对象, 没有正确的结束对象, 来返回HTML代码.';
                } else {
                    $msg .= echor($v, true) . self::BR . ' - body标签内添加了错误的对象实例, 需要转换为字符串!';
                }
                
                $msg .= '<br> <p>正确关闭对象的方法 : <br> `->getHtml()` 或 `->tagEnd()` 等.</p>';
                $this->error($msg);
            }
        }
    }
    
    /**
     * HTMC内部用户错误提示页面
     * @param string $msg 错误信息文本
     */
    private function error($msg)
    {
        $hc = new static();
        
        $hc->title('QPF-HTMC-解析器');
        
        $css = $hc->css()->attr();
        $hc->addStyle('body',
            $css->background()->background_color('#FFFFCE'),
            $css->layout()->width('500px'),
            $css->layout()->margin2('100px', 'auto'));
        $hc->addStyle('h1',
            $css->text()->color('#C21B30'));
        $hc->addStyle('h2',
            $css->text()->color('#C21B7D'));
        $hc->addStyle('#msg',
            $css->text()->color('#0066B6'));
        
        $hc->body[] = $hc->tag('h1')->content('HTMC : ( 发现错误')->getHtml();
        $hc->body[] = $hc->tag('h2')->content('Code :')->getHtml();
        $hc->body[] = $hc->tag('div')->attr(['id'=>'msg'])->content($msg)->getHtml();
        
        exit($hc->getHtml());
    }

    /**
     * 获得HTML内所以标签的代码
     * 
     * @return string
     */
    public function getHtml()
    {
        return '<!doctype html>' . PHP_EOL 
        . '<html' . $this->buildLang() . '>' . PHP_EOL 
        /* 页面<head> */
        . $this->head()->getHtml() . PHP_EOL 
        /* 页面<style> */
        . $this->buildPageStyle() . PHP_EOL
        /* 页面<body> */
        . $this->parseBody() 
        . '</html>';
    }
}



/* $h = new Htmc();
$h->addClass(Css::this()->width('300px')->height('200px')->bg_color('#FC5E61')->createClass('main'));
//$h->addClass(Css::this()->width('100%')->bg_color('#FC1E61')->createIdClass('d1'));
//$h->addClass(Css::this()->width('300px')->height('20px')->bg_color('#FC5E61')->createIdClass('d1 div'));

$h->div('main')->setContent('111')->setStyle(Css::this()->width('100px')->height('100px')->bg_color('red')->createString());

$odi = $h->div('d1')->setClass('main')->setContent('ni,hao!');
$o123 = $odi->div('1223');//->setContent('my name is 1223!');
$o123->div('bbbb')->setContent('my name is bbbb!');
$occc = $o123->div('cccc')->setContent('my name is cccc!');
$occc->div('yy');

$odi->setContent('main-end');

echo $h->getHtml(); */
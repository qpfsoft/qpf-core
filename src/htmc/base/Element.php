<?php
namespace qpf\htmc\base;

/**
 * Element 元素对象代表一个 html 标签元素
 */
class Element extends ElementBuilder
{
    /**
     * 元素名称
     * @var string
     */
    protected $name;
    
    /**
     * 是否需要闭合元素, 默认`true`
     * @var bool
     */
    protected $end = true;
    
    /**
     * 元素属性
     * @var array
     */
    protected $attr = [];
    
    /**
     * 元素内容行
     * @var array
     */
    protected $content = [];
    
    /**
     * 元素属性映射表
     * - 用于标识属性那些元素可用, 自动过滤
     * - 属性的映射为数组类型, 代表多个元素拥有它.
     * - 属性的映射为字符串, 代表元素专属.
     * - 属性的映射为`null`代表全局属性
     * @var array
     */
    private $mapping = [
        // 文件类型
        'accept'            => ['form','input'],
        // 键盘触发元素焦点
        'accesskey'         => null,
        // 支持的字符集列表
        'accept-charset'    => 'form',
        // 提交url
        'action'            => 'form',
        // 元素的水平对齐方式
        'align'             => ['applet','caption','col','colgroup','hr','iframe','img','table','tbody','td','tfoot','th','thead','tr'],
        // iframe的功能策略
        'allow'             => 'iframe',
        // 图片的代替文本
        'alt'               => ['applet','area','img','input'],
        // 异步执行脚本
        'async'             => 'script',
        // 自动大写
        'autocapitalize'    => null,
        // 自动填值
        'autocomplete'      => ['form','input','textarea'],
        // 自动焦点
        'autofocus'         => ['button','input','keygen','select','textarea'],
        // 自动播放音频或视频
        'autoplay'          => ['audio', 'video'],
        // 背景颜色, 请改用CSS background-color属性。
        'bgcolor'           => ['body','col','colgroup','marquee','table','tbody','tfoot','td','th','tr'],
        // 边框宽度, 请改用CSS border属性。
        'border'            => ['img','object','table'],
        // 选中元素
        'checked'           => 'input',
        // 包含指向引用或更改源的URI。
        'cite'              => ['blockquote','del','ins','q'],
        // 样式类
        'class'             => null,
        // 文本颜色, 请改用CSS color属性
        'color'             => ['basefont','font','hr'],
        // 单元格跨越的列数, 合并单元格
        'colspan'           => ['td', 'th'],
        // 上下文的值
        'content'           => 'meta',
        // 元素的内容是否可编辑
        'contenteditable'   => null,
        // 定义<menu>将用作元素上下文菜单的元素的ID
        'contextmenu'       => null,
        // 浏览器是否应向用户显示播放控件
        'controls'          => ['audio', 'video'],
        // 一组指定热点区域坐标的值
        'coords'            => 'area',
        // 元素如何处理跨源请求
        'crossorigin'       => ['audio','img','link','script','video'],
        // 指定嵌入式文档必须同意对其自身强制执行的内容安全策略
        'csp'               => 'iframe',
        // 指定资源的URL
        'data'              => 'object',
        // 自定义属性
        'data-*'            => null,
        // 元素关联的日期和时间
        'datetime'          => ['del','ins','time'],
        // 解码图像的首选方法
        'decoding'          => 'img',
        // 启用该轨道
        'default'           => 'track',
        // 在解析页面后应执行脚本
        'defer'             => 'script',
        // 文本方向
        'dir'               => null,
        // 提交的内容书写的方向性，ltr或rtl
        'dirname'           => ['input', 'textarea'],
        // 是否禁用元素
        'disabled'          => ['button','command','fieldset','input','keygen','optgroup','option','select','textarea'],
        // 超链接用于下载资源
        'download'          => ['a', 'area'],
        // 定义是否可以拖动元素
        'draggable'         => null,
        // 表示该元素接受删除其中的内容
        'dropzone'          => null,
        // 定义methodPOST 时表单日期的内容类型
        'enctype'           => 'form',
        // 属于这个的元素
        'for'               => ['a', 'area'],
        // 指示作为元素所有者的表单
        'form'              => ['button','fieldset','input','keygen','label','meter','object','output','progress','select','textarea'],
        // 指示元素的操作
        'formaction'        => ['input', 'button'],
        // 适用于此元素的元素的ID
        'headers'           => ['td', 'th'],
        // 元素的高度, 对于所有其他元素，请使用CSS height属性。
        'height'            => ['canvas','embed','iframe','img','input','object','video'],
        // 隐藏元素
        'hidden'            => null,
        // 上限范围的下限
        'high'              => 'meter',
        // 链接资源的URL
        'href'              => ['canvas','embed','iframe','img','input','object','video', 'a'],
        // 指定链接资源的语言
        'hreflang'          => ['a','area','link'],
        // 定义pragma指令
        'http-equiv'        => 'meta',
        // 该属性的值必须是唯一
        'id'                => null,
        // 指示资源的相对提取优先级
        'importance'        => ['iframe','img','link','script'],
        // 安全功能，允许浏览器验证他们获取的内容
        'integrity'         => ['link', 'script'],
        // 表示图像是服务器端图像映射的一部分
        'ismap'             => 'img',
        // 包含名称和值对
        'itemprop'          => null,
        // 指定文本轨道的类型
        'kind'              => 'track',
        // 指定文本轨道的用户可读标题
        'label'             => 'track',
        // 定义元素中使用的语言
        'lang'              => null,
        // 定义元素中使用的脚本语言
        'language'          => 'script',
        // 是否应该懒惰地加载元素
        'lazyload'          => ['img', 'iframe'],
        // 预定义选项列表
        'list'              => 'input',
        // 是否重播
        'loop'              => ['audio','bgsound','marquee','video'],
        // 较低范围的上限
        'low'               => 'meter',
        // 指定文档缓存清单的URL
        'manifest'          => 'html',
        // 表示允许的最大值
        'max'               => ['input','meter','progress'],
        // 允许的最大字符数
        'maxlength'         => ['input', 'textarea'],
        // 允许的最小字符数
        'minlength'         => ['input', 'textarea'],
        // 指定为其设计链接资源的媒体的提示 - 未添加方法
        'media'             => ['a','area','link','source','style'],
        // 提交表单时要使用的HTTP方法
        'method'            => 'form',
        // 允许的最小值
        'min'               => ['input', 'meter'],
        // 是否表示多个值可以在类型的输入侧来输入侧email或file
        'multiple'          => ['input', 'select'],
        // 指示音频在页面加载时是否最初静音
        'muted'             => ['audio', 'video'],
        // 元素的名称
        'name'              => ['button','form','fieldset','iframe','input','keygen','object','output','select','textarea','map','meta','param'],
        // 此属性表示在提交时不应验证表单
        'novalidate'        => 'form',
        // 当前是否可见
        'open'              => 'details',
        // 表示最佳数值
        'optimum'           => 'meter',
        // 定义一个正则表达式，该元素的值将被验证
        'pattern'           => 'input',
        // 包含以空格分隔的URL列表
        'ping'              => ['a', 'area'],
        // 向用户提供可在该字段中输入的内容的提示
        'placeholder'       => ['input', 'textarea'],
        // 指示在用户播放或搜索之前显示的海报框架的URL
        'poster'            => 'video',
        // 指示是否应预先加载整个资源，部分资源或任何内容
        'preload'           => ['audio', 'video'],
        // 指示是否可以编辑元素
        'readonly'          => ['input', 'textarea'],
        // 指定目标对象与链接对象的关系
        'rel'               => ['a','area','link'],
        // 指示是否需要填写此元素
        'required'          => ['input','select','textarea'],
        // 指示列表是否应按降序显示而不是按升序显示 - 未添加
        'reversed'          => 'ol',
        // 定义文本区域中的行数
        'rows'              => 'textarea',
        // 定义表格单元格应跨越的行数
        'rowspan'           => ['td', 'th'],
        // 使用某些功能
        'sandbox'           => 'iframe',
        // 定义标题所涉及的单元格
        'scope'             => 'th',
        // 不推荐 - 指定样式仅适用于其父项和子项的元素
        'scoped'            => 'style',
        // 定义将在页面加载时选择的值
        'selected'          => 'option',
        // 相关热点的形状
        'shape'             => ['a', 'area'],
        // 元素的宽度, 如果元素的type属性是text，password那么它是字符数。
        'size'              => ['input', 'select'],
        // 定义资源中包含的可视媒体的图标大小
        'sizes'             => ['link','img','source'],
        // 将阴影DOM阴影树中的插槽分配给元素
        'slot'              => null,
        'span'              => ['col', 'colgroup'],
        // 指示是否允许对元素进行拼写检查
        'spellcheck'        => null,
        // 可嵌入内容的URL
        'src'               => ['audio','embed','iframe','img','input','script','source','track','video'],
        // 包含的页面内容
        'srcdoc'            => 'iframe',
        // 轨道文本数据的语言
        'srclang'           => 'track',
        // 一个或多个响应图像候选
        'srcset'            => ['img', 'source'],
        // 编号的起始值
        'start'             => 'ol',
        'step'              => 'input',
        // CSS样式
        'style'             => null,
        // 替代文本, 允许使用盲文屏幕浏览网页
        'summary'           => 'table',
        // 覆盖浏览器的默认Tab键顺序，并遵循指定的顺序
        'tabindex'          => null,
        // 链接打开位置
        'target'            => ['a','area','base','form'],
        // 鼠标悬停元素时显示的文本
        'title'             => null,
        // 否要转换元素的属性值
        'translate'         => null,
        // 定义元素的类型
        'type'              => ['button','input','command','embed','object','script','source','style','menu'],
        // 与元素关联的图像映射的部分URL（以“＃”开头）
        'usemap'            => ['img','input','object'],
        // 页面加载时, 元素的默认值
        'value'             => ['button','option','input','li','meter','progress','param'],
        // 元素的宽度
        'width'             => ['canvas','embed','iframe','img','input','object','video'],
        // 是否应该包装文本
        'wrap'              => 'textarea',
    ];
    
    /**
     * 构造函数
     * @param array $config 元素属性
     */
    public function __construct($config = [])
    {
        foreach ($config as $attr => $val) {
            $this->setAttr($attr, $val);
        }
    }
    
    /**
     * 获取当前元素对象的名称
     * @return string
     */
    public function getName()
    {
        if ($this->name === null) {
            $class = static::class;
            $name = substr($class, strrpos($class, '\\') + 1);
            $this->name = strtolower($name);
        }
        
        return $this->name;
    }
    
    /**
     * 设置当前元素对象的名称
     * @param string $element
     * @return $this
     */
    public function setName($element)
    {
        $this->name = $element;
        return $this;
    }
    
    /**
     * 是否需闭合的元素
     * @return bool
     */
    public function isEnd()
    {
        return $this->end;
    }
    
    /**
     * 设置元素为单标签元素
     * @return $this
     */
    public function noEnd()
    {
        $this->end = false;
        return $this;
    }
    
    /**
     * 过滤元素不支持的属性
     * @param string $attr 属性名
     * @param string $element 元素名
     * @param bool $throw 是否报错, 默认`false`
     * @return bool 返回可用true, 不可用fase
     */
    public function filterAttr($attr, $element, $throw = false)
    {
        if(isset($this->mapping[$attr])) {
            if(is_array($this->mapping[$attr])) {
                $result = in_array($element, $this->mapping[$attr]);
            } else {
                $result = ($element == $this->mapping[$attr]) ? true : false;
            }
            
            
            if($throw && !$result) {
                throw new \Exception('Element Does not exist ' . $attr . ' attr');
            }
            
            return $result;
        }
        
        
        // 默认支持自定义属性
        return true;
    }
    
    /**
     * 设置元素属性
     * @param string $attr 属性名
     * @param string|bool $value 属性值, 布尔类型代表启用属性
     */
    public function setAttr($attr, $value)
    {
        // 自动将分隔符转换为属性格式.
        if (strpos($attr, '_') !== false) {
            $attr = str_replace('_', '-', $attr);
        }

        if($this->filterAttr($attr, $this->getName())) {
            $this->attr[$attr] = $value;
        }
    }
    
    /**
     * 获取元素属性
     * @param string $attr 属性名
     * @return null|mixed 返回null代表不存在.
     */
    public function getAttr($attr)
    {
        return isset($this->attr[$attr]) ? $this->attr[$attr] : null;
    }
    
    /**
     * 获取元素全部属性
     * @return array
     */
    public function getAttrs()
    {
        return $this->attr;
    }
    
    /**
     * 设置元素属性
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        // 接受未定义的属性设置
        if (array_key_exists($name, $this->mapping)) {
            $this->setAttr($name, $value);
        }
    }
    
    /**
     * 获取属性
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $this->getAttr($name);
    }
    
    /**
     * 元素对象自动转换为字符串
     * @return string
     */
    public function __toString()
    {
        return $this->build($this);
    }
    
    /**
     * 添加一行内容
     * @param mixed $content
     * @return $this
     */
    public function setContent($content)
    {
        // 闭包
        if($content instanceof \Closure) {
            $this->content[] = $content();
        } else {
            $this->content[] = $content;
        }
        
        return $this;
    }
    
    /**
     * 设置元素内容
     * @param array $content
     * @return $this
     */
    public function setContents(array $content)
    {
        $this->content = $content;
        
        return $this;
    }
    
    /**
     * 返回元素所有内容
     * @return array
     */
    public function getCountents()
    {
        return $this->content;
    }
     
    /**
     * 设置键盘按键来获得元素焦点
     * @param string $value 按键
     * @return $this
     */
    public function accesskey($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        
        return $this;
    }
    
    /**
     * 指定元素的水平对齐方式
     * - 元素'caption','col','colgroup','hr','iframe','img','table','tbody','td','tfoot','th','thead','tr'
     * - 请使用`float` 或 `vertical-align`代替它.
     * @param string $value 对齐方式, 可能的值:
     * - `top` : 相当于vertical-align: top;或vertical-align: text-top;
     * - `middle` : 相当于 vertical-align: -moz-middle-with-baseline;
     * - `bottom` : 缺失值默认值，相当于vertical-align: unset;或vertical-align: initial;
     * - `left` : 相当于 float: left;
     * - `right` : 相当于 float: right;
     */
    public function align($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        
        return $this;
    }
    
    /**
     * 文本自动大写, 输入或编辑时
     * 
     * - iOS Safari Mobile使用的非标准属性
     * 
     * @param string $value 可能的值:
     * - `none` : 完全禁用自动大写
     * - `sentences` : 默认值, 自动将句子的第一个字母大写。
     * - `words` : 自动大写单词的第一个字母
     * - `characters` : 自动大写所有字符
     * @return $this
     */
    public function autocapitalize($value = true)
    {
        $this->setAttr(__FUNCTION__, $value);
        
        return $this;
    }
    
    /**
     * 背景颜色 - 遗留属性, 用css代替
     * @param string $value 16进制颜色值
     * @return $this
     */
    public function bgcolor($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 设置样式类
     * @param string $value
     * @return $this
     */
    public function class($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 元素的内容是否可编辑
     * @param bool $value
     * @return $this
     */
    public function contenteditable($value = true)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 定义<menu>将用作元素上下文菜单的元素的ID
     * @param string $value
     * @return $this
     */
    public function contextmenu($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * data-* 自定义属性
     * @param string $name 属性名
     * @param string $value 值
     * @return $this
     */
    public function data($name, $value)
    {
        $this->setAttr('data-' . $name, $value);
        return $this;
    }
    
    /**
     * 元素关联的日期和时间
     * @param string $value
     * @return $this
     */
    public function datetime($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 文本方向
     * @param string $value 可能的值:
     * - `ltr` 从左到右
     * - `rtl` 从右到左
     * @return $this
     */
    public function dir($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 禁用元素
     * @param bool $value
     * @return $this
     */
    public function disabled($value = true)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 是否可以拖动元素
     * @param bool $value
     * @return $this
     */
    public function draggable($value = true)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 接受删除其中的内容
     * @param string $value 可能的值:
     * - `copy` : 表示drop将创建被拖动元素的副本
     * - `move` : 表示拖动的元素将移动到此新位置
     * - `link` : 将创建拖动数据的链接
     * @return $this
     */
    public function dropzone($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 设置元素所属的表单
     * @param string $value 表单名
     * @return $this
     */
    public function form($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 元素的高度, 对于所有其他元素，请使用CSS height属性
     * @param string $value
     * @return $this
     */
    public function height($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 隐藏元素
     * @param bool $value
     * @return $this
     */
    public function hidden($value = true)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 该属性的值必须是唯一的
     * @param string $value
     * @return $this
     */
    public function id($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 用于向项添加属性
     * @param string $value 包含名称和值对
     * @return $this
     */
    public function itemprop($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 定义元素中使用的语言
     * @param string $value 
     * @return $this
     */
    public function lang($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 元素的名称
     * @param string $value 包含名称和值对
     * @return $this
     */
    public function name($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 将阴影DOM阴影树中的插槽分配给元素
     * @param string $value
     * @return $this
     */
    public function slot($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 是否可以检查元素是否存在拼写错误
     * @param bool $value
     * @return $this
     */
    public function spellcheck($value = true)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 行内CSS样式
     * @param string $value
     * @return $this
     */
    public function style($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 覆盖浏览器的默认Tab键顺序，并遵循指定的顺序
     * @param string $value
     * @return $this
     */
    public function tabindex($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 鼠标悬停元素时显示的文本
     * @param string $value
     * @return $this
     */
    public function title($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 否要转换元素的属性值
     * @param string $value
     * @return $this
     */
    public function translate($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 元素的类型
     * @param string $value
     * @return $this
     */
    public function type($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
}
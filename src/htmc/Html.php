<?php
namespace qpf\htmc;

use qpf;

/**
 * HTML代码生成器
 */
class Html
{
    /**
     * 属性名称验证
     * @var string
     */
    public static $attributeRegex = '/(^|.*\])([\w\.\+]+)(\[.*|$)/u';
    /**
     * 空元素列表
     * @var array
     */
    public static $voidElements = [
        'area' => 1,
        'base' => 1,
        'br' => 1,
        'col' => 1,
        'command' => 1,
        'embed' => 1,
        'hr' => 1,
        'img' => 1,
        'input' => 1,
        'keygen' => 1,
        'link' => 1,
        'meta' => 1,
        'param' => 1,
        'source' => 1,
        'track' => 1,
        'wbr' => 1,
    ];
    /**
     * 属性排序
     * @var array
     */
    public static $attributeOrder = [
        'type',
        'id',
        'class',
        'name',
        'value',
        
        'href',
        'src',
        'srcset',
        'form',
        'action',
        'method',
        
        'selected',
        'checked',
        'readonly',
        'disabled',
        'multiple',
        
        'size',
        'maxlength',
        'width',
        'height',
        'rows',
        'cols',
        
        'alt',
        'title',
        'rel',
        'media',
    ];
    /**
     * 自定义属性支持列表
     * ```
     * $data = `['name' => 'xyz', 'age' => 13]`
     * //  `data-name="xyz" data-age="13"`
     * ```
     * @var array 当设置的属性值为数组时, 将逐个生成
     */
    public static $dataAttributes = ['data', 'data-ng', 'ng'];
    
    /**
     * 生成HTML标签
     * @param string $name 标签名, 设置为`null`或`false`将直接返回内容
     * @param string $content 标签内容
     * - 安全提示: 若是用户提供的内容, 应该考虑html实体编码, 来防止XSS攻击.
     * @param array $options 标签属性, 以数组的格式`属性名 => 属性值`,
     * 属性值为`null`将不会显示, 属性值会进行HTML编码.
     * @return string
     */
    public static function tag($name, $content = '', $options = [])
    {
        if ($name === null || $name === false) {
            return $content;
        }
        $html = "<$name" . static::renderTagAttributes($options) . '>';
        return isset(static::$voidElements[strtolower($name)]) ? $html : "$html$content</$name>";
    }
    
    /**
     * 开始标签
     * @param string $name 标签名
     * @param array $options 标签属性
     * @return string
     */
    public static function beginTag($name, $options = [])
    {
        if ($name === null || $name === false) {
            return '';
        }
        
        return "<$name" . static::renderTagAttributes($options) . '>';
    }
    
    /**
     * 结束标签
     * @param string $name 标签名
     * @return string
     */
    public static function endTag($name)
    {
        if ($name === null || $name === false) {
            return '';
        }
        
        return "</$name>";
    }
    
    /**
     * 生成style标签
     * @param string $content CSS代码
     * @param array $options 标签属性
     * @return string
     */
    public static function style($content, $options = [])
    {
        return static::tag('style', $content, $options);
    }
    
    /**
     * 生成script标签
     * @param string $content js代码
     * @param array $options 标签属性
     * @return string
     */
    public static function script($content, $options = [])
    {
        return static::tag('script', $content, $options);
    }
    
    /**
     * 生成引用外部CSS文件的link标签
     * @param string $url 外部CSS文件的URL
     * @param array $options 标签属性, 可用的功能选项:
     * - condition : 指定IE的条件注释，例如`lt IE 9`
     * - noscript : 如果设置为true，`link`标签将被包装到`<noscript>`标签中
     * @return string
     */
    public static function cssFile($url, $options = [])
    {
        if (!isset($options['rel'])) {
            $options['rel'] = 'stylesheet';
        }
        // TODO: Url::to($url)
        $options['href'] = $url;
        
        if (isset($options['condition'])) {
            $condition = $options['condition'];
            unset($options['condition']);
            return self::wrapIntoCondition(static::tag('link', '', $options), $condition);
        } elseif (isset($options['noscript']) && $options['noscript'] === true) {
            unset($options['noscript']);
            return '<noscript>' . static::tag('link', '', $options) . '</noscript>';
        }
        
        return static::tag('link', '', $options);
    }
    
    /**
     * 生成引用外部JS文件的script标签
     * @param string $url 外部JS文件的URL
     * @param array $options 标签属性, 可用的功能选项:
     * - condition : 指定IE的条件注释，例如`lt IE 9`
     * @return string
     */
    public static function jsFile($url, $options = [])
    {
        // TODO: Url::to($url)
        $options['src'] = $url;
        if (isset($options['condition'])) {
            $condition = $options['condition'];
            unset($options['condition']);
            return self::wrapIntoCondition(static::tag('script', '', $options), $condition);
        }
        
        return static::tag('script', '', $options);
    }
    
    /**
     * 将内容包含在IE的条件注释中 - `lt IE 9`
     * @param string $content 原始HTML内容
     * @param string $condition 条件字符串
     * @return string
     */
    private static function wrapIntoCondition($content, $condition)
    {
        if (strpos($condition, '!IE') !== false) {
            return "<!--[if $condition]><!-->\n" . $content . "\n<!--<![endif]-->";
        }
        
        return "<!--[if $condition]>\n" . $content . "\n<![endif]-->";
    }
}
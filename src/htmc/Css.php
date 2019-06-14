<?php
namespace qpf\htmc;

use qpf;

/**
 * Css 类代表一个元素的样式描述对象
 *
 * -----------------------------------
 * 使用[Css::classBuild()]方法来指定样式名, 并使用css属性集合[Css::attr()]来选择多个css属性到样式中.
 * 
 * 例如：
 * # 创建CSS样式类.demo，即属性为`width: 100px; height: 100px;`
 * ~~~~
 * Css::classBuild('.demo', 
 *      Css::attr()->layout()->width('100px'),
 *      Css::attr()->layout()->height('100px'));
 * 返回的字符串为:
 * .demo{
 * width: 100px;
 * height: 100px;
 * }
 * ~~~
 * 
 * @author qiun
 *        
 */
class Css
{
    /**
     * css属性生成器
     * @var \qpf\htmc\css\CssBuilder
     */
    private static $cssBuilder;
    
    /**
     * css属性集合
     * @return \qpf\htmc\css\CssBuilder
     */
    static public function attr()
    {
        if (is_null(self::$cssBuilder)) {
            self::$cssBuilder = new \qpf\htmc\css\CssBuilder();
        }
        return self::$cssBuilder;
    }

    /**
     * 转换参数为像素单位
     *
     * @param string|integer $value
     */
    static public function px($value)
    {
        if (is_numeric($value) && $value == intval($value)) {
            return trim($value) . 'px';
        } elseif (is_string($value) && $value !== '') {
            return $value;
        } else {
            return '0';
        }
    }
 
    /**
     * 生成样式类
     * 
     * - 即`name{...}`格式, 花括号最后有换行符
     * - 直接使用时, 可无限参数模式
     * - 被二次调用时将`func_get_args()`数组作为参数传入即可
     * 
     * 使用示例:
     * ~~~
     * Css::classBuild('')  / Css::classBuild(null) >> ''
     * Css::classBuild('.demo') >> '.demo{}'
     * # '.demo{width: 100px;}'
     * Css::classBuild('.demo', 'width: 100px;');
     * Css::classBuild('.demo',  Css::attr()->layout()->width('100px'));
     * # 无样式名时,属性不会被花括号包裹: `width: 100px;`
     * Css::classBuild(null, 'width: 100px;');
     * ~~~
     * 
     * @param string|array $css 样式名
     * - null : 当样式名为`null`时, css属性不会被`{}`包裹
     * - array : 数组类型[`样式名`, 'css属性' ...]
     * @param string 可接收多个css属性样式参数
     * @return string
     */
    static public function classBuild($css)
    {
        // 参数传递为数组
        if (is_array($css)) {
            $arr = $css;
        } else {
            // 接收全部的参数到数组
            $arr = func_get_args();
        }
        
        // 接收结果的变量
        $string = '';
        
        // 获得样式名
        if (!empty($arr[0])) {
            $className = $arr[0];
            unset($arr[0]);
            
            $string .=   $className . '{' . PHP_EOL;
        }

        // 判断, 最少设置了一条css属性样式
        if (count($arr) > 0) {
            foreach ($arr as $i => $val) {
                $string .= $val . PHP_EOL;
            }
        }
        
        // 设置了样式名, 追加结束符
        if (isset($className)) {
            $string .= '}' . PHP_EOL;
        }
        
        return $string;
    }


}
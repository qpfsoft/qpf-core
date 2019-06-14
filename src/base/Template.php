<?php
namespace qpf\base;

use qpf;

/**
 * 模板渲染
 *
 * ```
 * # 渲染对象类型的参数模板, 即 $this->var 参数模式
 * echo \qpf\core\Template::render_object(['title' => '标题'], '@qpf/tpl/obj.php');
 * # 渲染参数模板, 即 $var 参数模式
 * echo \qpf\core\Template::render_var(['title' => '标题'], '@qpf/tpl/var.php');
 * # 渲染HTML代码
 * $params = ['title' => '标题'];
 * $html = '<h1>文本模板: $title</h1>';
 * echo \qpf\core\Template::render_html($params, $html);
 * 或
 * echo \qpf\core\Template::render_html(['title' => '标题'], '<h1>文本模板: $title</h1>');
 * ```
 */
class Template extends Core
{

    /**
     * 渲染参数模板
     *
     * 模板参数操作格式`$var`
     *
     * @param array $params 关联数组, 模板变量值
     * @param string $tpl 模板文件, 支持路径别名
     * @return string 返回渲染结果
     */
    public static function render_var($params, $tpl)
    {
        ob_start();
        ob_implicit_flush(false);
        extract($params, EXTR_OVERWRITE);
        include (QPF::getAlias($tpl));
        
        return ob_get_clean();
    }

    /**
     * 渲染对象类型的参数模板
     *
     * 模板参数操作格式`$this->var`
     *
     * @param object|array $context 参数对象,
     * - 数组将自动转换为qpf\core\ArrayObject.
     * - PHP内置的ArrayObject和stdClass无法使用.
     * @param string $tpl 模板文, 支持路径别名
     * @return void 直接渲染到游览器
     */
    public static function render_object($object, $tpl)
    {
        /*@var Closure $closure */
        $closure = function ($tpl) {
            ob_start();
            ob_implicit_flush(false);
            include (QPF::getAlias($tpl));
            return ob_get_clean();
        };
        
        if (is_array($object)) {
            $object = new ArrayObject($object);
        }
        
        // 创建并返回一个 匿名函数, 绑定对象属性
        $closure = $closure->bindTo($object, $object);
        // 执行回调函数
        return $closure($tpl);
    }

    /**
     * 渲染HTML代码
     * @param array $params 关联数组, 模板变量值
     * @param string $html 字符串模板, 包含html和php模板变量, 单引号类型字符串.
     * @return string 返回渲染结果
     */
    public static function render_html($params, $html)
    {
        if (is_string($html)) {
            ob_start();
            extract($params, EXTR_OVERWRITE);
            /*
             * 编译字符串
             * - 即将单引号字符串作为双引号的运行效果.
             * - 通过赋值的方式, 编译一次字符串.
             */
            eval("\$html = \"$html\";");
            echo $html;
            return ob_get_clean();
        }
        
        return '模板内容必须为字符串类型.';
    }
}
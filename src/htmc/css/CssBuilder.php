<?php
namespace qpf\htmc\css;

use qpf\htmc\css\attr\Text;
use qpf\htmc\css\attr\Border;
use qpf\htmc\css\attr\Background;
use qpf\htmc\css\tools\Tools;
use qpf\htmc\css\attr\Table;
use qpf\htmc\css\attr\Css3;
use qpf\htmc\css\attr\Css2;
use qpf\htmc\css\attr\Webkit;
use qpf\htmc\css\attr\FlexBox;
use qpf\htmc\css\attr\Transform;
use qpf\htmc\css\attr\IE;
use qpf\htmc\css\attr\Layout;
use qpf\htmc\css\attr\Transition;

/**
 * CSS样式生成器
 *
 * 前缀:
 * -moz代表firefox浏览器私有属性
 * -ms代表ie浏览器私有属性
 * -webkit代表safari、chrome私有属性
 * IE css hack:
 * ie6: `_` 和 `*`
 * ie7: `*`
 * 
 * @author qiun
 *        
 */
class CssBuilder
{

    /* css hack前缀  */
    const IE6_Hack = '_';
    const IE67_Hack = '*';
    
    /**
     * 元素选择器
     * @return \qpf\htmc\css\Selector
     */
    public static function query()
    {
        return new Selector();
    }

    /**
     * 布局
     *
     * 功能:
     * - 宽高,显示类型,边距,填充,定位,浮动,溢出,隐藏,透明;
     * 
     * @return \qpf\htmc\css\attr\Layout
     */
    public static function layout()
    {
        return new Layout();
    }

    /**
     * 文本
     *
     * 功能:
     * - 颜色,大小,字体,对齐,行高,间距,换行,英文换行;
     * - 下划线,阴影,英文大小写,图文排版,文本溢出;
     * - ul列表符号,符号类型,图片符号;
     * - 垂直对齐,
     * 
     * @return \qpf\htmc\css\attr\Text
     */
    public static function text()
    {
        return new Text();
    }

    /**
     * 边框
     *
     * 功能:
     * - 设置边框样式,粗细,颜色,
     * - 圆角, 表格边框合并, 表格单元格间距
     * - 轮廓描边，在边看的外边不占用占用体积
     * 
     * @return \qpf\htmc\css\attr\Border
     */
    public static function border()
    {
        return new Border();
    }

    /**
     * 表格
     *
     * 功能:
     * - 表格显示算法，单元格无内容时显示边框
     * 
     * @return \qpf\htmc\css\attr\Table
     */
    public static function table()
    {
        return new Table();
    }

    /**
     * 背景
     *
     * 功能:
     * - 背景颜色, 渐变颜色
     * - 图片背景: 设置,定位,固定,大小,区域,重复,
     * - 背景阴影, 渐变背景颜色
     * - rgba颜色属性值代码段
     * 
     * @return \qpf\htmc\css\attr\Background
     */
    public static function background()
    {
        return new Background();
    }
    
    /**
     * 伸缩盒子
     * @return \qpf\htmc\css\attr\FlexBox
     */
    public static function flexBox()
    {
        return new FlexBox();
    }
    
    /**
     * 元素变换形状 - 扭曲
     * @return \qpf\htmc\css\attr\Transform
     */
    public static function transform()
    {
        return new Transform();
    }
    
    /**
     * css3 过度动画效果
     */
    public static function transition()
    {
        return new Transition();
    }
    
    /**
     * 动画效果
     */
    public function aniation()
    {
        
    }
    
    /**
     * 媒体查询
     */
    public function mediaQueries()
    {
        
    }
    
    /**
     * IE 私有属性
     * @return \qpf\htmc\css\attr\IE
     */
    public static function onlyIE(){
        return new IE();
    }
    
    public static function onlyFirefox(){}
    
    public static function onlyWebkit(){}

    /**
     * css 未归类的属性
     * 
     * - content属性
     * - 鼠标图标
     * @return \qpf\htmc\css\attr\Css2
     */
    public static function css2()
    {
        return new Css2();
    }

    /**
     * css3 兼容性一般的属性
     * 
     * - 服务器字体，渐变颜色，禁止选中文本
     * - 多列排版
     * @return \qpf\htmc\css\attr\Css3
     */
    public static function css3()
    {
        return new Css3();
    }

    /**
     * webkit私有属性
     *
     * 功能:
     * - 不兼容: IE, Firefox
     * - 文字填充颜色,文字描边
     * - 渐变色
     * 
     * @return \qpf\htmc\css\attr\Webkit
     */
    public static function webkit()
    {
        return new Webkit();
    }

    /**
     * CSS代码段工具 - 计算生成代码
     *
     * 功能:
     * - 生成垂直居中的定位
     * 
     * @return \qpf\htmc\css\tools\Tools
     */
    public static function tools()
    {
        return new Tools();
    }

}
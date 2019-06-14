<?php
namespace qpf\htmc\html;

/**
 * Area 标签定义图像映射中的区域（注：图像映射指得是带有可点击区域的图像）。
 * 
 * - area 元素总是嵌套在 <map> 标签中, 用来设置图片上的热点.
 * - <img> 中的 usemap 属性可引用 <map> 中的 id 或 name 属性（由浏览器决定），
 * 所以我们需要同时向 <map> 添加 id 和 name 两个属性。
 * 
 * 语法结构:
 * <area   
 * class=type   
 * id＝Value    
 * href＝url    
 * alt＝text    // 必须设置的属性
 * shape＝area-shape // 形状
 * coods＝value>  // 大小
 * 
 * 示例
 * ~~~
 * # 表示设定热点的形状为矩形，左上角顶点坐标为（X1,y1），右下角顶点坐标为（X2,y2）。
 * <area shape="rect" coords="x1, y1,x2,y2" href=url>
 * 
 * # 表示设定热点的形状为圆形，圆心坐标为（X1,y1），半径为r。
 * <area shape="circle" coords="x1, y1,r" href=url>
 * 
 * # 表示设定热点的形状为多边形，各顶点坐标依次为（X1,y1）、（X2,y2）、（x3,y3） ......。
 * <area shape="poligon" coords="x1, y1,x2,y2 ......" href=url>
 * ~~~
 * 
 * @author qiun
 *
 */
class Area extends HtmlBase
{
    /**
     * 标签名称
     * @var string
     */
    protected $tagName = 'area';
    
    /**
     * 定义此区域的替换文本, 必须属性
     * @param string $value 区域描述
     * @return $this
     */
    public function alt($value)
    {
        $this->attr(['alt' => $value]);
        return $this;
    }
    
    /**
     * 定义可点击区域（对鼠标敏感的区域）的坐标
     * @param string $value 坐标值
     * @return $this
     */
    public function coords($value)
    {
        $this->attr(['coords' => $value]);
        return $this;
    }
    
    /**
     * 定义此区域的目标 URL
     * @param string $value URL
     * @return $this
     */
    public function href($value)
    {
        $this->attr(['href' => $value]);
        return $this;
    }
    
    /**
     * 从图像映射排除某个区域
     * @param string $value nohref
     * @return $this
     */
    public function nohref($value)
    {
        $this->attr(['nohref' => $value]);
        return $this;
    }
    
    /**
     * 定义区域的形状
     * @param string $value 可能的值:
     * - default : 规定全部区域。
     * - rect : 定义矩形区域。
     * - circ : 定义圆形。
     * - poly : 定义多边形区域。
     * @return $this
     */
    public function shape($value)
    {
        $this->attr(['shape' => $value]);
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
}
<?php
namespace qpf\htmc\html;

/**
 * Canvas 画布标签 - h5
 * 
 * - <canvas> 是一个可用于使用脚本（通常是JavaScript）绘制图形的HTML元素。
 * - 通过重设 width 或 height 属性来清空画布（使用 JavaScript）。
 * 
 * ~~~
 * var c=document.getElementById("myCanvas");
 * 
 * // 清空画布
 * function clearCanvas()
 * {
 *   c.height=c.height;
 * }
 * ~~~
 * 
 * 教程:
 * 
 * 前言:
 * 所有主流浏览器的最新版本中都支持该元素。画布的默认大小为300像素×150像素（宽×高）。
 * 
 * @author qiun
 *
 */
class Canvas extends HtmlBase
{
    /**
     * 标签名称
     * @var string
     */
    protected $tagName = 'canvas';
    
    /**
     * 设置 canvas 的高度
     * @param string $value 高度（比如 "100px" 或仅仅是 "100"）。默认是 "150"
     * @return $this
     */
    public function height($value)
    {
        $this->attr(['height' => $value]);
        return $this;
    }
    
    /**
     * 设置 canvas 的宽度
     * @param string $value 宽度（比如 "100px" 或仅仅是 "100"）。默认是 "150"。
     * @return $this
     */
    public function width($value)
    {
        $this->attr(['width' => $value]);
        return $this;
    }
}
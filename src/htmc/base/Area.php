<?php
namespace qpf\htmc\base;

/**
 * Area 图片上的热点
 */
class Area extends Element
{
    /**
     * 图片的替代文本
     * @param string $value 代替文字
     * @return $this
     */
    public function alt($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 一组指定热点区域坐标的值
     * @param string $value 值的数量和含义取决于为shape属性指定的值
     * - `rect` 矩形, 值是 left，top，right，bottom
     * - `circle` 圆, 值是 x,y,r 其中 x,y 是圆心, r是半径
     * - `poly` 多边形, 值是 每个点一组, x1,y1,x2,y2,x3,y3,依此类推
     * @return $this
     */
    public function coords($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 链接资源的URL
     * @param string $value
     * @return $this
     */
    public function href($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 指定链接资源的语言
     * @param string $value
     * @return $this
     */
    public function hreflang($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 包含以空格分隔的URL列表
     * @param string $value
     * @return $this
     */
    public function ping($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 指定目标对象与链接对象的关系
     * @param string $value
     * @return $this
     */
    public function rel($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 相关热点的形状
     * @param string $value
     * @return $this
     */
    public function shape($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 显示链接URL的位置
     * @param string $value 可能的值:
     * - `_self` : 默认, 当前页面
     * - `_blank` : 新页面
     * - `_parent` : 父页面
     * - `_top` : 顶级窗口
     * @return $this
     */
    public function target($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
}
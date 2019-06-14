<?php
namespace qpf\htmc\base;

class HtmcBase
{
    /**
     * 返回一个新的a元素
     * @return \qpf\htmc\base\A
     */
    public function a()
    {
        return new A();
    }
    
    /**
     * 返回一个新的img元素
     * @return \qpf\htmc\base\Img
     */
    public function img()
    {
        return new Img();
    }
    
    /**
     * 返回一个新的表格生成器
     * @return \qpf\htmc\build\Table
     */
    public function table()
    {
        return new \qpf\htmc\build\Table();
    }
}
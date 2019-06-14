<?php
namespace qpf\htmc;

use qpf\htmc\base\HtmcBase;

class Element extends HtmcBase
{
    /**
     * 创建指定元素对象 - 成对标签元素
     * @param string $name 元素名, 即标签名
     * @param string $config 元素属性集合
     * @return \qpf\htmc\base\Element
     */
    public function get($name, $config = [])
    {
        $e = new \qpf\htmc\base\Element($config);
        return $e->setName($name);
    }
    
    /**
     * 创建指定元素对象 - 单标签元素
     * @param string $name 元素名, 即标签名
     * @param string $config 元素属性集合
     * @return \qpf\htmc\base\Element
     */
    public function getTag($name, $config = [])
    {
        $e = new \qpf\htmc\base\Element($config);
        $e->noEnd();
        return $e->setName($name);
    }
}
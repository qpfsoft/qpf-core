<?php
namespace qpf\htmc\base;

/**
 * Meta 元素
 */
class Meta extends Element
{
    /**
     * 声明页面的字符编码
     * - 不得使用CESU-8，UTF-7，BOCU-1和/或SCSU作为跨网站脚本与这些编码攻击已被证明
     * - 不应该使用，UTF-32因为并非所有HTML5编码算法都可以区分它UTF-16
     * @param string $value 代替文字
     * @return $this
     */
    public function charset($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 与上下文关联http-equiv或name依赖于上下文的值
     * @param string $value
     * @return $this
     */
    public function content($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 定义pragma指令
     * @param string $value
     * @return $this
     */
    public function http_equiv($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
}
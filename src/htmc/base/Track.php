<?php
namespace qpf\htmc\base;

/**
 * Audio 音频元素
 */
class Track extends Element
{
    /**
     * 表示应启用该轨道，除非用户的首选项指示不同的内容。
     * @param bool $value
     * @return $this
     */
    public function default($value = true)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 指定文本轨道的类型
     * @param string $value
     * @return $this
     */
    public function kind($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 指定文本轨道的用户可读标题
     * @param string $value
     * @return $this
     */
    public function label($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 可嵌入内容的URL
     * @param string $value
     * @return $this
     */
    public function src($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 轨道文本数据的语言
     * @param string $value
     * @return $this
     */
    public function srclang($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
}
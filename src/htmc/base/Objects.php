<?php
namespace qpf\htmc\base;

/**
 * Audio 音频元素
 */
class Objects extends Element
{
    /**
     * 元素名称
     * @var string
     */
    protected $name = 'Object';
    
    /**
     * 边框宽度 - 遗留属性, 用css代替
     * @param string $value 宽度
     * @return $this
     */
    public function border($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        
        return $this;
    }
    
    /**
     * 指定资源的URL
     * @param string $value
     * @return $this
     */
    public function dataUrl($value)
    {
        // 防止与全局自定义属性冲突
        $this->setAttr('data', $value);
        return $this;
    }
    
    /**
     * 元素的宽度
     * @param string $value
     * @return $this
     */
    public function width($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
}
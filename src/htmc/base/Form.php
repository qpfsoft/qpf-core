<?php
namespace qpf\htmc\base;

/**
 * Form 元素
 *
 */
class Form extends Element
{
    /**
     * 服务器接受的类型列表 - HTML5中已删除
     * @param string $value 服务器接受的以逗号分隔的内容类型列表
     * @return $this
     */
    public function accept($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 支持的字符集列表
     * @param string $value 服务器接受的以空格或逗号分隔的字符编码列表
     * @return $this
     */
    public function accept_charset($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 处理通过表单提交的信息的程序的URI
     */
    public function action($url)
    {
        $this->setAttr(__FUNCTION__, $url);
        return $this;
    }
    
    /**
     * 是否可以由浏览器自动完成其值
     * @param string $value 可能的值:
     * - `off` : 浏览器不会自动完成输入
     * - `on` : 浏览器可以根据用户先前在表单中输入的值自动完成值
     * @return $this
     */
    public function autocomplete($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 提交的内容类型
     * @param string $value 可能的值:
     * - `application/x-www-form-urlencoded` 如果未指定属性，则为默认值
     * - `multipart/form-data` 用于属性设置为`file`的`input`元素的值type
     * - `text/plain` 
     * @return $this
     */
    public function enctype($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 提交表单时要使用的HTTP方法
     * @param string $value 可以是GET（默认）或POST
     * @return $this
     */
    public function method($value)
    {
        $this->setAttr(__FUNCTION__, $value);
        return $this;
    }
    
    /**
     * 提交时不验证表单
     * @param bool $value
     * @return $this
     */
    public function novalidate($value = true)
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
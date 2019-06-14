<?php
namespace qpf\htmc\build;

/**
 * Html 生成器基础
 */
class Builder
{
    /**
     * 样式类名
     * @var array
     */
    protected $class = [];
    
    /**
     * 添加样式类
     * @param string $name 类名
     * @return $this
     */
    public function addClass($name)
    {
        if($name instanceof \Closure) {
            $this->class[] = call_user_func($name, $this);
        } else {
            $this->class[] = $name;
        }
        
        return $this;
    }
    
    /**
     * 移除指定或全部的样式类
     * @param string $name 类名
     * @return $this
     */
    public function removeClass($name = null)
    {
        if($name === null) {
            $this->class = [];
        } else {
            $key = array_search($name, $this->class);
            if($key !== false) {
                unset($this->class[$key]);
            }
        }
        return $this;
    }
    
    /**
     * 存在删除或不存在添加一个类
     * @param string $name 类名
     * @return $this
     */
    public function toggleClass($name)
    {
        $key = array_search($name, $this->class);
        
        if($key === false) {
            $this->addClass($name);
        } else {
            unset($this->class[$key]);
        }
        return $this;
    }

    /**
     * 使用空格拼接变量
     * @param string ...$val
     * @return string
     */
    protected function spliceBlank(... $val)
    {
        $result = '';
        foreach ($val as $var) {
            $result .= ' ' . $var;
        }
        
        return $result;
    }
}
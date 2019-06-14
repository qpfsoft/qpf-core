<?php
namespace qpf\htmc\input;

/**
 * Option 类用于设置input元素属性来生成option配置
 *
 * 在Input对象中快捷调用各种类型元素时，需要设置能多属性时，
 * 需要传递option['属性名'=>'值']数组，该对象用于快捷生成配置数组。
 *
 * 目前只提供常用的设置，如果未提供[[attr]]方法进行数组添加。
 * 该类配合[[Input]]对象使用更佳，需设置属性过多建议直接调用指定类型元素的对象进行创建。
 *
 * @author qiun
 */
class Option
{

    /**
     * input元素配置
     * 
     * @var array
     */
    public $config = [];

    /**
     * 要检测的元素类型对象名
     * 
     * @var string
     */
    private $_isType;

    /**
     * 添加属性设置
     * 
     * @param array $arr
     *            采用数组格式
     */
    public function attr($arr = [])
    {
        $this->config = array_merge($this->config, $arr);
        return $this;
    }

    /**
     * 设置类型并忽略该元素不支持的属性
     *
     * @param string $type
     *            input类型
     * @return $this
     */
    public function isType($type)
    {
        if (is_string($type) && ! empty($type)) {
            $this->_isType = '\\qpf\\htmc\\input\\' . ucwords($type);
        }
        
        return $this;
    }

    /**
     * 过滤指定类型元素不支持的属性
     */
    private function checkType()
    {
        if ($this->_isType === null) {
            return;
        }
        // 删除无效属性设置
        foreach ($this->config as $name => $value) {
            if (! property_exists($this->_isType, $name)) {
                unset($this->config[$name]);
            }
        }
    }

    /**
     * 返回option配置数组并重置对象
     *
     * @return array
     */
    public function create()
    {
        $this->checkType();
        $array = $this->config;
        $this->config = [];
        $this->_isType = null;
        return $array;
    }

    /**
     * 设置CSS样式`class = ""`属性
     *
     * @param string $className
     *            样式名
     * @return $this
     */
    public function classes($className)
    {
        $this->config['class'] = $className;
        return $this;
    }

    /**
     * 未输入内容时显示的提示
     * 
     * @param string $string
     *            提示文本
     * @return $this
     */
    public function placeholder($string)
    {
        $this->config['placeholder'] = $string;
        return $this;
    }

    /**
     * 禁用元素
     * 
     * @return $this
     */
    public function disabled($val = true)
    {
        $this->config['disabled'] = $val;
        return $this;
    }

    /**
     * 设置文本框是否记录历史输入
     * 
     * @param string $val            
     * @return $this
     */
    public function autocomplete($val = true)
    {
        $this->config['autocomplete'] = $val;
        return $this;
    }
}
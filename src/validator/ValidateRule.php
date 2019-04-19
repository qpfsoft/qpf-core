<?php
namespace qpf\validator;

/**
 * ValidateRule 验证规则类
 * 
 * 该对象代表一个字段的验证规则定义
 */
class ValidateRule implements ValidateRuleInterface
{
    /**
     * 字段名称
     * @var string
     */
    public $name;
    /**
     * 字段规则
     * ```
     * [
     *      [验证方法 => 方法参数],
     * ]
     * ```
     * @var array
     */
    public $rule;
    
    /**
     * 构造函数
     * @param string $name 字段名称
     * @param string|array $rule 验证规则
     */
    public function __construct($name = null, $rule = null)
    {
        $this->name = $name;
        $this->rule = $rule;
    }
    
    /**
     * 设置字段名称
     * @param string $name 字段名称
     * @return $this
     */
    public function name($name)
    {
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * 设置字段规则
     * @param string $rule 验证规则
     * @return $this
     */
    public function rule($rule)
    {
        $this->rule = $rule;
        
        return $this;
    }
    
    /**
     * 获取当前字段的验证规则
     * @return array
     */
    public function getRule()
    {
        return $this->parseRule($this->rule);
    }
    
    /**
     * 获取字段验证规则字符串
     * @return string
     */
    public function getRuleString()
    {
        if (!is_array($this->rule)) {
            return $this->rule;
        }
        
        $rules = [];
        foreach ($this->rule as $method => $param) {
            if ($param === null) {
                $rules[] = $method;
            } else {
                $rules[] = $method . ':' . join(',', $param);
            }
        }
        
        return join('|', $rules);
    }
    
    /**
     * 解析验证规则
     * @param string|array $rule 验证规则
     */
    protected function parseRule($rule)
    {
        if (strpos($rule, '|') !== false) {
            $rule = explode('|', $rule);
        } else {
            $rule = [$rule];
        }
        
        $rules = [];
        foreach ($rule as $index => $item) {
            list($method, $param) = $this->parseRuleItem($item);
            $rules[$method] = $param;
        }
        
        return $rules;
    }
    
    /**
     * 解析验证规则项
     * @param string $item 规则项
     * @return array
     */
    protected function parseRuleItem($item)
    {
        if (false !== strpos($item, ':')) {
            list($method, $param) = explode(':', $item);
            $param = explode(',', $param);
        } else {
            $method = $item;
            $param = null;
        }
        
        return [$method, $param];
    }

    /**
     * 追加字段的验证规则
     * @param string|array $item 验证项
     */
    public function append($item)
    {
        if (is_array($this->rule)) {
            list($method, $param) = $this->parseRuleItem($item);
            $this->rule[$method] = $param;
        } else {
            $this->rule .= '|' . $item;
        }
    }

    /**
     * 移除指定规则项
     * @param string $item 验证方法名称
     */
    public function remove($item)
    {
        if (is_array($this->rule) && key_exists($item, $this->rule)) {
            unset($this->rule[$item]);
            return $this;
        }
        
        if (strpos($this->rule, '|') !== false) {
            $rules = explode('|', $this->rule);
            foreach ($rules as $index => $value) {
                if (strpos($value, $item) === 0) {
                    unset($rules[$index]);
                }
            }
            $this->rule = join('|', $rules);
        } elseif ($this->rule == $item) {
            $this->rule = null;
        }
        
        
        return $this;
    }
    
    

    /**
     * {@inheritDoc}
     * @see \qpf\validator\ValidateRuleInterface::check()
     */
    public function check()
    {
        $rules = $this->rule;
        if (!is_array($rules)) {
            $rules = $this->parseRule($rules);
        }
        
        foreach ($rules as $method => $args) {
            $args = var_export($args, true);
            echo $method . "({$args}); <br>";
        }
    }

    
    
}
<?php
namespace qpf\validator;

/**
 * 验证器
 */
class Validate
{
    /**
     * 字段验证规则
     * @var array
     */
    public $rule = [];
    /**
     * 规则提示
     * @var array
     */
    public $ruleAlert = [];
    /**
     * 验证情景模式
     * ```
     * [
     *      'add'  => ['字段1', '字段2'],
     *      'edit' => ['id', 'title', 'content'],
     *      'del'  => ['id'],
     * ]
     * ```
     * @var array
     */
    public $use = [];
    /**
     * 当前验证情景名称
     * @var string
     */
    public $name;
    
    /**
     * 添加字段验证规则
     * @param string $name 字段名称
     * @param mixed $rule 验证规则
     * @param array $message 规则提示
     * @return void
     */
    public function addRule($name, $rule = null, array $message = [])
    {
        if ($rule instanceof ValidateRuleInterface) {
            $this->rule[$name] = $rule;
        } else {
            $this->rule[$name] = new ValidateRule($name, $rule, $message);
        }
    }
    
    /**
     * 切换验证情景模式
     * @param string $name 情景名称
     * @return $this
     */
    public function use($name)
    {
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * 获取当前情景使用的规则
     * @param string $name 情景名称
     * @return array|false
     */
    public function getUseRule($name)
    {
        if (!isset($this->use[$name])) {
            return false;
        }
        
        $rules = [];
        foreach ($this->use[$name] as $item) {
            $rules[$item] = $this->getRule($item);
        }
        
        return $rules;
    }
    
    /**
     * 操作指定字段规则
     * @param string $name 字段名称
     * @return ValidateRule|NULL
     */
    public function getRule($name)
    {
        if (isset($this->rule[$name])) {
            return $this->rule[$name];
        }
    }
    
    /**
     * 导入字段规则与提示
     * @param array $rules 规则数组
     * @param array $message 规则提示数组
     * @return void
     */
    public function import(array $rules, array $message = [])
    {
        $this->importRule($rules);
        !empty($message) && $this->importAlert($message);
    }
    
    /**
     * 导入字段验证规则
     * @param array $rules 规则数组
     * @return void
     */
    public function importRule(array $rules)
    {
        foreach ($rules as $name => $rule) {
            $this->addRule($name, $rule);
        }
    }
    
    /**
     * 导入字段规则提示
     * @param array $message
     * @return void
     */
    public function importAlert(array $message)
    {
        $this->ruleAlert = array_merge($this->ruleAlert, $message);
    }
    
    public function check(array $data, array $rules = [])
    {
        if ($this->name) {
            $rules = $this->getUseRule($this->name);
        } else {
            $rules = $rules ?: $this->rule;
        }
        
        foreach ($rules as $field => $validateRule) {
            
            if (isset($rules[$field])) {
                $value = isset($data[$field]) ? $data[$field] : null;
                $this->checkItem($field, $value, $rules[$field]);
            }
            
        }
        
    }
    
    /**
     * 验证单个字段项
     * @param string $field 字段名
     * @param mixed $value 字段值
     * @param ValidateRule $rules
     */
    public function checkItem($field, $value, ValidateRule $rule)
    {
        $rule = $rule->getRule();
        
        foreach ($rule as $method => $param) {
            
        }
    }
    

}
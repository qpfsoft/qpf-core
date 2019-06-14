<?php
namespace qpf\router;

/**
 * 路由规则组
 * 
 * - 路由规则采集器
 */
class RuleGroup
{
    /**
     * 规则请求分组
     * @var array
     */
    protected $rules = [
        'any'       => [],
        'get'       => [],
        'post'      => [],
        'put'       => [],
        'delete'    => [],
        'patch'     => [],
        'head'      => [],
        'options'   => [],
    ];
    /**
     * 额外规则
     * @var array
     */
    protected $extra = [];

    /**
     * 添加路由规则
     * @param string $rule 路由规则
     * @param mixed $match 匹配结果
     * @param string $method 请求类型,  多种用`|`分割, 不区分大小写
     * @param string $type 路由类型
     * @return Rule 返回当前路由规则操作实例
     */
    public function rule(string $rule, $match, string $method, string $type): Rule
    {
        $instance = new Rule([
            'rule'  => $rule,
            'match' => $match,
            'method' => $method,
            'type'  => $type,
        ]);

        if (strpos($method, '|') !== false) {
            $method = explode('|', $method);

            foreach ($method as $item) {
                $group = $this->getGrouping($rule, $item);
                
                if ($group) {
                    $this->rules[$item][$group][] = $instance;
                } else {
                    $this->extra[$method][] = $instance;
                    break;
                }
            }
        } else {
            $group = $this->getGrouping($rule, $method);
            
            if ($group) {
                $this->rules[$method][$group][] = $instance;
            } else {
                $this->extra[$method][] = $instance;
            }
        }
        
        return $instance;
    }
    
    /**
     * 获取并创建分组
     * @param string $rule 路由规则
     * @param string $method 请求类型
     * @param string|false 返回分组标识, 分组名为含变量规则将返回false
     */
    protected function getGrouping(string $rule, string $method): string
    {
        $group = $this->parseRuleGrouping($rule);
        
        // 分组名称不允许是变量规则, 即 动态路由
        if (strpos($group, ':') !== false || strpos($group, '<') !== false) {
            return false;
        }
        
        if (!isset($this->rules[$method][$group])) {
            $this->rules[$method][$group] = [];
        }
        
        return $group;
    }
    
    /**
     * 解析路由规则的分组名称
     * @param string $rule 路由规则
     * @return string 返回该规则的分组名称
     */
    protected function parseRuleGrouping(string $rule): string
    {
        $pos = strpos(trim($rule, '/'), '/');
        
        if ($pos !== false) {
            $group =  substr($rule, 0, $pos);
        } else {
            $group =  $rule;
        }
        
        return empty($group) ? '*' : $group;
    }
    
    public function any($rule, $match)
    {
        return $this->rule($rule, $match, 'any');
    }
    
    public function get($rule, $match)
    {
        return $this->rule($rule, $match, 'get');
    }
    
    public function post($rule, $match)
    {
        return $this->rule($rule, $match, 'post');
    }
    
    public function put($rule, $match)
    {
        return $this->rule($rule, $match, 'put');
    }
    
    public function delete($rule, $match)
    {
        return $this->rule($rule, $match, 'delete');
    }
    
    public function patch($rule, $match)
    {
        return $this->rule($rule, $match, 'patch');
    }
    
    public function head($rule, $match)
    {
        return $this->rule($rule, $match, 'head');
    }
    
    /**
     * options请求路由
     * @param string $rule
     * @param mixed $match
     * @return \qpf\router\Rule
     */
    public function options($rule, $match)
    {
        return $this->rule($rule, $match, 'options');
    }
    
    /**
     * 生成路由规则组数组
     * @return array
     */
    public function toArray(): array
    {
        $rules = $this->rules;
        
        foreach ($rules as $method => $methodGroup) {
            foreach ($methodGroup as $group => $ruleGroup) {
                foreach ($ruleGroup as $index => $rule) {
                    $rules[$method][$group][$index] = $rule->toArray();
                }
            }
        }

        if (!empty($this->extra)) {
            $extra = $this->extra;
            foreach ($extra as $method => $methodGroup) {
                foreach ($methodGroup as $index => $rule) {
                    $extra[$method][$index] = $rule->toArray();
                }
            }
        }
        $rules['extra'] = isset($extra) ? $extra : [];
        
        return $rules;
    }
}
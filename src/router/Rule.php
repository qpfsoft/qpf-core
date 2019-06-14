<?php
namespace qpf\router;

use qpf\base\Core;

/**
 * 路由规则
 */
class Rule extends Core
{
    /**
     * 路由类型
     * @var string 默认`控制器路由`
     */
    protected $type = 'controller';
    /**
     * 请求类型
     * @var string 默认`get`
     */
    protected $method;
    /**
     * 路由规则
     * @var string
     */
    protected $rule;
    /**
     * 匹配结果
     * @var mixed
     */
    protected $match;
    /**
     * 路由变量规则
     * @var array
     */
    protected $pattern = [];
    /**
     * 当前路由规则的表达式
     * @var string
     */
    protected $express;
    /**
     * 路由变量的值
     * @var array
     */
    protected $params = [];
    /**
     * 标识符
     * @var string
     */
    protected $id;
    /**
     * 变量默认规则
     * @var string
     */
    protected $defvar = '[\w\-]+';
    /**
     * 优先级
     * @var int
     */
    protected $q;

    /**
     * 设置路由标识符
     * @param string $name 全局唯一
     * @return $this
     */
    public function setID(string $name): Rule
    {
        $this->id = $name;
        return $this;
    }
    
    /**
     * 获取路由标识符
     * @return string
     */
    public function getID(): string
    {
        return $this->id;
    }
    
    /**
     * 获取路由规则
     * @return string
     */
    public function getRule(): string
    {
        return $this->rule;
    }
    
    /**
     * 设置路由规则
     * @param string $rule
     * @return Rule
     */
    public function setRule(string $rule): Rule
    {
        $this->rule = $rule;
        return $this;
    }
    
    /**
     * 获取路由类型
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
    
    /**
     * 设置路由类型
     * @param string $type
     * @return Rule
     */
    public function setType(string $type): Rule
    {
        $this->type = $type;
        return $this;
    }
    
    /**
     * 设置路由匹配结果
     * @param mixed $match
     * @return Rule
     */
    public function setMatch($match): Rule
    {
        $this->match = $match;
        return $this;
    }
    
    /**
     * 获取匹配匹配结果
     * @param bool $parse 是否解析替换参数
     * @return mixed
     */
    public function getMatch(bool $parse = false)
    {
        if ($parse && is_string($this->match)) {
            if (strpos($this->match, ':') !== false && !empty($this->params)) {
                $match = explode('/', $this->match);
                foreach ($match as $i => $val) {
                    if (strpos($val, ':') === 0) {
                        $name = substr($val, 1);
                        $match[$i] = isset($this->params[$name]) ? $this->params[$name] : null;
                    }
                }
                
                return trim(implode('/', $match), '/');
            }
        }
        
        return $this->match;
    }
    
    /**
     * 获取路由正则表达式
     * @return string
     */
    public function getExpress(): string
    {
        if ($this->express === null) {
            $this->parseRule();
        }
        
        return $this->express;
    }
    
    /**
     * 获取路由请求类型
     * @return string
     */
    public function getMethod(): string
    {
        return strtolower($this->method);
    }
    
    /**
     * 获取路由参数
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
    
    /**
     * 检查是否匹配
     * @param string $url
     * @return bool
     */
    public function check($url): bool
    {
        // 生成规则正则, 排序pattern参数顺序
        if ($this->express === null) {
            $this->parseRule();
        }
        
        // 路由匹配
        if (preg_match($this->express, $url, $matchs)) {
            array_shift($matchs);
            foreach ($this->pattern as $name => $value) {
                $this->params[$name] = array_shift($matchs);
            }
            return true;
        }
        
        return false;
    }
    
    /**
     * 设置变量匹配规则
     * ```
     * pattern('var', '[\w\-]+');
     * pattern([
     *      'int' => '\d+',
     *      'str' => '\w+',
     * ]);
     * ```
     * @param string|array $name 变量名称, 变量匹配规则数组
     * @param string $value 变量规则, 例`[\w\-]+`
     * @return Rule
     */
    public function pattern($name, string $value = null): Rule
    {
        if (is_array($name)) {
            $this->pattern = array_merge($this->pattern, $name);
        } else {
            $this->pattern[$name] = $value;
        }
        
        return $this;
    }
    
    /**
     * 设置请求类型
     * @param string $method
     * @return $this
     */
    public function method($method)
    {
        $this->method = strtolower($method);
        return $this;
    }

    /**
     * 解析路由规则为正则
     * @return string
     */
    public function parseRule(): string
    {
        // 路由规则
        $rule = explode('/', $this->rule);
        // 变量规则
        $pattern = $this->pattern;
        // 正确顺序的变量规则列表
        $list = [];
        
        foreach ($rule as $i => $part) {
            // <var>必须,  <var?`>可选
            if (strpos($part, '<') !== false && preg_match_all('/<(\w+(\??))>/', $part, $matches)) {
                $lable = [];
                $replace = [];
                foreach ($matches[1] as $item) {
                    if (strpos($item, '?')) {
                        $item = substr($item, 0, -1);
                        $list[$item] = isset($pattern[$item]) ? $pattern[$item] : $this->defvar;
                        $replace[] = '?(' . $list[$item] . ')?';
                    } else {
                        $list[$item] = isset($pattern[$item]) ? $pattern[$item] : $this->defvar;
                        $replace[] = '(' . $list[$item] . ')';
                    }
                    $lable[] = $item;
                }
                
                $rule[$i] = str_replace($matches[0], $replace, $part);
            // :var必须,  [:var]可选
            } elseif (strpos($part, ':') !== false) {
                // 是否是可选参数
                $optional = strpos($part, '[:') === 0 ? true : false;
                // 将[:name] 转换为 :name
                if ($optional) {
                    $part = substr($part, 1, -1);
                }
                // :name 转换为 name
                $part = substr($part, 1);
                $list[$part] = isset($pattern[$part]) ? $pattern[$part] : $this->defvar;
                $rule[$i] = $optional ? '?('. $list[$part] . ')?' : '('. $list[$part] . ')';
            }
        }

        $this->pattern = $list;
        return $this->express = '#^' . implode('/', $rule) . '$#';
    }
    
    /**
     * 计算Q值
     * @param string $rule 路线或规则
     * @return int
     */
    public static function q(string $rule): int
    {
        if (empty($rule) || $rule === '/') {
            return 0;
        }
        
        $part = explode('/', $rule);
        $q = 0;
        foreach ($part as $val) {
            if (strpos($val, '?>') !== false || strpos($val, '[:') === 0) {
                continue;
            }
            $q++;
        }
        return $q;
    }
    
    /**
     * 生成路由规则数组
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'type'      => $this->type,
            'rule'      => $this->rule,
            'match'     => $this->match,
            'method'    => $this->method,
            'express'   => $this->getExpress(),
            'pattern'   => $this->pattern,
            'q'         => self::q($this->rule),
        ];
    }
}
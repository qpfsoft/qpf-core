<?php
namespace qpf\validator;

/**
 * 内置验证方法
 * 
 * 提示
 * 通过客户端传递的数字可能是数字字符.
 */
class ValidateMethods
{
    /**
     * 必填项
     * @param string $value
     * @return bool
     */
    public static function require($value)
    {
        return !empty($value) || $value == '0';
    }
    
    public static function must($value)
    {
        return !empty($value) || $value == '0';
    }
    
    /**
     * 是否存在
     * @param string $value
     * @return bool
     */
    public static function exist($value)
    {
        return $value !== null || $value !== '';
    }
    
    /**
     * 接受类型检查
     * @param mixed $value
     * @param array $accept 允许的值
     */
    public static function in($value, array $accept)
    {
        return in_array($value, $accept);
    }

    /**
     * 是否布尔值
     * @param mixed $value
     * @return bool
     */
    public static function bool($value)
    {
        return in_array($value, [true, false, 0, 1, '0', '1'], true);
    }
    
    /**
     * 是否纯数字
     * @param string $value
     * @return bool
     */
    public static function number($value)
    {
        return ctype_digit((string) $value);
    }
    
    /**
     * 最小值验证
     * @param mixed $value
     * @param mixed $rule 最小值
     * @return bool
     */
    public static function min($value, $rule)
    {
        return $value >= $rule;
    }
    
    /**
     * 最大值验证
     * @param mixed $value
     * @param mixed $rule 最大值
     * @return bool
     */
    public static function max($value, $rule)
    {
        return $value <= $rule;
    }
    
    /**
     * 数字范围验证
     * @param mixed $value
     * @param mixed $min
     * @param mixed $max
     */
    public static function num($value, $min, $max)
    {
        return $value <= $max && $value >= $min;
    }
    
    public static function get($value, $rule)
    {
        return $value >= $rule;
    }
    
    public static function gt($value, $rule)
    {
        return $value > $rule;
    }
    
    public static function elt($value, $rule)
    {
        return $value <= $rule;
    }
    
    public static function lt($value, $rule)
    {
        return $value < $rule;
    }
    
    public static function eq($value, $rule)
    {
        return $value == $rule;
    }
    
    /**
     * 是否仅含英文字符
     * @param string $value
     * @return bool
     */
    public static function str($value)
    {
        return ctype_alpha($value);
    }
    
    /**
     * 是否仅含字母与数字
     * @param string $value
     * @return bool
     */
    public static function strnum($value)
    {
        return ctype_alnum((string) $value);
    }
    
    /**
     * 是否为数组
     * @param mixed $value
     * @return boolean
     */
    public static function arr($value)
    {
        return is_array($value);
    }

    /**
     * 是否仅含小写字母
     * @param string $value
     * @return bool
     */
    public static function lower($value)
    {
        return ctype_lower($value);
    }
    
    /**
     * 是否不包含空格,换行符, 制表键 等不可见字符
     * @param string $value
     * @return bool 不含返回ture
     */
    public static function graph($value)
    {
        return ctype_graph($value);
    }
    
    /**
     * 使用正则验证
     * @param string $value
     * @param string $rule
     * @return bool
     */
    public static function regex($value, $rule)
    {
        if (0 !== strpos($rule, '/') && !preg_match('/\/[imsU]{0,4}$/', $rule)) {
            // 不是正则表达式则两端补上/
            $rule = '/^' . $rule . '$/';
        }
        
        return is_scalar($value) && 1 === preg_match($rule, (string) $value);
    }
    
    /**
     * 有效日期字符串
     * @param string $value
     * @return bool
     */
    public static function date($value)
    {
        return strtotime((string) $value) !== false;
    }
    
    /**
     * 验证日期时间是否为指定格式
     * @param string $value 时间字符串
     * @param string $rule 格式`Y-m-d h:i:s`
     */
    public static function dateFormat($value, $rule)
    {
        $arr = date_parse_from_format($rule, $value);
        return 0 == $arr['warning_count'] && 0 == $arr['error_count'];
    }
    
    /**
     * 验证真实存在URL
     * @param string $value 主机IP或域名
     * @param string $type DNS标记类型
     * @return bool
     */
    public static function actionUrl($value, $type = 'MX')
    {
        if (!in_array($type, ['A', 'MX', 'NS', 'SOA', 'PTR', 'CNAME', 'AAAA', 'A6', 'SRV', 'NAPTR', 'TXT', 'ANY'])) {
            $type = 'MX';
        }
        
        return checkdnsrr($value, $type);
    }
 
    /**
     * 变量过滤器
     * @param mixed $value 变量
     * @param mixed $rule 规则
     * @param mixed $options 选项
     * @return boolean
     */
    public static function filter($value, $rule, $options = null)
    {
        if(is_string($rule) && strpos($rule, ',') !== false) {
            list($rule, $options) = explode(',', $rule, 2);
        } elseif (is_array($rule)) {
            $options = isset($rule[1]) ? $rule[1] : null;
            $rule = $rule[0];
        }
        
        return filter_var($value, is_int($rule) ? $rule : filter_id($rule), $options) !== false;
    }
    
    /**
     * 验证IP地址格式
     * @param mixed $value 变量
     * @param string $type IP类型
     * @return bool
     */
    public static function ip($value, $type = 'ipv4')
    {
        if (!in_array($type, ['ipv4', 'ipv6'])) {
            $type = 'ipv4';
        }
        
        return self::filter($value, FILTER_VALIDATE_IP, $type == 'ipv4' ? FILTER_FLAG_IPV4 : FILTER_FLAG_IPV6);
    }
    
    
}
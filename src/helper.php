<?php
// ╭───────────────────────────────────────────────────────────┐
// │ QPF Framework [Key Studio]
// │-----------------------------------------------------------│
// │ Copyright (c) 2016-2019 quiun.com All rights reserved.
// │-----------------------------------------------------------│
// │ Author: qiun <qiun@163.com>
// ╰───────────────────────────────────────────────────────────┘

if (! function_exists('echor')) {
    /**
     * 打印变量信息
     * @param mixed $var 变量
     * @param bool $return 返回打印内容
     * @return mixed
     */
    function echor($var, $return = false)
    {
        return \qpf\helper\Export::echor($var, $return);
    }
}

if (! function_exists('dump')) {
    /**
     * 打印变量类型与长度
     * @param mixed $var 变量
     * @return void
     */
    function dump($var)
    {
        \qpf\helper\Export::dump($var);
    }
}

if (! function_exists('print')) {
    /**
     * 打印易读的数组
     * @param mixed $var 变量
     * @return void
     */
    function echor_arr($var)
    {
        \qpf\helper\Export::print($var);
    }
}

if (! function_exists('echo_raw')) {
    /**
     * 原样换行并打印
     * @param string $var 变量
     * @return void
     */
    function echor_raw($var)
    {
        \qpf\helper\Export::echo($var);
    }
}

if (! function_exists('get_varstr')) {
    /**
     * 返回变量的内容描述
     * @param mixed $var 变量
     * @return mixed
     */
    function get_varstr($var)
    {
        return \qpf\helper\Export::varStr($var);
    }
}

if (! function_exists('get_arrstr')) {
    /**
     * 获取数组的字符串描述格式
     * @param array $var 数组
     * @return string
     */
    function get_arrstr($var)
    {
        return \qpf\helper\Export::varArray($var);
    }
}
if (! function_exists('get_obj_vars')) {
    
    /**
     * 获取对象的属性
     * @param object $instance 对象实例
     * @return array 0 - 元素格式
     * 1 - 带属性保护前缀
     * 2 - 无前缀格式
     */
    function get_obj_vars($instance)
    {
        return \qpf\helper\ParseObject::getobjectVars($instance);
    }
}
if (! function_exists('get_obj_methods')) {
    
    /**
     * 获取类的方法 - 不包含继承
     * @param string|object $class 类名或对象
     * @return array
     */
    function get_obj_methods($class)
    {
        return \qpf\helper\ParseObject::getPublicMethods($class);
    }
}
if (! function_exists('echor_object')) {
    
    /**
     * 打印对象信息
     * @param object $var
     * @param bool $return 是否仅返回内容, 默认`false`
     * @return array
     */
    function echor_object($var, $return = false)
    {
        return \qpf\helper\Export::objct($var, $return);
    }
}
if (! function_exists('echor_code')) {
    /**
     * 高亮输出脚本代码
     * @param string $var 代码字符串
     * @param string $return 是否直接返回输出
     * @return string
     */
    function echor_code($var, $return = false)
    {
        return \qpf\helper\Export::codeHighlight($var, $return);
    }
}
if (! function_exists('echor_html')) {
    /**
     * 安全的输出带html的内容
     * @param string $var 变量或内容
     * @param bool $return 是否直接返回输出
     * @return string
     */
    function echor_html($var, $return = false)
    {
        return \qpf\helper\Export::html($var, $return);
    }
}
if (! function_exists('echor_exit')) {
    
    /**
     * 输出内容并终止程序
     * @param mixed $var 变量或内容
     * @return void
     */
    function echor_exit($var)
    {
        echor($var, false);
        exit(1);
    }
}
if (! function_exists('exito')) {
    
    /**
     * 结束并输出内容
     * @param mixed $var 要输出的内容
     */
    function exito($var)
    {
        echo echor($var, true);
        exit(1);
    }
}
if(!function_exists('echor_const')) {
    /**
     * 输出用户常量
     * @param bool $return 是否直接返回输出
     */
    function echor_const($return = false)
    {
        $var = get_defined_constants(true);
        
        if($return) {
            return $var['user'];
        }
        
        echor_arr($var['user']);
    }
}
if (! function_exists('html_encode')) {
    
    /**
     * 特殊字符转换为HTML实体, 例如 & > &amp;
     * - 安全提示: 若是用户提供的内容, 应该考虑html实体编码, 来防止XSS攻击.
     * @param string $string
     * @param bool $doubleEncode 是否重复转换HTML实体, 默认`true`
     * @return string
     */
    function html_encode($string, $doubleEncode = true)
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }
}
if (! function_exists('html_decode')) {
    
    /**
     * 将特殊HTML实体解码回相应的字符
     * @param string $string
     * @return string
     */
    function html_decode($string)
    {
        return htmlspecialchars_decode($string, ENT_QUOTES);
    }
}
if (! function_exists('quote')) {
    
    /**
     * 引用字符串
     * @param string $string 内容
     * @param int|string|array $type 引号类型{默认0:``, 1:'', 2:"", '<>', '{}'}
     */
    function quote($string, $type = 0)
    {
        if (is_numeric($type)) {
            $opt = ['`','\'','"'];
            $ql = $qr = (isset($opt[$type]) ? $opt[$type] : '\'');
        } elseif (is_array($type)) {
            if (isset($type['1'])) {
                $ql = $type['0'];
                $qr = $type['1'];
            } else {
                $ql = $qr = $type['0'];
            }
        } else {
            $len = strlen($type);
            if ($len > 2) {
                $avg = intval($len / 2);
                $ql = substr($type, 0, $avg);
                $qr = substr($type, $avg);
            } else {
                $ql = substr($type, 0, 1);
                $qr = substr($type, 1);
            }
        }
        
        return $ql . $string . $qr;
    }
}
if (! function_exists('array_last_key')) {
    
    /**
     * 返回数组最后一个元素的key值
     * @param array $array
     * @return string
     */
    function array_last_key($array)
    {
        // PHP >= 7.3.0
        if(function_exists('array_key_last')) {
            return array_key_last($array);
        }
        
        end($array);
        return key($array);
    }
}
if (! function_exists('array_first_key')) {
    
    /**
     * 返回数组第一个元素的key值
     * @param array $array
     * @return string
     */
    function array_first_key($array)
    {
        // PHP >= 7.3.0
        if(function_exists('array_key_first')) {
            return array_key_first($array);
        }
        
        reset($array);
        return key($array);
    }
}
if (!function_exists('console_log')) {
    /**
     * 控制台输出
     * ```
     * // 打印字符串
     * console_log('xxx');
     * // 打印数组
     * $arr = [];
     * console_log($arr);
     * console_log(['array_title' => $arr]);
     * ```
     * @param string|array $str 消息
     * @param bool $send 是否发送到游览器, 默认`false`
     */
    function console_log($str, bool $send = false)
    {
        static $_log = [];
        static $showTitle = true;
        $_log[] = $str;
        
        if ($send) {
            echo \qpf\protect\web\Console::log($_log, $showTitle);
            $_log = [];
            $showTitle = false;
        }
    }
}

if (! function_exists('console_disable')) {
    
    /**
     * 禁用控制台
     * @param bool $src 是否引入模式, 默认`false`采用页内脚本
     * @return string
     */
    function console_disable(bool $src = false): string
    {
        if ($src) {
            return \qpf\protect\web\Console::openSrc();
        } else {
            return \qpf\protect\web\Console::openScript();
        }
    }
}
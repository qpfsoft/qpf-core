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
     * 打印一个或多个变量
     * @param mixed $var [必须]要输出的内容
     * @param bool|... $return [可选]是否直接返回信息，而不是输出
     * @return mixed
     */
    function echor($var, $return = false)
    {
        $eol = $return ? '' : '<br>';
        $args = func_get_args();
        $count = func_num_args();
        
        // 参数数量为2时, 参数2是为布尔值将作为返回选项
        if ($count > 2 || ($count == 2 && $return !== true && $return !== false)) {
            foreach ($args as $val) {
                echor($val, false);
            }
            return;
        }
        
        $str = '';
        if (is_array($var)) {
            $str = $return ? get_varstr($var) : '<pre>' . (get_varstr($var)) . '</pre>'; // html_encode
        } elseif (is_object($var)) {
            $str .= 'Object("' . get_class($var) . '") {';
            $vars = get_obj_vars($var);
            if ($vars) {
                $str .= get_arrstr($vars[2], $return);
            }
            $str .= '}';
        } else {
            $str = get_varstr($var);
        }
        
        if ($return) {
            return $str;
        }
        
        echo $str . $eol;
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
        if (is_array($var)) {
            $map_varstr = function (array $arr) use (&$map_varstr) {
                foreach ($arr as $i => $v) {
                    if (is_array($v)) {
                        $arr[$i] = $map_varstr($v);
                    } else {
                        $arr[$i] = get_varstr($v);
                    }
                }
                return $arr;
            };
            
            return print_r($map_varstr($var), true);
        } elseif ($var === null) {
            return 'null';
        } elseif ($var === true || $var === false) {
            return $var ? 'true' : 'false';
        } elseif (is_int($var) || is_float($var)) {
            return $var;
        } elseif (is_numeric($var)) {
            return '\'' . $var . '\'';
        } elseif (is_string($var)) {
            if (strpos($var, '<') !== false || strpos($var, '>') !== false) {
                $var = html_encode($var);
            }
            return '\'' . addslashes($var) . '\''; // 安全转义
        } elseif ($var instanceof \Closure) {
            return 'function(){}';
        } elseif (is_object($var)) {
            return 'Object("' . get_class($var) . '"){}';
        } elseif (is_resource($var)) {
            return '{resource}';
        } else {
            return '{unknown}';
        }
    }
}
if (! function_exists('get_arrstr')) {
    
    /**
     * 获取数组的字符串描述格式
     * @param array $array
     * @param bool|string $retrun 返回类型, 可能的值:
     * - `true` : 使用文本转义符, 排版数组
     * - `false` : 默认, 使用html标签, 排版数组
     * - `text` : 字符串类型, 代表纯文本, 不排版
     * @return string
     */
    function get_arrstr($array, $retrun = false, $level = 1)
    {
        if (! is_array($array))
            return (string) $array;
            
            if ($retrun == 'txt') {
                $eol = '';
                $tab = '';
            } else {
                $eol = $retrun ? PHP_EOL : '<br>';
                $tab = $retrun ? "\t" : '&nbsp;&nbsp;&nbsp;&nbsp;';
            }
            
            $tab_level = str_repeat($tab, $level);
            $str = '';
            if (empty($array)) {
                $str = $retrun ? '[]' : '[ ]';
            } else {
                $tmp = '[' . $eol;
                $lastKey = array_last_key($array);
                
                foreach ($array as $index => $value) {
                    $comma = $lastKey == $index ? '' : ',';
                    if (is_array($value)) {
                        $tmp .= $tab_level . $index . ' => ';
                        $tmp .= get_arrstr($value, $retrun, $level + 1) . $comma . $eol;
                    } else {
                        $tmp .= $tab_level . $index . ' => ' . get_varstr($value) . $comma . $eol;
                    }
                }
                
                if ($level > 1) {
                    $str = $tmp . $tab_level = str_repeat($tab, $level - 1) . ']';
                } else {
                    $str = $tmp . ']';
                }
            }
            return $str;
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
        $clone = (array) $instance;
        if (empty($clone))
            return [];
            
            $arr = [];
            $arr['0'] = $clone;
            $parse_type = function (array $param) {
                $name = ['public_','protected_','private_'];
                if (isset($param[2])) {
                    $type = $param[1] == '*' ? '1' : '2';
                } else {
                    $type = '0';
                }
                return $name[$type];
            };
            
            foreach ($clone as $key => $value) {
                $aux = explode("\0", $key);
                $count = count($aux);
                $newkey = $parse_type($aux) . $aux[$count - 1];
                $arr['1'][$newkey] = &$arr['0'][$key];
                $newkey = $aux[$count - 1];
                $arr['2'][$newkey] = &$arr['0'][$key];
            }
            
            return $arr;
    }
    ;
}
if (! function_exists('get_obj_methods')) {
    
    /**
     * 获取类的方法 - 不包含继承
     * @param string|object $class 类名或对象
     * @return array
     */
    function get_obj_methods($class)
    {
        $array1 = get_class_methods($class);
        $parent_class = get_parent_class($class);
        if ($parent_class) {
            $array2 = get_class_methods($parent_class);
            $array3 = array_diff($array1, $array2);
        } else {
            $array3 = $array1;
        }
        return $array3;
    }
    ;
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
        $eol = $return ? '' : '<br/>';
        $resutl = [];
        if (is_object($var)) {
            $resutl['class'] = get_class($var);
            $params = get_obj_vars($var);
            
            if ($params) {
                foreach ($params[2] as $name => $value) {
                    if (is_array($value)) {
                        $resutl['params'][$name] = $value;
                    } else {
                        $resutl['params'][$name] = get_varstr($value);
                    }
                }
            } else {
                $resutl['params'] = [];
            }
            
            $methods = get_obj_methods($var);
            if($methods) {
                foreach ($methods as $method) {
                    $resutl['methods'][] = $method . '()';
                }
            } else {
                $resutl['methods'] = [];
            }
            
        }
        
        return echor($resutl, $return);
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
        $code = $var;
        $result = highlight_string("<?php\n" . $code, true);
        $result = preg_replace('/&lt;\\?php<br \\/>/', '', $result, 1);
        if ($return) {
            return $result;
        }
        
        echo $result;
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
        $result = str_replace(' ', '&nbsp;', html_encode($var));
        if ($return) {
            return $result;
        }
        echo $result;
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
if (! function_exists('var_echor')) {
    /**
     * 打印变量相关信息
     * @param mixed $var
     */
    function var_echor($var)
    {
        ob_start();
        var_dump($var);
        echo '<pre style="white-space: pre-wrap;word-wrap: break-word;">' . ob_get_clean() . '</pre>';
        exit(1);
    }
}
if(!function_exists('echor_pre')) {
    /**
     * 将数据以pre标签输出
     * @param mixed $var
     * @param bool $return 是否直接返回输出
     * @return string
     */
    function echor_pre($var, $return = false)
    {
        $var = '<pre>' . print_r($var, true) . '</pre>';
        
        if($return) {
            return $var;
        }
        
        echo $var;
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
        
        echor_pre($var['user']);
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
<?php
declare(strict_types=1);

namespace qpf\func;

/**
 * 字符串处理方法
 */
class StringFunc
{
    /**
     * 字符集转换
     * @param string $data 数据
     * @param string $char 目标字符集, 默认`UTF-8`
     * @return string
     */
    public static function characet(string $data, string $char = 'UTF-8'): string
    {
        if (! empty($data)) {
            $type = mb_detect_encoding($data, [
                'UTF-8',
                'GBK',
                'GB2312'
            ]);
            if ($type !== 'UTF-8') {
                $data = mb_convert_encoding($data, 'UTF-8', $type);
            }
        }
        return $data;
    }
    
    /**
     * 获取字符串的字节长度
     *
     * `UTF-8` 一个字符占3字节数
     * `GBK|GB2312` 一个字符占2字节
     * @param string $string
     * @return int
     */
    public static function strbit($string)
    {
        return mb_strlen($string, '8bit');
    }
    
    /**
     * 截取指定字节长度
     * @param string $string 字符串
     * @param int $start 起始位置
     * @param int $length 截取长度
     * @return string
     */
    public static function subit($string, $start, $length = null)
    {
        return mb_substr($string, $start, $length === null ? mb_strlen($string, '8bit') : $length, '8bit');
    }
    
    /**
     * 获取中文字符个数
     * @param string $string 汉字, 字符集utf8
     * @return int
     */
    public static function zhlen($string)
    {
        return mb_strlen($string, '8bit') / 3;
    }
    
    /**
     * 截取字符串
     * @param string $string 字符串
     * @param int $start 起始位置
     * @param int $length 截取长度, 默认`null`
     * @param bool $mulbit 是否多字节字符, 默认`true`, 若不含汉字`false`会更快
     * @return string
     */
    public static function substr($string, $start, $length = null, $mulbit = true)
    {
        if (!$mulbit) {
            return substr($string, $start, $length);
        }
        
        return mb_substr($string, $start, $length, 'UTF-8');
    }
    
    /**
     * 截断字符串, 支持多字节字符
     * @param string $string 字符串
     * @param int $start 开始位置
     * @param int $length 截取长度
     * @param string $charset 字符集
     * @param string $suffix 截断显示字符, 默认`...`
     * @return string
     */
    public static function mbsubstr($string, $start = 0, $length, $charset = 'utf-8', $suffix = '...')
    {
        if (function_exists('mb_substr')) {
            $slice = mb_substr($string, $start, $length, $charset);
        } elseif (function_exists('iconv_substr')) {
            $slice = iconv_substr($string, $start, $length, $charset);
        } else {
            $re['utf-8']  = '/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/';
            $re['gb2312'] = '/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/';
            $re['gbk']    = '/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/';
            $re['big5']   = '/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/';
            preg_match_all($re[$charset], $string, $match);
            $slice = join('', array_slice($match[0], $start, $length));
        }
        
        return $suffix ? $slice . $suffix : $slice;
    }
    
    
    /**
     * 从字符串中随机取出一个或多个值 - 不支持多字节字符
     * @param string $string 字符串
     * @param int $num 取出数量
     * @param bool $mulbit 是否多字节字符, 默认`true`, 若不含汉字`false`会更快
     * @return string
     */
    public static function randString($string, $num = 1, $mulbit = true)
    {
        $strlen = $mulbit ? mb_strlen($string, 'UTF-8') : strlen($string);
        
        $result = '';
        for ($i = 0; $i < $num; $i ++) {
            if ($mulbit) {
                $result .= mb_substr($string, mt_rand(0, $strlen - 1), 1, 'UTF-8');
            } else {
                $result .= substr($string, mt_rand(0, $strlen - 1), 1);
            }
        }
        
        return $result;
    }
    
    /**
     * 打乱字符串顺序
     * @param string $string 字符串
     * @param bool $mulbit 是否多字节字符, 默认`true`, 若不含汉字`false`会更快
     * @return string
     */
    public static function shuffle($string, $mulbit = true)
    {
        if (!$mulbit) {
            return str_shuffle($string);
        }
        
        $array = preg_split("//u", $string, -1, PREG_SPLIT_NO_EMPTY);
        shuffle($array);
        return implode('', $array);
    }
    
    /**
     * 拆分字符串为数组
     * @param string $string 要拆分的字符串.
     * @param int $len 每一段的长度, 默认`1`
     * @param bool $mulbit 是否多字节字符, 默认`true`, 若不含汉字`false`会更快
     */
    public static function split($string, $len = 1, $mulbit = true)
    {
        if (!$mulbit) {
            return str_split($string, $len);
        }
        
        $array = [];
        $strlen = mb_strlen($string, 'UTF-8');
        for ($i = 0; $i < $strlen; $i += $len) {
            $array[] = mb_substr($string, $i, $len, 'UTF-8');
        }
        return $array;
    }
    
    /**
     * 获取字符串种子数据
     * @param bool $upper 是否包含大写字母
     * @param bool $number 是否包含数字
     * @param bool $rand 是否打乱顺序
     * @return string
     */
    public static function strData($upper = true, $number = true, $rand = false)
    {
        $data = 'abcdefghijklmnopqrstuvwxyz';
        $data .= $upper ? 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' : '';
        $data .= $number ? '1234567890' : '';
        
        return $rand ? str_shuffle($data) : $data;
    }
    
    /**
     * 获取随机汉字
     * @param int $num 获取数量
     * @param string $charset 字符集
     * @return array 返回指定数量的汉字数组
     */
    public static function strChinese($num, $charset = 'utf-8')
    {
        $result = [];
        for ($i = 0; $i < $num; $i ++) {
            $result[] = iconv('GB2312', $charset, chr(mt_rand(0xB0, 0xD0)) . chr(mt_rand(0xA1, 0xF0)));
        }
        
        return $result;
    }
    
    /**
     * 字符串是否存在
     *
     * @param string $string 被查找的字符串
     * @param string $check 查找的部分
     * @return boolean 如果没找到返回true. 找到了返回false.
     */
    public static function strExists($string, $check)
    {
        return (strpos($string, $check) !== false);
    }
    
    /**
     * 反 转义字符串.
     * 支持遍历数组
     *
     * - 转义字符串功能,会将 `我和'你` 转换为 `我和\'你`, 方便保存.
     * - 函数功能:
     * - 将`\'` 转换为 `'` , 方便输出.
     * - 将'\\' 转换为 `\` , 方便输出.
     *
     * @param string|array $string 被转义过的字符串
     */
    public static function stripslashes($string)
    {
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = self::stripslashes($val);
            }
        } else {
            $string = stripcslashes($string);
        }
    }
    
    /**
     * 将html实体转换未utf8字符串
     * @param string $input
     * @return string
     */
    public static function entitiesToUtf8($input)
    {
        return preg_replace_callback('/(&#[0-9]+;)/',
            function ($m) {
                return mb_convert_encoding($m[1], 'UTF-8', 'HTML-ENTITIES');
            }, $input);
    }
    
    /**
     * 将html代码过滤为普通文本
     * @param string $input
     * @return string
     */
    public static function plainText($input)
    {
        return trim(html_entity_decode($this->entitiesToUtf8(strip_tags($input))));
    }
    
    /**
     * 字符串超出长度部分显示为点
     * @param string $string 字符串
     * @param int $length 保留长度
     * @param string $omit 省略符号, 默认`...`
     * @param string $charset 字符集, 默认`utf8`
     * @return string
     */
    public static function substrLen($string, $length, $omit = '...', $charset = 'utf8')
    {
        if (mb_strlen($string, $charset) > $length) {
            return mb_substr($string, 0, $length, $charset) . $omit;
        }
        
        return $string;
    }
    
    /**
     * 获取路径中的文件名 - 支持多字节
     *
     * @param string $path 路径字符串
     * @param string $suffix 如果设置后缀名，它也将被去掉
     * @return stirng
     */
    public static function basename($path, $suffix = '')
    {
        if (($len = mb_strlen($suffix)) > 0 && mb_substr($path, - $len) === $suffix) {
            $path = mb_substr($path, 0, - $len);
        }
        $path = rtrim(str_replace('\\', '/', $path), '/\\');
        if (($pos = mb_strrpos($path, '/')) !== false) {
            return mb_substr($path, $pos + 1);
        }
        
        return $path;
    }
    
    /**
     * 获取父目录的路径 - 支持多字节
     * @param string $path
     * @return string
     */
    public static function dirname(string $path)
    {
        $pos = mb_strrpos(str_replace('\\', '/', $path), '/');
        if ($pos !== false) {
            return mb_substr($path, 0, $pos);
        }
        
        return '';
    }
    
    /**
     * 将字符串编码为Base64 - 安全使用在URL或文件名
     * @param string $input 要编码的字符串
     * @return string
     */
    public static function base64UrlEncode(string $input): string
    {
        return strtr(base64_encode($input), '+/', '-_');
    }
    
    /**
     * 将Base64解码为字符串 - 安全使用在URL或文件名
     * @param string $input Base64编码
     * @return string
     */
    public static function base64UrlDecode(string $input): string
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }
    
    /**
     * 安全地将float转换为字符串 - 小数分隔符始终为`.`
     * @param string $number
     * @return mixed
     */
    public static function floatToString($number):string
    {
        return str_replace(',', '.', (string) $number);
    }
}
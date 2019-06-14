<?php
declare(strict_types = 1);

namespace qpf\safety;


/**
 * 验证代码
 * 
 * # 随机密钥
 * - 可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度
 */
class AuthCode
{
    /**
     * 随机密钥长度 - 范围`0-32`
     * - 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
     * - 当此值为 0 时，则不产生随机密钥
     * @var int 
     */
    private static $secureID = 4;
    /**
     * 默认安全密钥 - 加密盐32位
     * @var string
     */
    private static $secureKey = 'saL+%IVVB#c~U*CPk3MJnq0VMNs~nrE^';
    /**
     * 密文混淆分组
     * @var string
     */
    private static $group = [
        '_e' => '4,12', // 1
        '_i' => [10, 16], // 2
        '_f' => 15,// 3
        '_b' => 12, // 4
        '_h' => 9, // 5
        '_c' => 6, // 6
        '_g' => 16, // 7
        '_a' => 21, // 8
        '_d' => 32, // 9
        '_j' => 100, // 10
    ];
    
    public static function groupEnode($encode)
    {
        $start = 0;
        $length = strlen($encode);
        $group = [];

        foreach (self::$group as $var => $end) {
            if (!is_numeric($end)) {
                if (!is_array($end)) {
                    $end = explode(',', $end);
                }
                list($min, $max) = $end;
                $end = mt_rand((int) $min, (int) $max);
            }
            
            if ($start < $length) {
                $group[$var] = substr($encode, $start, $end);
                $start += $end;
            }
        }

        // 自然排序
        ksort($group);
        return http_build_query($group);
    }
    
    public static function groupDecode($query)
    {
        if (is_string($query)) {
            parse_str($query, $group);
        }
        
        $encode = [];
        
        foreach (self::$group as $var => $end) {
            if (isset($group[$var])) {
                $encode[$var] = $group[$var];
            }
        }
        
        return join('', $encode);
    }
    
    
    /**
     * 加密数据
     * @param string $string 数据
     * @param string $key 密钥
     * @param int $expiry 有效期, 默认 0 代表永久有效, 单位秒
     * @return string
     */
    public static function encode(string $string, int $expiry = 0, string $key = ''): string
    {
        return strtr(self::keyBook($string, false, $key, $expiry), '+/', '-_');
    }

    /**
     * 解密数据
     * @param string $string 密文
     * @param string $key 密钥
     * @return string|false 验证码失效将返回false
     */
    public static function decode(string $string, string $key = '')
    {
        return self::keyBook(strtr($string, '-_', '+/'), true, $key, 0);
    }
    
    /**
     * 密匙簿
     * @param string $data 数据
     * @param bool $decode 是否解码, 默认`false`进行编码
     * @param string $key 32位密钥
     * @param int $expiry 有效期, 默认0永久, 单位秒
     * @return string|false
     */
    private static function keyBook(string $data, bool $decode = false, string $key, int $expiry = 0)
    {
        $key = md5($key ? $key : self::$secureKey);
        $keya = md5(substr($key, 0, 16)); // 前16位, 参与加密
        $keyb = md5(substr($key, 16, 16)); // 后16位, 参与数据完整性验证
        $keyc = self::$secureID > 0 ? ($decode ? substr($data, 0, self::$secureID) : substr(md5(microtime()), -self::$secureID)) : ''; // 用于生成变化的密文
        
        $cryptKey = $keya . md5($keya . $keyc);
        $cryptLength = strlen($cryptKey);
 
        $data = $decode ? base64_decode(substr($data, self::$secureID)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($data . $keyb), 0, 16) . $data;
        $dataLength = strlen($data);
        
        $result = '';
        $box = range(0, 255);
        
        $rndkey = []; // 产生密匙簿
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptKey[$i % $cryptLength]);
        }
        // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        // 核心加解密部分
        for ($a = $j = $i = 0; $i < $dataLength; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            // 从密匙簿得出密匙进行异或，再转成字符
            $result .= chr(ord($data[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        
        if ($decode) {
            // substr($result, 0, 10) == 0 验证数据有效性
            // substr($result, 0, 10) - time() > 0 验证数据有效性
            // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
            // 验证数据有效性，请看未加密明文的格式
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return false;
            }
        } else {
            // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
            return $keyc . str_replace('=', '', base64_encode($result));
        }
    }
}
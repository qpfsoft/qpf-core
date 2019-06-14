<?php
namespace qpf\safety;

/**
 * 数字编码
 */
class Number
{
    /**
     * 字典可用长度
     * - 最大值 63
     * - 可用字典范围越长，编码结果越短
     * @var int
     */
    private static $secureID = 61;
    /**
     * 字典打乱key
     * - 用逗号分隔的数字(不重复), 总和小于[[$secureID]]
     * @var string
     */
    private static $secureKey = '24,37';
    /**
     * 编码字典
     * @var string
     */
    private static $secureCodes;
    
    /**
     * 生成安全的编码字典
     * @return string
     */
    public static function secureCodes()
    {
        if (self::$secureCodes === null) {
            $old = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            
            $key = explode(',', self::$secureKey);
            sort($key); // 升序
            
            $new = [];
            $next = 0;
            foreach ($key as $i) {
                $new[] = substr($old, $next, $i);
                $next = $i;
            }

            arsort($new);// 降序
            self::$secureCodes = implode($new, '');
        }
        
        return self::$secureCodes;
    }
    
    /**
     * 将数字转为短字符串
     *
     * @param string $number 数字
     * @return string 短字符串
     */
    public static function encode($number)
    {
        $out = '';
        $codes = self::secureCodes();
        $max = self::$secureID - 1;
        while ($number > $max) {
            $key = bcmod($number, self::$secureID); // $number ÷ self::$secureID
            $number = bcsub(bcdiv($number, self::$secureID), '1'); // $number ÷ self::$secureID - 1
            $out = $codes{$key} . $out;
        }
        return $codes{$number} . $out;
    }
    
    /**
     * 将短字符串还原转为数字
     *
     * @param string $code 短字符串
     * @return integer 数字
     */
    public static function dencode($code)
    {
        $codes = self::secureCodes();
        $num = 0;
        $i = $count = strlen($code);
        for ($j = 0; $j < $count; $j ++) {
            $i --;
            $char = $code{$j};
            $pos = strpos($codes, $char);
            $num = bcadd(bcmul(bcpow(self::$secureID, $i), ($pos + 1)), $num);
        }
        $num = bcsub($num, 1);
        return $num;
    }
}
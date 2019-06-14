<?php
namespace qpf\safety;

/**
 * Base62安全版
 * 
 * - key+data的md5散列, 分为4段, 根据映射表, 生成四段编码
 *
 */
class Base62
{
    /**
     * base62编码
     * @param string $data 数据
     * @param string $key 盐
     * @return string[] 返回4个值, 取一个即可
     */
    public static function encode($data, $key = 'qpf')
    {
        // 62进制映射表
        $charset = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        
        /* 第一步
         * 将数据md5成固定的32位散列
         */
        $urlhash = md5($key . $data);
        $hexLen = strlen($urlhash); // 散列长度
        $bitLen = $hexLen / 8 ; // 分成多少段
        $output = [];
        
        /* 第二步
         * 将md5散列分为4段, 每段8个字节, 将它看成16进制串与`0x3fffffff`(30位1)与操作, 即超过30位的忽略处理;
         */
        for ($i = 0; $i < $bitLen; $i++) {
            // 取8个字位
            $urlhash_piece = substr($urlhash, $i * 8, 8);
            // 与运算 , 此处需要用到hexdec()将16进制字符串转为10进制数值型，否则运算会不正常
            $hex = hexdec($urlhash_piece) & 0x3fffffff;
            // 输出
            $out = '';
            
            /* 第三步
             * 生成6位字符串
             */
            for ($j = 0; $j < 6; $j++) {
                // 将得到的值与0x0000003d,3d为61，即charset的坐标最大值
                $out .= $charset[$hex & 0x0000003d];
                // 循环完以后将hex右移5位
                $hex = $hex >> 5;
            }
            
            // md5串得到4个6位串, 取里面的任意一个就可作为长url的短url地址
            $output[] = $out;
        }
        
        return $output;
    }
}
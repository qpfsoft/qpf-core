<?php
declare(strict_types=1);
namespace qpf\builder\code;

use qpf\helper\Export;

/**
 * 数组源代码生成
 */
class ArrayCode
{
    /**
     * 生成简单数组的源代码
     * @param array $array 一维数组
     * @return string
     */
    public static function buildSimple(array $array): string
    {
        $array = str_replace(['array (', '),', ');' , ')'], ['[', ']', '];', ']'], var_export($array, true));
        // `\t` 转换为4个空格
        return $array = str_replace('  ', '    ', $array);
    }
    
    /**
     * 生成数组源代码
     * @param array $array 数组
     * @return string
     */
    public static function build(array $array): string
    {
        $iscli = Export::isCli();
        Export::isCli(true);
        $code = Export::varArray($array);
        Export::isCli($iscli);
        return $code;
    }
}
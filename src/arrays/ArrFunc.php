<?php
namespace qpf\arrays;

/**
 * 数组处理方法
 */
class ArrFunc
{
    /**
     * 检查数组中是否存在某个值 - 大小写敏感
     * @param string $value 值
     * @param array $array 一维数组
     * @return bool
     */
    public static function in_array(string $value, array $array)
    {
        // 严格模式, 更块, 不会进行自动转换类型
        return in_array($value, $array, true);
    }
    
    /**
     * 检查数组中是否存在某个值 - 大小写敏感, 大数据更快
     * @param string $value 搜索值
     * @param array $array 一维数组
     */
    public static function in_array_multi(string $value, array $array)
    {
        $array = array_flip($array);
        return isset($array[$value]);
    }
}
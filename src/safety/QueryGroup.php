<?php
declare(strict_types = 1);

namespace qpf\safety;

use qpf\exceptions\ParameterException;

/**
 * 查询参数分组
 * 
 * # 概念理解
 * 有10个盒子, 盒子自左向右的编号为1~10, 但是编号在箱子的底部, 只有放箱子的人知道!
 * - 然后打乱箱子的顺序, (编号在底部, 可验证)
 * - 可以为箱子刷上不同的颜色, 或自定义做编号.
 * 
 * 将一个圆柱体面包, 切掉随机的长度放入到盒子中:
 * - 情况一: 每个箱子都要放入, 放入大小不限 (10个数字的排列, 破解很快), 箱子数量固定容易发现是分组
 * - 情况二: 从左向右, 随心所欲的放入一部分, 直到面包分完即可, 后面的箱子可以空着!可以更好的迷惑
 */
class QueryGroup
{
    /**
     * 字典集合
     * @var array
     */
    private $dict = [
        'def' => ['_a', '_b', '_c', '_d', '_e', '_f', '_g', '_h', '_i', '_j'], // count 10 
        'any' => ['o0', 'oq', 'op', 'og', 'oc', 'o8', 'oy', 'oa', 'o6', 'oe'], // 10
    ];
    
    /**
     * 词典长度
     * @var int
     */
    private $dictLen = 10;
    
    /**
     * 字符串拆分为数组 - 相对平均
     * @param string $string 字符串
     * @param string $count 拆分数量
     */
    public function strSplit(string $string, int $count)
    {
        $strlen = strlen($string);
        $avg = $strlen / $count;
        
        if (is_float($avg)) {
            list($avg, $m) = explode('.', (string) $avg);// 5.3 =>  5, 3
            
            $tmp = []; // 记录每个元素分配的长度
            $m = 0; // 已分配字符的总数
            $i = 0; // 数组key
            do {
                if (!key_exists($i, $tmp)) {
                    $tmp[$i] = 0;
                }
                
                $tmp[$i] += 1;
                $m++;

                if ($i < $count - 1) {
                    $i++;
                } else {
                    $i = 0;
                }
            } while ($m < $strlen);
            
            $start = 0;
            $arr = [];
            
            foreach ($tmp as $end) {
                $arr[] = substr($string, $start, $end);
                $start += $end;
            }
            
            return $arr;
        } else {
            return str_split($string, $avg);
        }
    }
    
    /**
     * 平均拆分 - 根据词典数量
     * @param string $data 数据
     * @param string $dict 字典类型
     * @param array $sort 排序密钥
     * @return string
     */
    public function splitAvg(string $data, string $dict = 'def', array $sort = [5, 4, 2, 7, 10, 6, 8, 3, 9, 1])
    {
        if (count($sort) !== $this->dictLen) {
            throw new ParameterException('array param want count ' . $this->dictLen);
        }
        
        if (!isset($this->dict[$dict])) {
            throw new ParameterException('dict type error!');
        }
        $start = 0;
        $dataLength = strlen($data);
        
        // 映射 参数名 => 存值顺序编号
        $map = array_combine($this->dict[$dict], $sort); // name1 => sort1
        asort($map); // 按 存值顺序 1-9 排序 参数名
        
        // 平均分割字符串为数组
        $list = $this->strSplit($data, $this->dictLen);
        
        $result = array_combine(array_keys($map), $list);
        ksort($result); // a-z

        return http_build_query($result);
    }

    /**
     * 拆分数据
     * @param string $data 数据
     * @param string $dict 字典类型
     * @param array $sort 排序密钥
     * @param array $long 每段拆分长度预设, 支持随机长度 `min, max`
     * @return array
     */
    public function split(string $data, string $dict = 'def', 
        array $sort = [5, 4, 2, 7, 10, 6, 8, 3, 9, 1],
        array $long = [[4,8], [10, 16], 15, 12, 9, 6, 16, 21, 32, 100]): string
    {
        if (count($sort) !== $this->dictLen || count($long) !== $this->dictLen) {
            throw new ParameterException('array param want count ' . $this->dictLen);
        }

        if (!isset($this->dict[$dict])) {
            throw new ParameterException('dict type error!');
        }
        $start = 0;
        $dataLength = strlen($data);
        
        // 映射 参数名 => 存值顺序编号
        $map = array_combine($this->dict[$dict], $sort); // name1 => sort1
        asort($map); // 按 存值顺序 1-9 排序 参数名

        // 映射 参数名 => 截取长度
        $dicts = array_combine(array_keys($map), $long);
        
        $group = [];
        foreach ($dicts as $var => $end) {
            if (!is_numeric($end)) {
                if (!is_array($end)) {
                    $end = explode(',', $end);
                }
                list($min, $max) = $end;
                $end = mt_rand((int) $min, (int) $max);
            }
            
            if ($start < $dataLength) {
                $group[$var] = substr($data, $start, $end);
                $start += $end;
            }
        }

        // 自然排序 a-z
        ksort($group);
        return http_build_query($group);
    }
    
    /**
     * 合并数据
     * @param array $get $_GET数组
     * @param string $dict 字典类型
     * @param array $sort 排序密钥
     * @return string
     */
    public function merge(array $get, string $dict = 'def', array $sort = [5, 4, 2, 7, 10, 6, 8, 3, 9, 1]): string
    {
        if (!isset($this->dict[$dict])) {
            throw new ParameterException('dict type error!');
        }
        
        if (count($sort) !== $this->dictLen) {
            throw new ParameterException('sort array param want count ' . $this->dictLen);
        }
        
        // 映射 参数名 => 存值顺序
        $map = array_combine($this->dict[$dict], $sort);
        asort($map); // 按存值顺序排序 映射名称, 1-9 

        $encode = [];
        
        foreach ($map as $var => $end) {
            if (isset($get[$var])) {
                $encode[$var] = $get[$var];
            }
        }
        
        return join('', $encode);
    }
}
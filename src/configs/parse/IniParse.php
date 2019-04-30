<?php
namespace qpf\configs\parse;

/**
 * ini配置解析器
 */
class IniParse implements ParseInterface
{
    /**
     * 解析配置
     * @param string $config 配置内容或路径
     * @return array 
     */
    public function parse($config)
    {
        if (is_file($config)) {
            return parse_ini_file($config, true);
        } else {
            return parse_ini_string($config, true);
        }
    }
}
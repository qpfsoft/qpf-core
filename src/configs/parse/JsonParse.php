<?php
namespace qpf\configs\parse;

/**
 * Json配置解析器
 */
class JsonParse implements ParseInterface
{
    /**
     * 解析配置
     * @param string $config 配置内容或路径
     * @return array
     */
    public function parse($config)
    {
        if (is_file($config)) {
            $config = file_get_contents($config);
        }
        return json_decode($config, true);
    }
}
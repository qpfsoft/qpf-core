<?php
namespace qpf\configs\parse;

/**
 * Xml配置解析器
 */
class XmlParse implements ParseInterface
{
    /**
     * 解析配置
     * @param string $config 配置内容或路径
     * @return array
     */
    public function parse($config)
    {
        if (is_file($config)) {
            $config = simplexml_load_file($config);
        } else {
            $config = simplexml_load_string($config);
        }
        $result = (array) $config;
        foreach ($result as $i => $v) {
            if (is_object($v)) {
                $result[$i] = (array) $v;
            }
        }
        return $result;
    }
}
<?php
namespace qpf\configs\parse;

/**
 * 配置解析接口
 */
interface ParseInterface
{

    /**
     * 解析配置
     * @param string $config 配置内容或路径
     */
    public function parse($config);
}
<?php
namespace qpf\assets;

/**
 * 资源编译接口
 */
interface AssetCompileInterface
{
    /**
     * 编译文件
     * @param string $asset 资源文件
     * @param string $dst 发布目录
     */
    public function compile($asset, $dst = null);
}
<?php
namespace qpf\assets;

use qpf;
use qpf\exceptions\ConfigException;
use qpf\file\Dir;
use qpf\base\Core;

/**
 * web资源包
 *
 * 会将src目录内的资源完整复制到dst目录下
 */
class WebAssetPack extends Core implements AssetInstallInterface
{
    /**
     * 资源根目录
     * @var string
     */
    public $src;
    /**
     * 发布目标目录
     * @var string
     */
    public $dst;
    
    /**
     * 引导初始化
     */
    protected function boot()
    {
        if ($this->src !== null) {
            $this->src = rtrim(QPF::apaths()->getAlias($this->src), '\\/');
        }
        
        if ($this->dst !== null) {
            $this->dst = rtrim(QPF::apaths()->getAlias($this->dst), '\\/');
        }
    }
    
    /**
     * 发布资源包到Web目录
     */
    public function publish()
    {
        if (!empty($this->src) && !empty($this->dst)) {
            if (is_dir($this->src)) {
                Dir::single()->copyDir($this->src, $this->dst);
            } else {
                throw new ConfigException('Can not find src dir');
            }
        }
    }
}
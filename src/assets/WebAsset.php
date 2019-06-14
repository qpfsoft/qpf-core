<?php
namespace qpf\assets;

use qpf;
use qpf\base\Core;
use qpf\file\Dir;

/**
 * web资源
 *
 * 按照[[assets]]资源描述单独安装资源包, 与[[WebAssetPack]]的区别
 * 是有选择的安装资源到web入口目录
 */
class WebAsset extends Core implements AssetInstallInterface
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
     * 资源列表
     * 
     * 格式:
     * ```
     * [
     *      ['资源路径', '发布到的资源路径'],
     *      ['@src/js/main.js', '@dst/main.js'], // @src 与 @dst 需要省略不写
     *      ['/js/main.js', '/main.js'], // 需要以`/`开头, 
     *      ['/css', '/'], // 将@src/css目录下的所有内容, 复制到 @dst/ 目录下
     * ]
     * ```
     * 
     * - 资源路径相对于[[$src]]
     * @var array
     */
    public $assets = [];
    
    /**
     * 初始化
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
     * 发布资源
     */
    public function publish()
    {
        foreach ($this->assets as $item) {
            
            if (is_array($item) && count($item) > 1) {
                list($src, $dst) = $item;
                
                $dir = Dir::single();
                
                if (is_file($this->src . $src)) {
                    $dir->copyFile($this->src . $src, $this->dst . $dst);
                } elseif (is_dir($this->src . $src)) {
                    $dir->copyDir($this->src . $src, $this->dst . $dst);
                }
                
            }
            
        }
    }

}
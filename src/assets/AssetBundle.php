<?php
namespace qpf\assets;

use qpf;
use qpf\base\Core;
use qpf\exceptions\ConfigException;
use qpf\file\Dir;

/**
 * 资源包抽象类
 * 
 * 代表的资源文件的集合，如 CSS，JS，图片
 * 
 * 资源路径与url路径以`/`开头代表入口目录
 */
class AssetBundle extends Core
{
    /**
     * 源码目录路径
     * @var string
     */
    public $srcPath;
    /**
     * 目标目录路径
     * @var string
     */
    public $dstPath;
    /**
     * URL访问路径前缀
     * @var string
     */
    public $urlPath;
    /**
     * 样式资源
     * @var Asset[]
     */
    public $css = [];
    /**
     * 脚本资源
     * @var Asset[]
     */
    public $js = [];
    /**
     * 依赖的其它资源包
     * @var AssetBundle[]
     */
    public $require = [];
    
    /**
     * 引导初始化
     */
    protected function boot()
    {
        if ($this->srcPath !== null) {
            $this->srcPath = rtrim(QPF::apaths()->getAlias($this->srcPath), '\\/');
        }
        
        if ($this->dstPath !== null) {
            $this->dstPath = rtrim(QPF::apaths()->getAlias($this->dstPath), '\\/');
        }
        
        if ($this->urlPath !== null) {
            $this->urlPath = rtrim(QPF::apaths()->getAlias($this->urlPath) . '/');
        }
    }
    
    /**
     * 暴露资源包到视图
     * @param View $view 视图对象
     * @return static 资源实例
     */
    public static function export($view)
    {
        return $view->import(static::class);
    }
    
    /**
     * 发布资源包到Web目录
     */
    public function publish()
    {
        if (!empty($this->srcPath) && !empty($this->dstPath)) {
            if (is_dir($this->srcPath)) {
                Dir::single()->copyDir($this->srcPath, $this->dstPath);
            } else {
                throw new ConfigException('Can not find srcPath dir');
            }
        }
    }
}
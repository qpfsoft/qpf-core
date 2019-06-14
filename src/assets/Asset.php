<?php
namespace qpf\assets;

use qpf;
use qpf\base\Core;
use qpf\exceptions\ConfigException;
use qpf\file\Dir;

/**
 * 资源对象
 * 
 * 该类代表一个静态资源
 */
class Asset extends Core
{
    /**
     * 样式资源
     * @var string
     */
    const CSS = 'css';
    /**
     * 脚本资源
     * @var string
     */
    const JS = 'js';
    /**
     * 图片资源
     * @var string
     */
    const IMG = 'img';
    /**
     * 视频资源
     * @var string
     */
    const VIDEO = 'video';
    /**
     * 音频资源
     * @var string
     */
    const MUSIC = 'music';
    /**
     * 保持原样
     * @var string
     */
    const RAW = 'raw';
    
    /**
     * 源文件路径
     * @var string
     */
    public $src;
    /**
     * 发布文件路径
     * @var string
     */
    public $dst;
    /**
     * URL访问路径
     * CDN路径或网络路径
     * @var string
     */
    public $url;
    /**
     * 本地域名URL路径
     * @var string
     */
    public $local;
    /**
     * 资源类型
     * @var string
     */
    public $type;
    
    protected function boot()
    {
        if (empty($this->src) && empty($this->url)) {
            throw new ConfigException('Assest path not set');
        }
    }

    /**
     * 暴露资源到视图
     * @param View $view 视图对象
     * @return static 资源实例
     */
    public static function export($view)
    {
        return $view->import(static::class);
    }
    
    /**
     * 发布资源文件
     */
    public function publish()
    {
        if (!empty($this->src) && !empty($this->dst)) {
            if (is_file($this->src)) {
                Dir::single()->copyFile($this->src, $this->dst);
            }
        }
    }
    
    /**
     * 获取资源路径
     * @return string
     */
    public function getPath() : string
    {
        if (!empty($this->url)) {
            return $this->url;
        }
        
        if (!empty($this->dst)) {
            if (!is_file($this->dst)) {
                $this->publish();
            }
            return $this->dst;
        }
        
        return $this->src;
    }

}
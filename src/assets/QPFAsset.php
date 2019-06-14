<?php
namespace qpf\assets;

use qpf;

/**
 * QPF 资源包
 */
class QPFAsset extends AssetBundle
{
    public $css = [
        '/qpf.min.css',
    ];
    public $js = [
        '/js/qpf.js',
    ];
    
    protected function boot()
    {
        $this->srcPath = QPF::apaths()->getAlias('@qpfsoft/qpf-ui/dst');
    }
    
    /**
     * 获取指定目录下的js文件列表
     * @param string $path 查找目录
     * @param string $rootPath 隐藏的根路径部分
     * 默认为null, 返回完整文件路径
     * @return array
     */
    public function getJsAll(string $path, string $rootPath = null): array
    {
        $files = glob($path . '/*.js');
        
        $start = $rootPath === null ? 0 : strlen($rootPath);
        foreach ($files as $i => $file) {
            $files[$i] = '/' . trim(substr($file, $start), '\\/');
        }
        
        return $files;
    }
}
<?php
namespace qpf\assets;

use qpf;

/**
 * web资源包
 * 
 * 将资源包配置文件路径, 添加到[[$assets]]属性中, 或直接添加数组定义.
 */
class Webpack
{
    /**
     * 资源包配置列表
     * ```
     * [
     *      '资源包定义配置文件路径', // string
     *      
     *      // array
     *      [
     *          '$class' => '...',
     *          'src' => '...',
     *          'dst' => '...',
     *      ],
     * ]
     * ```
     * @var array
     */
    protected $assets = [];
    
    /**
     * 获取web资源包安装列表
     * @return array
     */
    public function getAssets()
    {
        if (empty($this->assets)) {
            if (is_file(QPF::$app->getQpfPath() . '/assets.php')) {
                $this->assets = include(QPF::$app->getQpfPath() . '/assets.php');
            }
        }
        
        return $this->assets;
    }
    
    /**
     * 安装web资源包
     * @return array
     */
    public function install(): array
    {
        $info = [];
        $assets = $this->getAssets();
        
        // 无定义
        if  (empty($assets)) {
            return $info;
        }
        
        foreach ($assets as $pack) {
            // 将字符串判定为资源包定义配置文件
            if (is_string($pack)) {
                $pack = QPF::apaths()->getAlias($pack);
                
                if (is_file($pack)) {
                    $pack = include($pack);
                } else {
                    $info[] = 'load error: ' . $pack;
                }
            }
            
            $msg = 'load & install: ' . $pack['src'];
            
            try {
                $asset = QPF::create($pack);
                if ($asset instanceof AssetInstallInterface) {
                    $asset->publish();
                } else {
                    throw new \Exception('Asset not AssetInstallInterface');
                }
            } catch (\Exception $e) {
                $msg = 'install error: ' .  $pack['src'] . '; ' . $e->getMessage();
            }
            
            $info[] = $msg;
        }
        
        return $info;
    }
    
}
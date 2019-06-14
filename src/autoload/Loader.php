<?php
use qpf\autoload\Autoload;
use qpf\autoload\FastLoad;

/**
 * 加载器
 */
class Loader
{
    /**
     * 加载基础文件
     */
    protected static function loadBaseFiles()
    {
        include __DIR__ . '/../base/Apaths.php';
        include __DIR__ . '/Autoload.php';
    }
    
    /**
     * 注册自动加载程序
     */
    public static function register()
    {
        self::loadBaseFiles();
        if (is_file(__DIR__ . '/FastLoad.php')) {
            include __DIR__ . '/FastLoad.php';
            // 快速加载注册
            FastLoad::register();
        } else {
            Autoload::setClassMap(include __DIR__ . '/map.php');
            Autoload::setNamespace(include __DIR__ . '/namespace.php');
            Autoload::setLoadDir(include __DIR__ . '/dir.php');
            
            Autoload::register();
        }
    }
}
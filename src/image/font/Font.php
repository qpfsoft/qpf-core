<?php
namespace qpf\image\font;

use qpf\exception\NotFoundException;

/**
 * 字体管理
 */
class Font
{
    /**
     * 字体映射
     * @var array
     */
    private static $map;
    /**
     * 资源根路径
     * @var string
     */
    public static $path = __DIR__;
    
    /**
     * 初始化内置字体
     */
    private static function init()
    {
        if (self::$map === null) {
            if (is_file($file = self::$path . '/map.php')) {
                self::$map = include($file);
            } else {
                self::$map = [];
            }
        }
    }
    
    /**
     * 添加字体
     * @param string $name 字体名或唯一标识
     * @param string $path 字体TTF文件位置
     * @return void
     */
    public static function add($name, $path)
    {
        self::init();
        self::$map[$name] = $path;
    }
    
    /**
     * 导入字体集合
     * @param array $maps
     */
    public static function setMaps(array $maps)
    {
        self::init();
        self::$map = array_merge(self::$map, $maps);
    }
    
    /**
     * 返回所有字体映射
     * @return array
     */
    public static function getMaps()
    {
        self::init();
        return self::$map;
    }
    
    /**
     * 获取字体TTF文件
     * @param string $name 字体名或唯一标识
     * @return string 如果不存在将返回默认字体的TTF
     */
    public static function get($name)
    {
        self::init();
        if(isset(self::$map[$name])) {
            return self::$map[$name];
        } elseif (is_file($file = self::$path . '/font/' . $name . '.TTF')) {
            return $file;
        } elseif (isset(self::$map['default'])) {
            return self::$map['default'];
        } else {
            throw new NotFoundException('Font default missing');
        }
    }
}
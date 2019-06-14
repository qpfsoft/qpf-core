<?php
namespace qpf\autoload;

use qpf;
use qpf\exceptions\UnknownClass;
use qpf\base\Apaths;

/**
 * 自动加载
 *
 * - 类映射 : 以'\'开头带命名空间的类名与类文件路径的映射数组
 * - 别名路径 : 以`@`开头的目录名可替换为注册的完整路径
 * - 类别名 : 注册别名到PHP系统内, 类别名与类原名都可代表某一个类
 */
class Autoload
{
    /**
     * 类名映射到类文件
     * @var array
     */
    private static $map = [];
    /**
     * 命名空间映射到路径
     * @var array
     */
    private static $namespace = [];
    /**
     * 别名映射到类
     * @var array
     */
    private static $alias = [];
    /**
     * 加载查找目录
     * @var array
     */
    private static $dir = [];
    /**
     * 别名路径处理程序
     * @var Apaths
     */
    private static $apaths;
    
    /**
     * 实例化工厂
     * @param string $name 类名
     * @param string $namespace 命名空间
     * @param array $params 构造参数
     * @param array $option 对象属性设置
     * @return object
     */
    public static function create($name, $namespace = '', $params = [], $option = [])
    {
        $class = $namespace ?  $namespace . ucfirst($name) : $name;
        
        if(class_exists($class)) {
            $config = ['class' => $class];
            return QPF::create($config + $option, $params);
        } else {
            throw new UnknownClass($class);
        }
    }
    
    /**
     * 返回别名路径处理程序
     * @param Apaths $apaths 处理程序
     * @return Apaths
     */
    public static function apaths($apaths = null)
    {
        if ($apaths === null){
            if (is_object(self::$apaths)) {
                return self::$apaths;
            }
            
            self::$apaths = new Apaths();
        } else {
            self::$apaths = $apaths;
        }

        return self::$apaths;
    }
    
    /**
     * 设置类所在文件
     * @param string $class 类名(带命名空间), 批量设置数组
     * @param string $path 类文件路径
     * @return void
     */
    public static function setClassMap($class, $file = null)
    {
        if(is_array($class)) {
            self::$map = array_merge(self::$map, $class);
        } else {
            self::$map[$class] = $file;
        }
    }
    
    /**
     * 设置类别名
     * ```
     * setClassAlias('App', 'qpf\\App');
     * $app = new App();
     * ```
     * @param string|array $alias 别名, 批量设置数组
     * @param string $class 类名(带命名空间)
     * @return void
     */
    public static function setClassAlias($alias, $class = null)
    {
        if (is_array($alias)) {
            self::$alias = array_merge(self::$alias, $alias);
        } else {
            self::$alias[$alias] = $class;
        }
    }
    
    /**
     * 设置命名空间所在目录
     * @param string|array $namespace 命名空间, 批量设置数组
     * @param string $path 目录路径
     */
    public static function setNamespace($namespace, $path = null)
    {
        if (is_array($namespace)) {
            self::$namespace = array_merge(self::$namespace, $namespace);
        } else {
            self::$namespace[$namespace] = $path;
        }
        
        krsort(self::$namespace);
    }
    
    /**
     * 设置别名路径
     * @param string|array $namespace 命名空间, 批量设置数组
     * @param string $path 目录路径
     */
    public static function setAliasPath($namespace, $path = null)
    {
        if (is_array($namespace)) {
            self::apaths()->setAliases($namespace);
        } else {
            self::apaths()->setAlias($namespace, $path);
        }
    }
    
    /**
     * 设置自动加载目录
     * - 以键名管理,防止重复
     * @param string $dir 目录路径
     */
    public static function setLoadDir($dir)
    {
        if (is_array($dir)) {
            $dir = array_flip($dir);
            foreach ($dir as $path => $i) {
                self::$dir[$path] = strlen($path);
            }
        } else {
            self::$dir[$dir] = strlen($dir);
        }
        
        krsort(self::$dir);
    }

    /**
     * 类自动加载
     * @param string $className 类名, 分隔符`\`
     */
    public static function autoload($className)
    {
        // 类别名
        if(isset(self::$alias[$className])) {
            return class_alias(self::$alias[$className], $className);
        }
        
        // 类映射
        if (isset(self::$map[$className])) {
            $classFile = self::$map[$className];
            if (self::apaths()->isAlias($classFile)|| !is_file($classFile)) {
                $classFile = self::apaths()->getAlias($classFile);
            }
        } elseif (strpos($className, '\\') !== false) {
            // 类命名空间
            if (!empty(self::$namespace)) {
                $classFile = self::parseNamespace($className);
            }
            // 路径别名
            if (empty($classFile)) {
                $classFile = self::apaths()->getAlias('@' . strtr($className, '\\', '/') . '.php', false);
            }
            // 在目录中查找文件 dir/namespace/class.php
            if (empty($classFile)) {
                $classFile = self::findExtFile($className, '.php');
            }
            
            if ($classFile === false || !is_file($classFile)) {
                return false;
            }
        } else {
            // 在目录中查找文件 dir/name.php
            $classFile = self::findExtFile($className, '.php');
            
            if ($classFile === false || !is_file($classFile)) {
                return false;
            }
        }
        
        // win大小写敏感
        if (strpos(PHP_OS, 'WIN') !== false && pathinfo($classFile, PATHINFO_FILENAME) != pathinfo(realpath($classFile), PATHINFO_FILENAME)) {
            return false;
        }
        
        self::includeFile($classFile);
        
        return true;
    }
    
    /**
     * 搜索指定扩展文件
     * @param string $class 类名
     * @param string $ext 扩展名, 例`.php`
     */
    public static function findExtFile($class, $ext)
    {
        if (strpos($class, '\\') !== false) {
            $class = str_replace('\\', '/', $class);
        }
        // 在注册目录下搜索文件
        foreach (self::$dir as $path => $i) {
            if (is_file($file = $path . DIRECTORY_SEPARATOR . $class . $ext)) {
                return $file;
            }
        }
        
        return false;
    }
    
    /**
     * 解析类名为类文件
     * @param string $name 类名(带命名空间)
     * @return string|false
     */
    public static function parseNamespace($name)
    {
        foreach (self::$namespace as $prefix => $path) {
            $strlen = strlen($prefix);
            if (strncmp($prefix, $name, $strlen) === 0) {
                return $path . strtr(substr($name, $strlen), '\\', DIRECTORY_SEPARATOR) . '.php';
            }
        }
        
        return false;
    }

    /**
     * 注册自动加载
     * @return void
     */
    public static function register()
    {
        spl_autoload_register([self::class, 'autoload'], true, true);
    }
    
    /**
     * 注销自动加载
     * @return void
     */
    public static function unregister()
    {
        spl_autoload_unregister([self::class, 'autoload']);
    }
    
    /**
     * 返回根目录
     * @return string
     */
    public static function getRootPath()
    {
        static $root;
        if($root === null) {
            $root = strstr(__DIR__, 'vendor', true);
        }
        return $root;
    }
    
    /**
     * include载入文件 - 隔离作用域
     *
     * @param string $file
     */
    public static function includeFile($file)
    {
        return include $file;
    }
    
    /**
     * require载入文件 - 隔离作用域
     *
     * @param string $file
     */
    public static function requireFile($file)
    {
        return require $file;
    }
}
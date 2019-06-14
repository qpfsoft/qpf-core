<?php
namespace qpf\builder;

use qpf;
use qpf\base\Core;
use qpf\exceptions\ParameterException;
use qpf\exceptions\ConfigException;
use qpf\builder\template\BuildTemplateInterface;

/**
 * 目录结构生成器
 * 
 * 该类通过以数组的形式, 来定义一组文件目录结构, 其中内置了3种标识.
 * 
 * IROOT : 用于标识根目录, 数组一级必须参数项, 后面将是可选的
 * IDIR : 用于标识将在`IROOT`路径下要生成的文件夹列表
 * IFILE : 用于标识将在`IROOT`路径下要生成的文件列表
 */
class Dirstruc extends Core
{
    /**
     * 根路径标识
     * @var string
     */
    const IROOT = '__root__';
    /**
     * 标识为需创建的目录列表
     * @var string
     */
    const IDIR = '__dir__';
    /**
     * 标识为需创建的文件列表
     * 
     * - `['abc.txt']`若只定义了文件名, 会根据[[fileMap]]映射, 来创建该预定义内置文件.
     * - `['abc.txt' => 'hi!']` 键名作为文件名, 键值为字符串, 则判定为简单文件创建, 采用覆盖创建.
     * - `['abc.txt' => ['$class' => ...]]` 键值为数组, 将尝试解析为模板对象并获取模板内容, 来创建该文件.
     * @var string
     */
    const IFILE = '__file__';
    /**
     * 标识解析方法映射
     * 
     * - 可自定义新标识, 并关联解析的方法, 来扩展.
     * @var array
     */
    private $markMap = [
        self::IROOT => 'iroot',
        self::IDIR  => 'idir',
        self::IFILE => 'ifile',
    ];
    /**
     * 定义内置文件
     * - 格式 `文件名 => 内容模板定义`
     * - 该列表用于定义内置可选文件
     * @var array
     */
    private $fileMap = [];
    /**
     * 目录文件结构
     * @var array
     */
    private $setup = [];
    /**
     * 全局文件系统权限
     * - 默认0755自己可读写执行, 其它只读与执行
     * @var int
     */
    protected $mode = 0755;
    /**
     * 根目录路径
     * 
     * - 该属性用于记录起始根路径
     * - 由[[setup()]]方法设置
     * @var string
     */
    protected $rootPath;
    /**
     * 流程记录
     * @var array
     */
    protected $logs = [];
    
    /**
     * 引导程序
     */
    protected function boot()
    {
        $this->fileMap = array_merge($this->fileMap, $this->getDefaultFileMap());
        $this->markMap = array_merge($this->markMap, $this->getDefaultMarkMap());
        $this->setup = array_merge($this->setup, $this->getDefaultSetup());
    }
    
    /**
     * 绑定内置文件
     * 
     * - 内容必须是模板内容定义数组
     * - 不支持简单文件
     * @param string $name 文件名, 含扩展
     * @param array $config 内容定义
     */
    public function bindInFile($name, array $config)
    {
        $this->fileMap[$name] = $config;
    }
    
    /**
     * 记录或返回操作信息
     * @param string $msg 要记录的信息
     * @return array
     */
    public function log($msg = null)
    {
        if (!is_array($this->logs)) {
            $this->logs = [];
        }
        
        if ($msg === null) {
            return $this->logs;
        }
        
        $this->logs[] = $msg;
    }
    
    /**
     * 创建目录 - 支持多级嵌套
     * @param string $path 目录路径
     * @param int $mode 可选, 目录权限
     * @return bool
     */
    public function createDir($path, $mode = null)
    {
        $mode = $mode ?: $this->mode;
 
        if(!empty($path)) {
            if (is_dir($path)) {
                $this->log('createDir existed:' . $path);
            } else {
                if (mkdir($path, $mode, true)) {
                    $this->log('createDir ok:' . $path);
                } else {
                    $this->log('createDir error:' . $path);
                    return false;
                }
            }
            
        }
        
        return true;
    }

    /**
     * 新建文件 - 覆盖模式
     * @param string $file 文件路径
     * @param string $content 写入内容
     * @return bool
     */
    public function createFile($file, $content = '')
    {
        // 目录不存在, 直接创建文件, 将会报错
        $parts = pathinfo($file, PATHINFO_DIRNAME);
        if (!is_dir($parts)) {
            $this->log('createFile error, dir does not exist: ' . $parts);
            return false;
        }
        
        if (file_put_contents($file, $content) !== false) {
            $this->log('createFile ok: ' . $file);
            return true;
        } else {
            $this->log('createFile error: ' . $file);
            return false;
        }
    }

    /**
     * 创建内置文件
     * @param string $name 内置文件名
     * @param string $path 目录路径
     * @return bool
     */
    public function createBuiltinFile($name, $path)
    {
        if (!$this->fileMap[$name]) {
            $this->log('create unknown built in file:' . $name);
            return false;
        }
        
        if (is_file($path . '/' . $name)) {
            // 内置文件, 不能直接覆盖, 需手动删除, 否则可能丢失重要内容
            $this->log('File is existed:' . $path . '/' . $name);
        } else {
            // 获取该文件的内置配置, 并创建文件
            $this->tplFile($name, $this->fileMap[$name], $path);
        }
        
        return true;
    }
    
    /**
     * 创建内容模板实例对象
     * @param array $config 定义配置
     * @return BuildTemplateInterface
     */
    protected function createFileTemplate($config)
    {
        // 文件内容映射配置错误
        if (!QPF::isQlass($config)) {
            $this->log('config error: miss `$class` key');
            return false;
        }

        $tpl = QPF::create($config);
        
        // 检查是否可用模板
        if ($this->checkFileTemplate($tpl)) {
            $this->log("Tpl: create ok : " . $config['$class']);
            return $tpl;
        } else {
            $this->log("Tpl: not implement BuildTemplateInterface : " . $config['$class']);
            return false;
        }
    }
    
    /**
     * 检查文件模板是否可用
     * @param object $class 模板实例对象
     * @return bool
     */
    protected function checkFileTemplate($class)
    {
        if ($class instanceof BuildTemplateInterface) {
            return true;
        }
        
        return false;
    }
    
    /**
     * 生成目录结构
     * @param array $setup 目录结构
     * @param string $rootPath 可选, 根目录, 覆盖IROOT参数.
     * @return void
     */
    public function setup(array $setup = [], $rootPath = null)
    {
        $setup = !empty($setup) ? $setup : $this->setup;
        
        if (empty($setup)) {
            throw new ParameterException('setup param is null!');
        }
        
        $this->parseRootPath($setup, $rootPath);
        
        // 记录起始根目录路径
        if ($this->rootPath === null) {
            $this->rootPath = $setup[self::IROOT];
        }

        if (empty($this->markMap)) {
            throw new ConfigException('markMap not is null!');
        }

        foreach ($setup as $type => $param) {

            if (isset($this->markMap[$type])) {
                $method = $this->markMap[$type];
                $this->$method($param, $setup[self::IROOT]);
            } elseif (strpos($type, '__') === false) { // 下级目录 ['目录名' => [...内部结构]]
                $path = isset($param[self::IROOT]) ? $param[self::IROOT] : $setup[self::IROOT] . '/'. $type;
                $this->setup($param, $path);
            } else {
                $this->log('unknown setup itme : ' . $type . '=>' . var_export($param, true));
            }
            
        }
    }
    
    /**
     * 确保正确的根路径
     * 
     * 该方法仅限于[[setup]]调用, 因会遍历, 根目录需是相对的!
     * @param array $setup
     * @param string $rootPath
     * @return string
     */
    protected function parseRootPath(&$setup, $rootPath)
    {
        // 优先级:  $rootPath > $setup['__root__']
        // 方法传入的将覆盖, 数组中的值
        // $this->rootPath 属性 仅作为起始根目录
        
        if (!empty($rootPath)) {
            // 去除路径尾部的分隔符
            $setup[self::IROOT] = rtrim($rootPath, '/\\');
        }
        
        if (!isset($setup[self::IROOT])) {
            throw new ConfigException('array miss ' . self::IROOT);
        }
    }
    
    /**
     * 设置根目录并创建
     * @param array $setup 根目录
     * @param string $rootPath 可选, 根目录, 覆盖IROOT参数
     */
    protected function iroot($path, $rootPath = null)
    {
        if (empty($path) && empty($rootPath)) {
            throw new ParameterException('Build not set rootPath!');
        }
        
        // 创建根目录
        $this->createDir($path ?: $rootPath);
    }
    
    /**
     * 在指定路径下创建一个或多个目录
     * @param array|string $dirs 目录名列表
     * @param string $rootPath 根目录
     */
    protected function idir($dirs, $rootPath)
    {
        if (empty($dirs)) {
            return;
        }

        // 遍历创建目录
        if (is_array($dirs)) {
            foreach ($dirs as $dirname) {
                $this->idir($dirname, $rootPath);
            }
            // 创建单个文件夹
        } else {
            
            // 包含目录分隔符, 将不拼接根路径
            if ($this->isPath($dirs)) {
                $filename = $dirs;
            } else {
                $filename = $rootPath . '/' . $dirs;
            }

            // 不存在时创建
            $this->createDir($filename);
        }
    }
    
    /**
     * 在指定目录下创建文件
     * @param array|string $files
     * @param string $rootPath
     * @return void
     */
    protected function ifile($files, $rootPath)
    {
        if (empty($files)) {
            return;
        }
        
        if (is_array($files)) {
            foreach ($files as $index => $name) {
                if (is_numeric($index)) {
                    // 生成内置映射文件
                    $this->ifile($name, $rootPath);
                } else {
                    if (is_string($name)) {
                        // 简单文件 `文件名` => `文件内容`
                        $this->simpleFile($index, $name, $rootPath);
                    } elseif (is_array($name)) {
                        // 模板内容文件 `文件名` => [..模板定义]
                        $this->tplFile($index, $name, $rootPath);
                    }
                }
            }
        } else {
            // 创建内置文件
            $this->createBuiltinFile($files, $rootPath);
        }
    }
    
    /**
     * 创建自定义模板内容文件
     * @param string $name 文件名, 含扩展名
     * @param array $config 模板定义
     * @param string $dirPath 目标位置
     * @return bool
     */
    protected function tplFile($name, array $config, $dirPath)
    {
        $tpl = $this->createFileTemplate($config);
        
        if ($tpl) {
            // 基于模板生成的文件, 不允许覆盖
            if (!is_file($dirPath . '/' . $name)) {
                $this->createFile($dirPath . '/' . $name, $tpl->getContent());
            } else {
                $this->log('create tpl File, existed:' . $dirPath . '/' . $name);
            }
            
            return true;
        } else {
            $this->log('create File error, tpl config error :' . $dirPath . '/' . $name);
            return false;
        }
    }
    
    /**
     * 创建简单文件
     * 
     * - 简单文件始终会覆盖就文件, 便于修改
     * @param string $name 文件名称, 含扩展名
     * @param string $content 内容
     * @param string $dirPath 目录位置
     * @return bool
     */
    protected function simpleFile($name, $content, $dirPath)
    {
        if ($this->isPath($name)) {
            $this->log('rule not match, can not be path!');
            return false;
        }
        
        return $this->createFile($dirPath . '/' . $name, $content);
    }
    
    /**
     * 检查值是否包含路径分隔符
     * @param string $value
     * @return bool
     */
    protected function isPath($value)
    {
        // 包含目录分隔符
        if (strpos($value, '/') !== false || strpos($value, '\\') !== false) {
            return true;
        }
        
        return false;
    }
    
    /**
     * 获取默认标识解析映射
     * @return array
     */
    public function getDefaultMarkMap()
    {
        return [];
    }
    
    /**
     * 获取默认内置文件定义
     * @return array
     */
    public function getDefaultFileMap()
    {
        return [];
    }
    
    /**
     * 获取默认生成的文件目录结构
     * @return string[]|array[]
     */
    public function getDefaultSetup()
    {
        return [];
    }
}
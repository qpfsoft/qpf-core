<?php
namespace qpf\builder;

use QPF;
use qpf\exceptions\ErrorException;
use qpf\builder\template\BuildTemplateInterface;
use qpf\base\Single;
use qpf\exceptions\NotFoundException;

/**
 * 文件目录生成器
 */
class Build extends Single
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
     * 标识为需创建的文件列表, 根据fileMap映射
     * @var string
     */
    const IFILE = '__file__';
    /**
     * 标识为一个模块的文件目录结构列表
     * @var string
     */
    const IModul = '__modul__';
    /**
     * 标识以配置文件模板创建的文件
     * @var string
     */
    const IConf = '__config__';
    /**
     * 标识以控制器模板创建的文件
     * @var string
     */
    const IController = '__controller__';
    /**
     * 标识以模型模板创建的文件
     * @var string
     */
    const IModel = '__model__';
    /**
     * 标识以视图模板创建的文件
     * @var string
     */
    const IView = '__view__';
    
    /**
     * 创建配置
     * @var array
     */
/*     protected static $config = [
        '__file__' => ['common.php', 'config.php', 'database.php'],

        'modul'     => [
            '__file__'   => ['common.php'],
            '__dir__'    => ['controller', 'model', 'view'],
            'controller' => ['Index', 'Test', 'UserType'],
            'model'      => ['User', 'UserType'],
            'view'       => ['index/index'],
        ],
    ]; */
    
    
    /**
     * 目录文件结构
     * @var array
     */
    public $config;
    /**
     * 生成文件映射表
     * @var array
     */
    public $fileMap;
    /**
     * 根目录路径
     * @var string
     */
    public $rootPath;
    /**
     * 工作空间目录路径
     * @var string
     */
    public $appPath;
    /**
     * 配置文件目录
     * @var string
     */
    public $confPath;
    /**
     * 全局文件系统权限
     * @var int
     */
    public $mode;
    
    /**
     * 应用程序命名空间
     * @var string 例`app` 不含模块
     */
    public $appNamespace;
    /**
     * 当前要创建的模块名称
     * 
     * - 值由[buildModule()]方法设定
     * - 不一定是当前请求的模块名
     * @var string
     */
    public $module;
    
    /**
     * 流程记录
     * @var array
     */
    private $logs = [];

    /**
     * 创建基础目录
     */
    public function boot()
    {
        if (!QPF::hasApp() || !is_object(QPF::app())) {
            throw new \qpf\error\ErrorException('应用程序未初始化!');
        }
        
        $app = QPF::app();
        $this->mode = 0755;
        
        $this->appNamespace = $app->getZoneName();
        
        $this->appPath = $app->getZonePath();
        $this->rootPath = $app->getRootPath();
        $this->confPath = $app->getConfigPath();
        
        if (empty($this->config)) {
            $this->config = $this->getConfig();
        }
        
        if (empty($this->fileMap)) {
            $this->fileMap = $this->getFileMap();
        }
        
        // 解析基础目录结构
        $this->parseConfig($this->config);
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
     * 解析文件目录配置并创建
     * @param array $config 文件目录结构
     * @param string $rootPath 根目录, 覆盖IROOT参数.
     * @return void
     */
    protected function parseConfig($config, $rootPath = null)
    {
        // 确定起始根路径
        if ($rootPath === null) {
            if (isset($config[self::IROOT])) {
                $rootPath = $config[self::IROOT];
            } else {
                throw new NotFoundException('Build not set rootPath!');
            }
            // 确保根目录存在
            if (! is_dir($rootPath)) {
                $this->idir($rootPath);
            }
        }
        
        foreach ($config as $i => $param) {
            if ($i == self::IROOT) {
                continue;
            } if ($i == self::IDIR) {
                $this->idir($param, $rootPath);
            } elseif ($i == self::IFILE) {
                $this->ifile($param, $rootPath);
            } elseif ($i == self::IController){
                $this->itpl(self::IController, $param, $rootPath);
            } elseif (strpos($i, '__') === false) {
                // 下级目录结构
                $path = isset($param[self::IROOT]) ? $param[self::IROOT] : $rootPath . '/'. $i;
                $this->parseConfig($param, $path);
            }
        }
        
        return true;
    }
    
    /**
     * 生成目录
     * 
     * ```php
     * # 创建一个目录`/root/abc`
     * idir('abc', '/root');
     * # 创建多个目录 `/root/abc`  `/root/efg` 同级目录
     * idir(['abc', 'efg'], '/root');
     * ```
     * 
     * @param string|array $array 要创建的目录名 或 目录列表
     * @param string $rootPath 根路径
     */
    protected function idir($array, $rootPath = null)
    {
        if (empty($array)) return;
        
        $rootPath = !empty($rootPath) ? rtrim($rootPath, '/\\') : $rootPath;
        
        // 遍历创建目录
        if (is_array($array)) {
            foreach ($array as $dirname) {
                $this->idir($dirname, $rootPath);
            }
        // 创建单个文件夹
        } else {
            // 绝对路径,将不拼接根路径
            if (strpos($array, '/') || strpos($array, '\\')) {
                $filename = $array;
            } else {
                $filename = $rootPath . '/' .$array;
            }
            // 不存在时创建
            if (!is_dir($filename)) {
                mkdir($filename, $this->mode);
                $this->log('Dir: 目录创建成功!' . $filename);
            } else {
                $this->log('Dir: 目录已存在 或 创建失败! ' . $filename);
            }
        }
    }
 
    /**
     * 生成特定类型的文件
     * @param string $type 类型
     * @param string|array $array 文件名或多个用数组表示 
     * @param string $rootPath 根路径
     */
    protected function itpl($type, $array, $rootPath)
    {
        if (empty($type) || empty($array)) return;
        
        if (is_array($array)) {
            foreach ($array as $name) {
                $this->itpl($type, $name, $rootPath);
            }
        } else {
            if ($type == self::IController) {
                $array = ucfirst($array) . 'Controller';
                $class = [
                    'class' => 'qpf\builder\template\Controller',
                    'name' => $array,
                    'namespace' => $this->appNamespace . '\\' . $this->module,
                    'hello' => 'ok',
                ];
            } elseif ($type == self::IModel) {
                $class = [
                    'class' => 'qpf\builder\template',
                ];
            } elseif ($type == self::IView) {
                $class = [
                    'class' => 'qpf\builder\template',
                ];
            } elseif ($type == self::IConf) {
                $class = [
                    'class' => 'qpf\builder\template',
                ];
            }
            // 传入参数
            $class['build'] = $this;
            
            $filename = $rootPath . '/' . $array . '.php';
            
            if (!is_file($filename)) {
                $file = QPF::create($class);
                if ($file instanceof BuildTemplateInterface) {
                    file_put_contents($filename, $file->getContent());
                    $this->log("controller: 生成文件成功! " . $filename);
                } else {
                    $this->log("controller: 生成文件的映射类必须继承 BuildTemplateInterface! " . $filename);
                }
            } else {
                $this->log('controller : 控制器已存在 或 创建失败!' . $filename);
            }
        }
    }
    
    /**
     * 生成文件
     * - 只能生成fileMap中映射的固定文件
     * @param string|array $array 文件名, 或一组文件名
     * @param string $rootPath 根路径
     * @return void
     */
    protected function ifile($array, $rootPath)
    {
        if (empty($array)) return;
        
        $rootPath = !empty($rootPath) ? rtrim($rootPath, '/\\') : $rootPath;

        if (is_array($array)) {
            foreach ($array as $name) {
                $this->ifile($name, $rootPath);
            }
        } else {
            
            // 生成文件必须, 映射一个生成文件的类
            if (isset($this->fileMap[$array])) {
                $filename = $rootPath . '/' .$array;
                
                if (!is_file($filename)) {
                    $file = QPF::create($this->fileMap[$array]);
                    if ($file instanceof BuildTemplateInterface) {
                        file_put_contents($filename, $file->getContent());
                        $this->log("File: 生成文件成功! " . $filename);
                    } else {
                        $this->log("File: 生成文件的映射类必须继承 BuildTemplateInterface! " . $array);
                    }
                } else {
                    $this->log("File: 文件已存在! " . $filename);
                }
                
            } else {
                $this->log("File: 未定义创建文件的映射模板! " . $array);
            }
            
        }
    }

    /**
     * 生成一个模块的文件目录结构
     * 
     * @param string $name 模块名, 小写
     * - 需要确保是注册的模块
     * @return bool
     */
    public function buildModule($name)
    {
        $this->module = strtolower($name);
        return $this->parseConfig($this->buildModuleConfig($this->module));
    }
    
    /**
     * 模块文件结构是否存在
     * @return bool
     */
    public function hasModul($name)
    {
        return is_dir($this->appPath . '/' . $name . '/' . 'controller');
    }
    
    /**
     * 动态生成模块的文件结构配置
     * @param string $name 模块名,目录名
     * @return array
     */
    public function buildModuleConfig($name)
    {
        return [
            self::IROOT => $this->appPath,
            self::IDIR => [$name],
            
            $name => [
                self::IROOT => $this->appPath . '/' . $name,
                self::IDIR => ['controller', 'model', 'view'],
                self::IFILE => ['common.php'],
                
                
                'controller' => [
                    self::IController => ['Index'],
                ],
                'view' => [
                    self::IDIR => [$name],
                ],
            ]
        ];
    }
    
    /**
     * 返回基础目录结构配置
     * @return array
     */
    public function getConfig()
    {
        /**
         * IROOT : 指定当前层级的根路径,
         * IDIR : 指定要在IROOT中生成的目录, 目录可以是绝对路径.
         * IFILE : 指定要在IROOT中生成的文件, 必须在fileMap中映射生成器.
         */
        return [
            self::IROOT => $this->rootPath,
            self::IDIR => [$this->appPath, 'runtime'],
            
            basename($this->appPath) => [
                self::IROOT => $this->rootPath .'/'. basename($this->appPath),
                self::IFILE => ['common.php', 'config.php', 'database.php'],
            ],
        ];
    }
    
    /**
     * 返回基础文件生成模板
     * @return array
     */
    public function getFileMap()
    {
        /**
         * 键名必须与IFILE中设置的一直.
         * 例如: IFILE => ['common.php'] => fileMap['common.php']
         */
        return [
            'common.php' => [
                '$class' => '\qpf\builder\template\ArrayConfigFile',
                'comment' => ['公共文件', '- 用于存放公共方法'],
            ],
            'config.php' => [
                '$class'    => '\qpf\builder\template\ArrayConfigFile',
                'comment' => ['公共配置文件', '- 用于存放所有模块可读取的配置参数'],
            ],
            'database.php' => [
                '$class'    => '\qpf\builder\template\ArrayConfigFile',
                'comment' => ['公共数据库配置', '- 用于存放所有模块可读取的数据库配置参数'],
            ],
        ];
    }
}
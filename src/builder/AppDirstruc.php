<?php
namespace qpf\builder;

/**
 * 应用程序目录结构生成器
 * 
 * 该类依赖[[QPF::create()]]方法, 最低只需执行`QPF::app()`,
 * 即需要依赖解决容器的支持!
 */
class AppDirstruc extends Dirstruc
{
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
     * 定义自定义标识
     * @return array
     */
    public function getDefaultMarkMap()
    {
        return [
            self::IConf         => 'iconf',
            self::IController   => 'icontroller',
            self::IModel        => 'imodel',
            self::IView         => 'iview',
        ];
    }
    
    /**
     * 定义内置文件
     * @return array
     */
    public function getDefaultFileMap()
    {
        return [
            'common.php'    => [
                '$class'    => 'qpf\builder\template\PhpFile',
                'comment'   => ['公共函数库'],
            ],
            'event.php'     => [
                '$class'    => 'qpf\builder\template\ArrayConfigFile',
                'comment'   => ['事件'],
            ],
            'provider.php'  => [
                '$class'    => 'qpf\builder\template\ArrayConfigFile',
                'comment'   => ['服务提供商定义'],
            ],
        ];
    }
    
    /**
     * 生成基础目录结构定义
     * @param string $zoneName 应用工作目录名
     * @param string $rootPath 项目根目录
     * @return array
     */
    public function buildBaseSetup($zoneName, $rootPath)
    {
        /**
         * IROOT : 指定当前层级的根路径,
         * IDIR : 指定要在IROOT中生成的目录, 目录可以是绝对路径.
         * IFILE : 指定要在IROOT中生成的文件, 必须在fileMap中映射生成器.
         */
        return [
            self::IROOT => $rootPath,
            self::IDIR => [$zoneName, 'runtime', 'config', 'web',],
            
            $zoneName => [
                self::IROOT => $rootPath .'/'. $zoneName,
                self::IFILE => ['common.php', 'event.php', 'provider.php'],
            ],
        ];
    }
    
    /**
     * 以appName生成内部目录结构
     * @param string $name 应用名称
     * @return array
     */
    public function buildAppSetup($name)
    {
        return [
            self::IDIR => ['controller', 'model', 'view'],
            self::IFILE => ['common.php'],
            
            
            'controller' => [
                self::IController => ['Index'],
            ],
            'view' => [
                self::IDIR => ['index'],
                
                'index' => [
                    self::IFILE => [
                        'index.php' => [
                            '$class' => 'qpf\builder\template\Html5File',
                            'css' => [
                                '/static/qpf-ui/qpf.css',
                            ],
                            'js' => [
                                '/static/qpf-ui/js/qpf.js',
                            ],
                        ],  
                    ],
                ],
            ],
        ];
    }
    
    /**
     * 以zonePath为根创建应用目录结构
     * @param string $name 模块名
     * @param string $zonePath 应用工作目录
     * @return array
     */
    public function buildModuleSetup(string $name, string $zonePath): array
    {
        return [
            self::IROOT => $zonePath,
            self::IDIR => [$name],
            
            $name => $this->buildAppSetup($name),
        ];
    }
    
    /**
     * 创建配置文件
     * @param string $name 文件名
     * @param string $rootPath 目标路径
     */
    protected function iconf($name, $rootPath)
    {
        
    }
    
    /**
     * 解析IController标识数组项
     * @param array|string $name 文件名
     * @param string $rootPath 目标路径
     */
    protected function icontroller($name, $rootPath)
    {
        if (is_array($name)) {
            foreach ($name as $controllerName) {
                if (!empty($controllerName)) {
                    $this->createController($controllerName, $rootPath);
                }
            }
        } else {
            $this->createController($name, $rootPath);
        }
    }
    
    /**
     * 创建控制器
     * @param string $name 控制器名, 不含后缀与扩展名
     * @param string $rootPath 创建目录位置
     * @return bool
     */
    private function createController($name, $rootPath)
    {
        $name = ucfirst($name) . 'Controller';
        
        $config = [
            '$class'    => 'qpf\builder\template\ControllerFile',
            'className' => $name,
            'namespace' => $this->buildNameSpace('controller', $this->parsePathModuleName($rootPath)),
            'use'       => [
                'qpf\web\Controller',
            ],
            'comment'   => [
                $name . '控制器',
                '',
                '该控制器由系统自动生成!',
            ],
            'hello'     => null, // 默认操作的内容
        ];
        
        return $this->tplFile($name . '.php', $config, $rootPath);
    }
    
    /**
     * 生成命名空间
     * @param string $layer 层
     * @param string $app 应用名
     * @return string
     */
    private function buildNameSpace($layer, $app = null)
    {
        return 'app' . (empty($app) ? '\\' : '\\' . $app . '\\') . $layer;
    }
    
    /**
     * 解析路径获得模块名称
     * @param string $rootPath 路径
     * @return string
     */
    private function parsePathModuleName($rootPath)
    {
        // @app/index/controller => index
        return basename(dirname($rootPath));
    }
    
    /**
     * 创建模型文件
     * @param string $name 文件名
     * @param string $rootPath 目标路径
     */
    protected function imodel($name, $rootPath)
    {
        $name = ucfirst($name) . 'Model';
        
        $config = [
            '$class'    => 'qpf\builder\template\ModelFile',
            'className' => $name, // 类名
            'namespace' => '', // 命名空间
            
        ];

        return $this->tplFile($name, $config, $rootPath);
    }
    
    /**
     * 创建视图文件
     * @param string $name 文件名
     * @param string $rootPath 目标路径
     */
    protected function iview($name, $rootPath)
    {
        $config = [
            '$class' => 'qpf\builder\template',
        ];
        
        return $this->tplFile($name, $config, $rootPath);
    }
    
    
}
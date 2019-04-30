<?php
namespace qpf\configs;

use qpf\base\Core;

/**
 * 环境变量管理器
 */
class Env extends Core
{
    /**
     * 环境变量
     * @var array
     */
    public $env = [];
    
    /**
     * 构造函数
     */
    public function __construct($config = [])
    {
        $this->env = $_ENV;
        parent::__construct($config);
    }

    /**
     * 加载环境配置文件
     * @param string $file
     * @return void
     */
    public function load($file = null)
    {
        $envs = parse_ini_file($file, true);
        $this->set($envs);
    }
    
    /**
     * 设置环境变量
     * @param string|array $name 变量名
     * @param mixed $value 值
     * @return void
     */
    public function set($name, $value = null)
    {
        if (is_array($name)) {
            $name = array_change_key_case($name, CASE_UPPER);
            foreach ($name as $key => $val) {
                if(is_array($val)) {
                    foreach ($val as $k => $v) {
                        $this->env[$key .'_'. strtoupper($k)] = $v;
                    }
                } else {
                    $this->env[$key] = $val;
                }
            }
        } else {
            $name = $this->parseName($name);
            $this->env[$name] = $value;
        }
    }
    
    /**
     * 获得环境变量
     * ```env
     * [app]
     * debug = "true"
     * ```env
     * app_debug = "true"
     * ```
     * // 两种写法, 获取时以下等价:
     * env->get('app.debug');
     * env->get('app_debug'); 
     * ```
     * @param string $name 变量名
     * @param mixed $default 返回的默认值
     * @param string $envPrefix 系统环境变量前缀, 默认`PHP_`
     * @return mixed
     */
    public function get($name = null, $default = null, $envPrefix = 'PHP_')
    {
        if ($name === null) return $this->env;
        
        $name = $this->parseName($name);
        if (isset($this->env[$name])) {
            return $this->env[$name];
        }
        return $this->getEnv($name, $default, $envPrefix);
    }
    
    /**
     * 返回全部环境变量
     * @return array
     */
    public function getEnvs()
    {
        return $this->env;
    }
    
    /**
     * 获取系统设置的环境变量
     * @param string $name 变量名
     * @param mixed $default 返回的默认值
     * @param string $envPrefix 系统环境变量前缀, 默认`PHP_`
     * @return string|boolean|string
     */
    protected function getEnv($name, $default = null, $envPrefix = 'PHP_')
    {
        $name = $envPrefix . $name;
        $env = getenv($name);
        if($env === false) {
            return $default;
        }
        if($env === 'false') {
            $env = false;
        } elseif ($env === 'true') {
            $env = true;
        }
        if (!isset($this->env[$name])) {
            $this->env[$name] = $env;
        }
        return $env;
    }
    
    /**
     * 将点连接符转换
     * @param string $name
     * @return string
     */
    protected function parseName($name)
    {
        return strtoupper(str_replace('.', '_', $name));
    }
}
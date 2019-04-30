<?php
namespace qpf\configs;

use qpf;
use qpf\base\Core;
use qpf\Autoload;

/**
 * 配置管理器
 */
class Config extends Core implements \ArrayAccess
{
    /**
     * 配置集合
     * @var array
     */
    protected $configs = [];
    /**
     * 首选配置组名
     * @var string
     */
    protected $group = 'param';
    /**
     * 配置文件根路径
     * @var string
     */
    protected $path;
    /**
     * 配置扩展名
     * @var string
     */
    protected $ext = '.php';
    /**
     * 是否启用Yaconf模块
     * @var bool
     */
    protected $onYaconf = false;
    /**
     * Yaconf配置项前缀
     * @var string
     */
    protected $yaPrefix;

    
    /**
     * 设置配置保存目录
     * @param string $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }
    
    /**
     * 设置配置扩展名
     * @param string $ext
     * @return void
     */
    public function setExt($ext)
    {
        $this->ext = $ext;
    }
    
    /**
     * 获取配置中指定的参数
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }
    
    /**
     * 添加配置项
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        return $this->set($key, $value);
    }
    
    /**
     * 检查是否存在配置项
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->has($key);
    }

    /**
     * 切换首选配置组
     * @param string $name 配置名称
     * @return void
     */
    public function useGroup($name)
    {
        $this->group = $name;
    }

    /**
     * 获取配置组的参数
     * @param string $name 配置名称
     * @return array
     */
    public function group($name)
    {
        $name = strtolower($name);
        if ($this->onYaconf) {
            $yakey = $this->getYaconfKey($name);
            if(\Yaconf::has($yakey)) {
                $conf = \Yaconf::get($yakey);
                return isset($this->configs[$name]) ? array_merge($this->configs[$name], $conf) : $conf;
            }
        }
        return isset($this->configs[$name]) ? $this->configs[$name] : [];
    }

    /**
     * 设置配置项
     * @param string|array $key 
     * - string : 参数名, 最大支持三维数组的点连接符取值
     * - array : 多个配置项数组
     * @param mixed $value 值, 当使用数组添加时该参数值作为分组名
     * @return mixed
     */
    public function set($key, $value = null)
    {
        if(is_array($key)) {
            if (is_null($value)) {
                return $this->configs = array_merge($this->configs, $key);
            } else {
                if (!isset($this->configs[$value])){
                    return $this->configs[$value] = $key;
                }
                
                return $this->configs[$value] = array_merge($this->configs[$value], $key);
            }
        }
        
        // 无点连接符时, 自动前缀首选组
        $key = $this->getkey($key);
        
        $key = explode('.', $key, 3);
        $key[0] = strtolower($key[0]); // 确保配置组名为小写

        if(count($key) == 2) {
            $this->configs[$key[0]][$key[1]] = $value;
        } else {
            $this->configs[$key[0]][$key[1]][$key[2]] = $value;
        }
        
        return $value;
    }
    
    /**
     * 获取配置项
     * @param string $name 配置项, 支持点获取:
     * - `.group` : 直接获取一级配置
     * - `group.name` : 获取二级配置
     * @param mixed $default 不存在返回的默认值
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        if (empty($key)) return $this->configs;
         
        // 'app.' 获得配置组
        if (substr($key, -1) == '.') {
            return $this->group(substr($key, 0, -1));
        }
        
        if ($this->onYaconf) {
            $yakey = $this->getYaconfKey($key);
            if (\Yaconf::has($yakey)) {
                return \Yaconf::get($yakey);
            }
        }
        
        $key = $this->getkey($key);
        $key = explode('.', $key);
        $key[0] = strtolower($key[0]); // 确保配置组名为小写
        $conf = $this->configs;
        foreach ($key as $var) {
            if (isset($conf[$var])) {
                $conf = $conf[$var];
            } else {
                return $default;
            }
        }
        
        return $conf;
    }

    /**
     * 解析配置文件
     * @param string $config 配置内容或路径
     * @param string $type 配置类型, 即扩展名
     * @param string $group 所属分组
     * @return mixed
     */
    public function parse($config, $type = null, $group = null)
    {
        $type = !is_null($type) ?: pathinfo($config, PATHINFO_EXTENSION);
        
        $parse = Autoload::create($type, '\\qpf\configs\\parse\\', $config);
        
        return $this->set($parse->parse(), $group);
    }
    
    /**
     * 加载配置文件
     * @param string $file 配置名
     * @param string $group 所属分组
     * @return mixed
     */
    public function load($file, $group = null)
    {
        if(is_file($file)) {
            $filePath = $file;
        } elseif (is_file($this->path . $file . $this->ext)) {
            $filePath = $this->path . $file . $this->ext;
        }
        
        if(isset($filePath)) {
            return $this->loadFile($file, $group);
        } elseif ($this->onYaconf && \Yaconf::has($file)) {
            return $this->set(\Yaconf::get($file), $group);
        }
        
        return $this->configs;
    }
    
    /**
     * 开启Yaconf扩展支持
     * @param string $prefix 配置项前缀
     * @return void
     */
    public function onYaconf($prefix = '')
    {
        $this->onYaconf = true;
        $this->yaPrefix = $prefix ?: $this->yaPrefix;
    }
    
    /**
     * 获取Yaconf配置项
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public function yaconf($key, $default = null)
    {
        if ($this->onYaconf) {
            $yakey = $this->getYaconfKey($key);
            
            if (\Yaconf::has($yakey)) {
                return \Yaconf::get($yakey);
            }
        }
        
        return $default;
    }
    
    /**
     * 获取完整的yaconf配置项名
     * @param string $key
     * @return string
     */
    public function getYaconfKey($key)
    {
        if($this->onYaconf) {
            return $this->yaPrefix . '.' . $key;
        }
        
        return $key;
    }
    
    /**
     * 加载配置文件
     * @param string $file
     * @param string $group
     * @return mixed
     */
    protected function loadFile($file, $group)
    {
        $name = strtolower($group);
        $type = pathinfo($file, PATHINFO_EXTENSION);
        
        if($type == 'php') {
            return $this->set(include $file, $group);
        } elseif ($type == 'yaml' && function_exists('yaml_parse_file')) {
            return $this->set(yaml_parse_file($file), $group);
        }
        
        return $this->parse($file, $type, $group);
    }
    
    /**
     * 确保配置项有首选分组
     * @param string $key
     * @return string
     */
    protected function getkey($key)
    {
        if (strpos($key, '.') === false) {
            return $this->group . '.' . $key;
        }
        return $key;
    }

    /**
     * 移除配置项
     * @param string $key 配置名, 可用点连接符
     * @return void
     */
    public function remove($key)
    {
        $key = $this->getkey($key);
        $key = explode('.', $key, 3);
        
        if (count($key) == 2) {
            unset($this->configs[$key[0]][$key[1]]);
        } else {
            unset($this->configs[$key[0]][$key[1]][$key[2]]);
        }
    }
    
    /**
     * 清空指定配置组或全部
     * @param string $group 仅配置组
     * @return void
     */
    public function reset($group = null)
    {
        if (is_null($group)) {
            $this->configs = [];
        } elseif (array_key_exists($group, $this->configs)) {
            $this->configs[$group] = [];
        }
    }

    /**
     * 判断是否设置指定元素
     * @param string $name 数组元素名
     * @return boolean
     */
    public function has($key)
    {
        $key = $this->getkey($key);
        return null !== $this->get($key);
    }

    /**
     * 设置一个偏移位置的值
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }
    
    /**
     * 获取一个偏移位置的值
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }
     
    /**
     * 检查一个偏移位置是否存在
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }
    
    /**
     * 复位一个偏移位置的值
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($key)
    {
        $this->remove($key);
    }
}
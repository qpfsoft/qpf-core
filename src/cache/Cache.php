<?php
namespace qpf\cache;

use qpf;
use qpf\exception\NotFoundException;
use qpf\base\Core;
use qpf\psr\cache\CacheItemPoolInterface;
use qpf\psr\cache\CacheItemInterface;

/**
 * 缓存管理类
 *
 */
class Cache extends Core implements CacheItemPoolInterface
{
    /**
     * 缓存项列表
     * @var CacheItem[]
     */
    protected $data = [];
    /**
     * 延期保存的缓存项列表
     * @var CacheItem[]
     */
    protected $deferred = [];
    /**
     * 缓存实例
     * @var array
     */
    protected $instance = [];
    /**
     * 缓存选项
     * @var array
     */
    protected $option = [];
    /**
     * 缓存处理程序
     * @var CacheHandler
     */
    protected $handler;
    /**
     * 底层驱动
     * @var array
     */
    protected $driver = [
        'file'      => __NAMESPACE__ . '\type\File',
        'memcache'  => __NAMESPACE__ . '\type\Memcache',
        'memcached' => __NAMESPACE__ . '\type\Memcached',
        'redis'     => __NAMESPACE__ . '\type\Redis',
        'sqlite'    => __NAMESPACE__ . '\type\Sqlite',
        'wincache'  => __NAMESPACE__ . '\type\Wincache',
    ];
    
    /**
     * 构造函数
     * @param array $option 缓存选项
     * @param array $config 对象配置
     */
    public function __construct(array $option = [], array $config = [])
    {
        $this->option = $option ?: QPF::app()->config->group('cache');
        $this->useConfig($config);
        $this->init($config);
    }
    
    /**
     * 自动初始化缓存
     * ```
     * [
     *      'type'          => 'complex', // 混合
     *      'default'       => ['type' => 'file', ...], // 默认配置
     *      '自定义标识'     => [...], // 自定义配置1
     *      '标识2'         => [...], // 自定义配置2
     * ]
     * ```
     * @param array $options 配置选项
     * @param bool $reset 是否重新连接
     * @return
     */
    public function init(array $options = [], $reset = false)
    {
        if($this->handler === null || $reset) {
            if($options['type'] === 'complex') {
                $default = $options['default'];
                $options =  isset($options[$default['type']]) ? $options[$default['type']] : $default;
            }
            $this->handler = $this->connect($options);
        }
        
        return $this->handler;
    }
    
    /**
     * 连接缓存
     * @param array $options 缓存选项
     * @param bool $reset 是否重置连接
     * @return CacheHandler
     */
    public function connect(array $options = [], $reset = false)
    {
        $name = md5(serialize($options));
        
        if ($reset || !isset($this->instance[$name])) {
            $type = strtolower(empty($options['type']) ? 'file' : $options['type']);
            
            if(!isset($this->driver[$type])) {
                throw new NotFoundException($type, 'Cache type');
            }
            
            $this->instance[$name] = QPF::create($this->driver[$type], $options);
        }
        
        return $this->instance[$name];
    }
    
    /**
     * 设置缓存选项
     * @param array $option
     * @return void
     */
    public function setOptions(array $option)
    {
        $this->option = array_merge($this->option, $option);
    }
    
    /**
     * 切换缓存类型 - 需要设置cache.type为complex
     * @param string $name 缓存标识
     * @param bool $reset 是否重置连接
     */
    public function store($name = null, $reset = false)
    {
        if (!empty($name) && $this->option['type'] == 'complex') {
            return $this->connect($this->option[$name], $reset);
        }
        
        return $this->init();
    }
    
    /**
     * 返回指定缓存项
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->init()->get($key);
    }
    
    /**
     * 设置缓存项
     * @param string $key 缓存变量名 
     * @param mixed $value 存储数据
     * @param null|int|\DateInterval $expire 有效时间 0为永久 
     * @return bool
     */
    public function set($key, $value, $expire = null)
    {
        return $this->init()->set($key, $value, $expire);
    }
    
    /**
     * 删除缓存项
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        return $this->init()->rm($key);
    }
    
    /**
     * 判断缓存项是否存在
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return $this->init()->has($key);
    }
    
    /**
     * 缓存标签
     * @param string|array $name 标签名
     * @return CacheHandler
     */
    public function tag($name)
    {
        return $this->init()->tag($name);
    }
    
    /**
     * 返回指定的缓存项
     * @param string $key 缓存标识
     * @return CacheItem
     */
    public function getItem($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
            
        $item = new CacheItem($key);
        
        if ($this->has($key)) {
            $item->set($this->get($key));
        }
        
        $this->data[$key] = $item;
        
        return $item;
    }
    
    /**
     * 返回一个或多个缓存项
     * @param array $keys 缓存标识集合
     * @return array|\Traversable
     */
    public function getItems(array $keys = [])
    {
        $result = [];
        
        foreach ($keys as $key) {
            $result[] = $this->getItem($key);
        }
        
        return $result;
    }
    
    /**
     * 检测缓存项是否存在
     * @param string $key
     * @return bool
     */
    public function hasItem($key)
    {
        return $this->has($key);
    }
    
    /**
     * 清空缓存池
     * @return bool
     */
    public function clear()
    {
        return $this->init()->clear();
    }
    
    /**
     * 删除指定缓存项
     * @param string $key
     * @return bool
     */
    public function deleteItem($key)
    {
        return $this->delete($key);
    }
    
    /**
     * 删除一个或多个缓存项
     * @param array $keys
     * @return bool
     */
    public function deleteItems(array $keys)
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        
        return true;
    }
    
    /**
     * 立即保存缓存项 - 数据持久化
     * @param CacheItem|CacheItemInterface $item
     * @return bool
     */
    public function save(CacheItemInterface $item)
    {
        if ($item->getKey()) {
            return $this->set($item->getKey(), $item->get(), $item->getExpire());
        }
        
        return false;
    }
    
    /**
     * 稍后保存缓存项 - 稍后数据持久化
     * @param CacheItem|CacheItemInterface $item
     * @return bool
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $this->deferred[$item->getKey()] = $item;
        return true;
    }
    
    /**
     * 提交所有等待持久化的缓存项
     * @return bool
     */
    public function commit()
    {
        foreach ($this->deferred as $key => $item) {
            $result = $this->save($item);
            unset($this->deferred[$key]);
            
            if (false === $result) {
                return false;
            }
        }
        return true;
    }
    
    public function __call($method, $args)
    {
        return call_user_func_array([$this->init(), $method], $args);
    }
    
    public function __destruct()
    {
        if (!empty($this->deferred)) {
            $this->commit();
        }
    }
}
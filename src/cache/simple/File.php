<?php
namespace qpf\cache\simple;

use qpf;
use qpf\file\Directory;
use qpf\exception\PathException;

/**
 * 简单文件缓存
 */
class File implements CacheInterface
{
    /**
     * 缓存目录
     * @var string
     */
    protected $path;
    
    /**
     * 连接
     * @return mixed
     */
    public function connect()
    {
        $this->path(QPF::$app->getConfigPath() . DIRECTORY_SEPARATOR .  'simple_cache');
    }
    
    /**
     * 设置缓存目录
     * @param string $path
     * @return $this
     */
    public function path($path)
    {
        $this->path = $path;
        if(!Directory::single()->createDir($this->path)) {
            throw new PathException('Cache dir create error or dir not write');
        }
        
        return $this; 
    }
    
    /**
     * 写入缓存到文件
     * @param string $name 缓存标识
     * @param mixed $value 缓存值
     * @param int $expire 有效时间(秒), `0`代表永久有效, 默认保存1小时
     * @return mixed
     */
    public function set($name, $value, $expire = 3600)
    {
        $file = $this->getFile($name);
        
        // 10位整数0, 不够10位的话,用0来占位
        $expire = sprintf("%010d", $expire);
        $value = $expire . serialize($value);
        
        return file_put_contents($file, $value);
    }
    
    /**
     * 读取缓存文件的值
     * @param string $name 缓存标识
     * @return mixed
     */
    public function get($name)
    {
        $file = $this->getFile($name);
        
        // 不存在或不可读
        if(!is_file($file) || !is_readable($file)) {
            return null;
        }
        
        $value = file_get_contents($file);
        // 有效期截取前10位
        $expire = intval(substr($value, 0, 10));
        // 获取文件修改时间
        $mtime = filemtime($file);
        
        // 有效期大于0, 写入时间+有效期 小于当前时间 判断为失效
        if($expire > 0 && $mtime + $expire < time()) {
            @unlink($file);
            return false;
        }
        
        return unserialize(substr($value, 10));
    }
    
    /**
     * 删除缓存文件
     * @param string $name 缓存标识
     * @return mixed
     */
    public function delete($name)
    {
        $file = $this->getFile($name);
        
        return Directory::single()->deleteFile($file);
    }
    
    /**
     * 刷新缓存池 - 删除全部缓存文件
     * @return mixed
     */
    public function flush()
    {
        return Directory::single()->deleteDir($this->path);
    }
    
    /**
     * 获取缓存文件路径
     * @param string $name 缓存标识名称
     * @return string
     */
    protected function getFile($name)
    {
        return $this->path . DIRECTORY_SEPARATOR . md5($name) . '.php';
    }
}
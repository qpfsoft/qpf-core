<?php
namespace qpf\cache\simple;

use qpf;
use qpf\db\Db;

/**
 * Mysql 缓存
 */
class Mysql implements CacheInterface
{
    /**
     * 数据库连接
     * @var Db
     */
    protected $link;
    
    /**
     * 连接
     * @return mixed
     */
    public function connect()
    {
        $cache_table = QPF::$app->config->get('cache.table');
        $this->link = QPF::$app->db->table($cache_table);
    }
    
    /**
     * 设置缓存
     * @param string $name 缓存标识
     * @param mixed $value 缓存值
     * @param int $expire 有效期
     * @param array $fields 预设字段值
     * @return mixed
     */
    public function set($name, $value, $expire = 0, array $fields = [])
    {
        $data = array_merge($fields, [
            'name'  => $name,
            'data'  => serialize($value),
            'create_time'   => time(),
            'expire'    => $expire,
        ]);
        
        return $this->link->replace($data) ? true : false;
    }
    
    /**
     * 获取缓存
     * @param string $name 缓存标识
     * @return mixed
     */
    public function get($name)
    {
        $data = $this->link->where('name', $name)->first();
        // 缓存不存在
        if (empty($data)) {
            return null;
        // 缓存过期
        } elseif ($data['expire'] > 0 && $data['create_time'] + $data['expire'] < time()) {
            $this->link->where('name', $name)->delete();
        } else {
            return unserialize($data['data']);
        }
    }
    
    /**
     * 删除缓存
     * @param string $name 缓存标识
     * @return mixed
     */
    public function delete($name)
    {
        return $this->link->where('name', $name)->delete();
    }
    
    /**
     * 刷新缓存
     * @return mixed
     */
    public function flush()
    {
        $cache_table = QPF::$app->config->get('cache.table');
        return $this->link->schema()->truncate($cache_table);
    }
}
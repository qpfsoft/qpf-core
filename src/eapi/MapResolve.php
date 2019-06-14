<?php
namespace qpf\eapi;

use qpf\exception\ConfigException;
use qpf\exception\ParamException;

/**
 * Api地图解析
 */
class MapResolve
{
    /**
     * apiMap配置列表
     * @var array
     */
    protected $apiMap;
    /**
     * map文件路径
     * @var string
     */
    protected $mapPath;
    /**
     * url别名
     * @var array
     */
    protected $urlAlias;
    /**
     * 解析后的apiMap对象列表
     * @var MapInstance[]
     */
    protected $apiMapList;

    /**
     * 获取指定接口对象
     * @param string $id 接口标识, 使用`.`点连接符来访问子接口
     * @return MapInstance
     */
    public function api($id)
    {
        // 支持点获取子接口
        if (strpos($id, '.') !== false) {
            list ($group, $item) = explode('.', $id);
        } else {
            $group = $id;
            $item = null;
        }
        
        // 获取前准备
        if ($this->apiMapList === null) {
            $this->apiMapList = $this->parseApiMap($this->getApiMap());
        }
        echor($this->apiMapList);
        
        if (isset($this->apiMapList[$group])) {
            $main = $this->apiMapList[$group];
            if (! empty($item)) {
                $main = $main->getSubItem($item);
            }
            
            return $main;
        } else {
            throw new ParamException('Eapi Api ID unknown : `' . $id . '`');
        }
    }

    
    /**
     * 检测url值是否使用了别名并替换
     * @param string $url 域名
     * @return string
     */
    public function urlAlias($url)
    {
        // url域名不包含点, 判定为URL别名
        if (strpos($url, '.') === false) {
            $url = isset($this->urlAlias[$url]) ? $this->urlAlias[$url] : $url;
        } else {
            $array = explode('.', $url);
            foreach ($array as $i => $val) {
                if(strpos($val, '_') !== false) {
                    $array[$i] = $this->urlAlias($val);
                }
            }
            
            $url = implode('.', $array);
        }
        
        return $url;
    }
    
    /**
     * 设置map文件路径
     * @param string $path
     * @return void
     */
    public function setMapPath($path)
    {
        $this->mapPath = $path;
    }
    
    /**
     * 获取map文件路径
     * @throws ConfigException
     * @return string
     */
    public function getMapPath()
    {
        if ($this->mapPath === null) {
            $path =  __DIR__ . '/map.php';
            if(!is_file($path)) {
                throw new ConfigException(__CLASS__ . ' `$mappath` no is file!');
            }
            $this->mapPath = $path;
        }
        
        return $this->mapPath;
    }
    
    /**
     * 获取api接口配置
     * @return array
     */
    public function getApiMap()
    {
        // 载入map文件
        if ($this->apiMap === null) {
            $map = $this->getMapPath();
            if(is_file($map)) {
                $map = include $map;
                // 准备url别名
                if (isset($map['__url_alias__'])) {
                    $alias = is_array($map['__url_alias__']) ? $map['__url_alias__'] : [];
                    unset($map['__url_alias__']);
                }
            } else {
                $map = [];
            }
            $this->apimap = $map;
            $this->urlAlias = isset($alias) ? $alias : [];
        }
        
        return $this->apimap;
    }
    
    /**
     * 将api地图转换为地图实例
     * @param array $apiMap 导入
     * @return MapInstance[]
     */
    public function parseApiMap(array $apiMap = [])
    {
        $map = empty($apiMap) ? $this->getApiMap() : $apiMap;
        
        $apiMapList = [];
        
        foreach ($map as $id => $conf) {
            // 不处理非数组类型的配置
            if (!is_array($conf)) { continue; }
            
            // 先从配置中截取出grup配置
            if(isset($conf['group'])) {
                if(!empty($conf['group'])) {
                    $group_conf = $conf['group'];
                    unset($conf['group']);
                } else {
                    throw new ConfigException('Eapi map config error! for `group` is empty !');
                }
            } else {
                $group_conf = null;
            }
            
            // 自动添加接口ID标识
            $conf['id'] = $id;
            // 预先解析
            $this->parseApiMapConfig($conf);
            // 创建接口地图对象实例
            $groupMap = new MapInstance($conf);
            
            // 存在子接口
            if($group_conf !== null) {
                $subItem = [];
                foreach ($group_conf as $sid => $sconf) {
                    $sconf['group'] = $groupMap;
                    $this->parseApiMapConfig($sconf);
                    $subItem[$sid] = new MapInstance($sconf);
                }
                
                $groupMap->subItem = $subItem;
            }
            
            $apiMapList[$id] = $groupMap;
        }
        
        return $apiMapList;
    }
    
    /**
     * 解析Api接口配置
     * @param array $conf
     * @return void
     */
    private function parseApiMapConfig(&$conf)
    {
        // 解析host别名
        if(isset($conf['host'])) {
            $conf['host'] = $this->urlAlias($conf['host']);
        }
    }
}
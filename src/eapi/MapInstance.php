<?php
namespace qpf\eapi;

use qpf\base\Core;
use qpf\exception\ParamException;

/**
 * Api地图实例
 */
class MapInstance extends Core
{
    /**
     * 接口id名称, 唯一
     * @var string
     */
    public $id;
    /**
     * 接口ID的字符串描述
     * @var string
     */
    public $name; 
    /**
     * 是否安全链接 {false:'http', true:'https'}
     * @var bool
     */
    public $https;
    /**
     * 接口域名`www.domain.com`
     * @var string
     */
    public $host;
    /**
     * 接口路径信息`/dir/file.ext`
     * @var string
     */
    public $path;
    /**
     * 完整域名不带查询参数 `http://www.domain.com/dir/file.ext`
     * @var string
     */
    public $url;
    /**
     * 查询参数, GET参数
     * @var array
     */
    public $param;
    /**
     * 当值不为NULL代表, 可获取公共参数
     * @var MapInstance
     */
    public $group;
    /**
     * 子接口
     * @var MapInstance[]
     */
    public $subItem;
    /**
     * 返回类型
     * @var string
     */
    public $return_type;
    /**
     * 返回内容的字符集
     * @var string
     */
    public $return_charset;
    /**
     * 接口返回内容的缓存
     * @var mixed
     */
    public $return_content;
    
    /**
     * 是否有可用公共参数
     * @return bool
     */
    public function isGroup()
    {
        return $this->group === null;
    }
    
    /**
     * 获取公共属性
     * @param string $name 属性名
     * @param mixed $defautl 默认值
     * @param bool $throw 未知属性抛出异常
     * @return mixed
     */
    protected function getGroupAttr($attr, $defautl = null, $throw = true)
    {
        // 可用属性
        $attrs = ['https', 'host', 'path', 'url', 'param'];
        
        if (in_array($attr, $attrs)) {
            if(null === $this->$attr) {
                return $this->isGroup() ? $this->group->$attr : $defautl;
            } else {
                return $this->$attr;
            }
        } elseif($throw) {
            throw new ParamException(__CLASS__ . ' get unknown property ' . $attr);
        }
        
        return $defautl;
    }
    
    /**
     * 获取子接口
     * @param string $item 子接口ID
     * @throws ParamException
     * @return $this
     */
    public function getSubItem($id)
    {
        if (isset($this->subItem[$id])) {
            return $this->subItem[$id];
        } else {
            throw new ParamException('Eapi unknown subItem ' . $id);
        }
    }

    /**
     * 获取Api说明
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * 设置Api说明
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * 获取协议
     * @return string
     */
    public function scheme()
    {
        if($this->https === null) {
            $this->https = $this->getGroupAttr('https');
        }
        return $this->https ? 'https' : 'http';
    }
    
    /**
     * 获取主机名, 即域名
     * @return string
     */
    public function host()
    {
        if ($this->host === null) {
            $this->host = $this->getGroupAttr('host');
        }
        
        return $this->host;
    }
    
    /**
     * 获取Api路径信息
     * @return string
     */
    public function path()
    {
        if ($this->path === null) {
            $this->path = $this->getGroupAttr('path');
        }
        
        return $this->path;
    }
    
    /**
     * 获取Api请求参数
     * @param string $name 参数名
     * @return array
     */
    public function param($name = null)
    {
        if ($name === null) {
            return $this->param;
        }
        
        if (isset($this->param[$name])) {
            $value = $this->param[$name];
            if(is_string($value)) {
                if(!$this->isGroup()) {
                    throw new ParamException('string type value need group param definition');
                }
                $value = $this->group->param[$name];
            }
            return $value;
        } else {
            throw new ParamException('get no set param ' . $name);
        }
    }
    
    /**
     * 生成URL查询参数字符串
     * ```
     * [
     *      'set'     => true, // (可选) 参数是否必须, 未设定时, 默认为 `false`
     *      'default' => null, // (可选) 默认值, 未设定时, 默认值为`null`
     * ]
     * ```
     * - 必选参数若未设置值, 将采用默认值.
     * - 必选参数若未设置也没有默认值, 将抛出异常
     * - 可选参数, 若默认值为`null`, 未设置时将会忽略该参数.
     * - 可选参数, 若默认值为""空字符串, 未设置时将`..&name=&..`传递参数名
     * 
     * @param array $param 参数配置集合
     * @param array $config 传入的参数值
     * @return array
     */
    protected function buildQueryParam(array $param, $config = [])
    {
        $query = [];

        foreach ($param as $name => $option) {
            // 直接使用传入的参数值
            if(isset($config[$name])) {
                $query[$name] = $config[$name];
            } elseif (!is_array($option)) {
                // 值为字符串直接采用
                $query[$name] = $option;
            } else {
                // 未设定必选参数, 将默认为可选参数
                $o_set = isset($option['set']) ? $option['set'] : false;
                // 如果默认值为空字符串, 也将设置都查询参数中, 例`..&name=&..`
                $o_default = (isset($option['default']) && $option['default'] !== null) ? $option['default'] : null;
                
                // 无默认值, 也未传入将抛出异常
                if($o_default === null) {
                    if($o_set) {
                        throw new ParamException('Eapi Pull Api query param miss `'. $name .'` of value');
                    } else {
                        continue;
                    }
                }
                
                $query[$name] = $o_default;
            }
        }

        return $query;
    }
    
    /**
     * 查询参数转换为字符串格式
     * - bool值为转换为 1 或 0 
     * - NULL值参数将会被忽略
     * @param array $query
     * @return string
     */
    public function queryToString(array $query)
    {
        return http_build_query($query);
    }

    /**
     * 获取Api地址
     * @return string
     */
    public function url()
    {
        if($this->url === null) {
            $url = $this->getGroupAttr('url', null);
            if (empty($url)) {
                $url = $this->scheme() . '://' . $this->host() . $this->path();
            }
            // 注意: 未设置`url`值时, 不要用来缓存api地址, 因为启用该值将会将忽略其它参数;
            // 该值仅允许在map配置中定义
            return $url;
        } else {
            return $this->url;
        }
    }
    
    /**
     * 获取Api接口URL
     * 
     * @param array $config 传入参数值
     * @param string|array $param 重新指定接口参数,
     * - string : 'user=admin&pwd=123'
     * @return string
     */
    public function getApi($config = [], $param = null)
    {
        $param = $param === null ? $this->param() : $param;

        if(is_string($param)) {
            $api = $this->url() . '?' . $param;
        } else {
            $api = $this->url() . '?' . $this->queryToString($this->buildQueryParam($param, $config));
        }
        
        return $api;
    }
    
    /**
     * 调用当前Api接口
     * @string array|string $config 接口参数配置
     * @param bool $isPost 是否POST请求
     * @return mixed 返回接口数据
     */
    public function pull($config = [], $isPost = false)
    {
        // TODO 缺少网络调用
        $curl = new \qpf\browser\network\Curl();
        /* - 显示支持传入参数, get与post处理不同, 
         * 
         */
        if($isPost) {
            $resutl = $curl->post($this->url(), $this->param());
        } else {
            $resutl = $curl->get($this->getApi($config));
        }
        
        return $resutl;
    }

}
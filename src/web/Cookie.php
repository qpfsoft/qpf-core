<?php
namespace qpf\web;

use qpf\base\Core;

/**
 * Cookie 管理器
 * 
 * - 有效期/保存时间:
 * 时间戳: 即 time() + 有效秒数
 * time()+60*60*24*30 就是设置 Cookie 30 天后过期。
 * 如果设置成零，或者忽略参数， Cookie 会在会话结束时过期, 也就是关掉浏览器时;
 * 
 * - 保存路径:
 * 默认为当前设置cookie时页面的路径，'/'表示整个域名有效，'/A'表示A分类目录下的页面有效。
 * 
 * - 有效域名:
 * 只有指定的域名才可以拿到cookie，默认所有域名都可以拿到。 如，`www.php.com`，也可是`.php.com`;
 * www.x.com : 对 `www.x.com/*` 子域名和 `*.www.x.com` 三级域名 有效;
 * x.com : 对整个域名生效, 即 `*.x.com/*`;
 * 
 * - secure(启用安全传输):
 * 当使用https安全链接时, 才会设置该cookie
 * 
 * - httponly设置:
 * 开启后通过js脚本将无法读取到cookie信息，这样能有效的防止XSS攻击
 * 
 * - 注意事项:
 * 设置cookie之前不能有任何输出.
 * 删除cookie时需要使用相同的参数设置. 例如 路径等. 否则可能无法删除
 */
class Cookie extends Core
{
    /**
     * Cookie默认选项
     * @var array
     */
    protected $options = [];
    /**
     * cookie数据
     * @var array
     */
    protected $cookies = [];
    
    /**
     * 初始化
     */
    protected function boot()
    {
        if (empty($this->options)) {
            $this->options = $this->getDefaultOptions();
        }
    }

    /**
     * 设置cookie前缀名称
     * @param string $prefix
     * @return void;
     */
    public function setPrefix($prefix)
    {
        $this->options['prefix'] = $prefix;
    }
    
    /**
     * 返回cookie前缀名称
     * @return string
     */
    public function getPrefix()
    {
        return $this->options['prefix'];
    }
    
    /**
     * 返回cookie数据
     * @return array
     */
    public function getCookie()
    {
        return $this->cookies;
    }
    
    /**
     * 获取cookie完整名字
     * @param string $name 名称
     * @param string $prefix 前缀名
     * @return string
     */
    public function getKey($name, $prefix = null)
    {
        return ($prefix === null ? $this->options['prefix'] : $prefix) . $name;
    }
    
    /**
     * 设置Cookie
     * @param string $name 名称
     * @param string $value 值
     * @param string|int $option 选项, 可能的值:
     * - `int` :  代表仅设置有效期
     * - `null` : 默认, 代表使用全局参数设置
     * - `array` : 其他选项[prefix,path,domain,secure,httponly,setcookie]
     */
    public function set($name, $value = '', $option = null)
    {
        if($option !== null) {
            if (is_numeric($option)) {
                $option = ['expire' => $option];
            }
            $option = array_merge($this->options, $option);
        } else {
            $option = $this->options;
        }
        
        // 将数组类型转换为字符串
        if (is_array($value)) {
            $value = $this->encodeArray($value);;
        }
        
        $this->setCookie($option['prefix'] . $name, (string) $value, $this->getExpire($option['expire']), $option);
    }
    
    /**
     * 获取cookie
     * @param string $name 名称
     * @param mixed $default 默认值
     * @param string $prefix 前缀名
     * @return mixed
     */
    public function get($name = null, $default = null, $prefix = null)
    {
        $key = $this->getKey($name, $prefix);
        
        if ($name === null) {
            if ($prefix === null) {
                // 仅返回指定前缀的cookie
                $result = [];
                foreach ($_COOKIE as $i => $v) {
                    if (strpos($i, $prefix) === 0) {
                        $result[$i] = $v;
                    }
                }
            } else {
                $result = $_COOKIE;
            }
        } elseif (isset($_COOKIE[$key])) {
            $result = $this->decodeArray($_COOKIE[$key]);
        } else {
            $result = $default;
        }
        
        return $result;
    }
    
    /**
     * 记录Cookie数据
     * @param string $name 名称
     * @param string $value 值
     * @param int $expire 有效期, 时间戳
     * @param array $option 其他选项, [path,domain,secure,httponly]
     * @return void
     */
    protected function setCookie($name, $value, $expire, array $option)
    {
        $this->cookies[$name] = [$value, $expire, $option];
    }
    
    /**
     * 永久保存cookie数据
     * @param string $name
     * @param mixed $value
     * @param array $option 其他选项, [path,domain,secure,httponly]
     * @return void
     */
    public function forever($name, $value, array $option = [])
    {
        $option['expire'] = 315360000; // 1年
        $this->set($name, $value, $option);
    }
    
    /**
     * 检查session是否设置
     * @param string $name 名称
     * @param string $prefix 前缀名
     * @return bool
     */
    public function has($name, $prefix = null)
    {
        $key = $this->getKey($name, $prefix);
        
        return isset($_COOKIE[$name]);
    }
    
    /**
     * 删除cookie
     * @param string $name 名称
     * @param string $prefix 前缀名
     * @return void
     */
    public function delete($name, $prefix = null)
    {
        $key = $this->getKey($name, $prefix);
        
        $this->setCookie($key, '', time() - 3600, $this->options);
    }
    
    /**
     * 清空cookie
     * @param string $prefix 前缀名, 仅清除指定前缀名的cookie
     * @return void
     */
    public function clear($prefix = null)
    {
        if (empty($_COOKIE)) {
            return;
        }
        
        $prefix = $prefix === null ? $this->prefix : $prefix;
        
        if ($prefix) {
            foreach ($_COOKIE as $name => $value) {
                if (strpos($name, $prefix) === 0) {
                    $this->setCookie($name, '', time() - 3600, $this->options);
                }
            }
        }
    }
    
    /**
     * 保存Cookie到客户端
     * @return void
     */
    public function save()
    {
        foreach ($this->cookies as $name => $set) {
            list($value, $expire, $option) = $set;
            
            setcookie($name, $value, $expire, $option['path'], $option['domain'], $option['secure'] ? true : false, $option['httponly'] ? true : false);
        }
    }
    
    /**
     * 返回cookie有效期时间戳
     * @param int $expire 有效秒数
     * @return int
     */
    protected function getExpire($expire = null)
    {
        return !empty($expire) ? time() + intval($expire) : 0;
    }

    /**
     * 生成cookie有效时间戳
     * @param int $val 时间
     * @param string $type 类型, `i`分钟(默认), `h`小时, `d`天,  `m/n`月(固定30天),
     */
    public function buildExpire($int, $type = 'i')
    {
        $expire = 0;
        $int = max([1, intval($int)]);
        switch ($type) {
            case 'i':
                $expire = $int * 60;
                break;
            case 'h':
                $expire = $int * 3600;
                break;
            case 'd':
                $expire = $int * 86400;
                break;
            case 'm':
            case 'n':
                $expire = $int * 2592000;
                break;
        }
        return $expire === 0 ? $expire : time() + $expire;
    }

    /**
     * 编码数组为字符串
     * @param array $array 数组
     * @param string $mark 标记
     * @return string 例`qpf:{"a":"1","b":"2"}`
     */
    private function encodeArray(array $array, $mark = 'qpf:')
    {
        array_walk_recursive($value, [$this, 'codingValue'], 'encode');
        return $mark . json_encode($value);
    }
    
    /**
     * 解码标记字符串为数组
     * @param strin $value 数组
     * @param string $mark 标记
     * @return array
     */
    private function decodeArray($value, $mark = 'qpf:')
    {
        if (strpos($value, $mark) === 0) {
            $value = substr($value, strlen($mark));
            $value = json_decode($value, true);
            array_walk_recursive($value, [$this, 'codingValue'], 'decode');
        }
        
        return $value;
    }
    
    /**
     * 将数组类型的cookie值进行编码
     * @param mixed $val 键值
     * @param string $key 键名
     * @param string $type 编码还是解码
     */
    private function codingValue(&$val, $key, $type = 'encode')
    {
        if (!empty($val) && $val !== true) {
            $val = 'decode' == $type ? urldecode($val) : urlencode($val);
        }
    }
    
    /**
     * 返回默认Cookie选项设置
     * @return array
     */
    private function getDefaultOptions()
    {
        return [
            'prefix'    => '', // 前缀名
            'expire'    => 0, // 有效期, 秒, `0` 关闭游览器失效.
            'path'      => '/', // 保存路径
            'domain'    => '', // 有效域名
            'secure'    => false, // 启用安全传输
            'httponly'  => false, // httponly设置
        ];
    }
}
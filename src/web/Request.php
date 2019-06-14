<?php
namespace qpf\web;

use qpf;
use qpf\exceptions\UploadException;
use qpf\file\UploadFiles;
use qpf\base\Injection;
use qpf\base\Core;
use qpf\base\Application;
use qpf\exceptions\ConfigException;
use qpf\exceptions\ParameterException;

/**
 * 处理当前HTTP请求的操作类
 */
class Request extends Injection
{
    /**
     * 应用程序
     * @var Application
     */
    protected $application;
    
    /**
     * 选项配置
     * @var array
     */
    protected $config = [
        // HTTPS代理标识
        'https_agent_name'  => '',
        // IP代理获取标识
        'http_agent_ip'     => 'HTTP_X_REAL_IP',
        // 全局默认过滤方法, 逗号分隔
        'default_filter'    => '',
        // URL路径信息查询参数名
        'var_pathinfo'      => 'r',
        // 兼容PATH_INFO获取
        'pathinfo_fetch'    => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
        // 表单请求类型伪装参数名
        'var_method'        => '_method',
        // 表单ajax伪装参数名
        'var_ajax'          => '_ajax',
        // 表单pjax伪装参数名
        'var_pjax'          => '_pjax',
        // 根域名, 例`quiun.com.cn`|`zone.quiun.com` , 当使用二级域名作为根域名时需配置
        'url_domain_root'   => '',
        // URL伪静态后缀, {false:'禁止伪静态访问'}
        'url_html_suffix'   => 'html',
        // 开启请求缓存
        'request_cache'     => false,
        // 请求缓存的有效期
        'request_cache_expire'  => null,
        // 请求缓存的排除规则
        'request_cache_except'  => [],
    ];
    

    /**
     * 构造函数
     * @param Application $app
     * @param array $config
     */
    public function __construct(Application $app, $config = [])
    {
        $this->application = $app;
        
        parent::__construct($config);
    }
    
    /**
     * 初始化
     */
    protected function boot()
    {
        $this->server = $_SERVER;
        $this->config = array_merge($this->config, $this->application->config->group('request'));
    }
    
    /**
     * 是否控制台请求
     * @return bool
     */
    public function isConsoleRequest()
    {
        return PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg';
    }
    
    /**
     * 是否控制台请求
     * @var bool
     */
    private $_isConsole;
    
    /**
     * 是否控制台请求
     * @return bool
     */
    public function isConsole()
    {
        return $this->_isConsole !== null ? $this->_isConsole : $this->isConsoleRequest();
    }
    
    /**
     * 设置当前是否为控制台请求
     * @param bool $value
     * @return void
     */
    public function setIsConsole($value)
    {
        $this->_isConsole = $value;
    }
    
    /**
     * 入口脚本文件路径
     * @var string
     */
    private $scriptFile;
    
    /**
     * 设置入口脚本文件路径
     * @param string $file
     */
    public function setScriptFile($file)
    {
        $file = realpath($file);
        if (is_file($file)) {
            $this->scriptFile = $file;
        }
    }
    
    /**
     * 获取入口脚本文件路径
     * @return string 返回本地绝对路径, 格式`c:\wwwroot\web\index.php`
     */
    public function getScriptFile()
    {
        if ($this->scriptFile === null) {
            if (isset($_SERVER['SCRIPT_FILENAME'])) {
                $this->setScriptFile($_SERVER['SCRIPT_FILENAME']);
            } elseif (isset($_SERVER['argv'][0])) {
                $this->setScriptFile($_SERVER['argv'][0]);
            }
        }
        
        return $this->scriptFile;
    }
    
    /**
     * 入口脚本文件名称
     * @var string
     */
    private $scriptName;
    
    /**
     * 获取入口脚本文件名
     * @return string 例如`c:\wwwroot\web\index.php`, 返回`index` 
     */
    public function getScriptName()
    {
        if ($this->scriptName === null) {
            $this->setScriptName(pathinfo($this->getScriptFile(), PATHINFO_FILENAME));
        }
        return $this->scriptName;
    }
    
    /**
     * 设置入口脚本文件名
     * @param string $name
     */
    public function setScriptName($name)
    {
        $this->scriptName = $name;
    }

    /**
     * 服务器参数
     * @var array
     */
    protected $server;
    
    /**
     * 返回服务器参数 - 大小写不敏感
     * @param string $name 参数名
     * @param string $default 默认值
     * @return mixed
     */
    public function server($name = null, $default = null)
    {
        if ($name === null) {
            return $this->server;
        }
        
        $name = strtoupper($name);
        
        return isset($this->server[$name]) ? $this->server[$name] : $default;
    }
    
    /**
     * 环境参数
     * @var array
     */
    protected $env;
    
    /**
     * 返回环境参数 - 大小写不敏感
     * @param string $name 参数名
     * @param string $default 默认值
     * @return mixed
     */
    public function env($name = null, $default = null)
    {
        if($name === null) {
            return $this->env;
        }
        
        $name = strtoupper($name);
        
        return isset($this->env[$name]) ? $this->env[$name] : $default;
    }
    
    /**
     * 域名 - 含协议与端口号
     * @var string
     */
    protected $domain;
    
    /**
     * 设置当前域名 - 含协议与端口号
     * @param string $domain
     * @return $this
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }
    
    /**
     * 返回当前请求的完整域名 - `http://w.a.com:80`
     * @param bool $port 是否去除端口号
     * @return string
     */
    public function domain($port = false)
    {
        return $this->scheme() . '://' . $this->host($port);
    }
    
    /**
     * 返回当前根域名
     * @return string
     */
    public function rootDomain()
    {
        // 二级域名作为根域名时需要单独设置, 例如 `com.cn`, `net.cn`
        $root = $this->config['url_domain_root'];
        
        if(!$root) {
            $item  = explode('.', $this->host());
            $count = count($item);
            $root  = $count > 1 ? $item[$count - 2] . '.' . $item[$count - 1] : $item[0];
        }
        
        return $root;
    }
    
    /**
     * 子域名
     * @var string
     */
    protected $subDomain;
    
    /**
     * 返回当前子域名
     * @return string
     */
    public function subDomain()
    {
        if($this->subDomain === null) {
            $root = $this->config['url_domain_root'];
            
            if($root) {
                $domain = explode('.', rtrim(stristr($this->host(), $root, true), '.'));
            } else {
                $domain = explode('.', $this->host(), -2);
            }
            
            $this->subDomain = implode('.', $domain);
        }
        
        return $this->subDomain;
    }
    
    /**
     * 泛域名
     * - 表示所有子域名都将解析到当前, 实现无限二级域名功能
     * @var string
     */
    protected $panDomain;
    
    /**
     * 设置当前泛域名
     * @param string $domain 域名
     * @return $this
     */
    public function setPanDomain($domain)
    {
        $this->panDomain = $domain;
        
        return $this;
    }
    
    /**
     * 返回当前泛域名
     * @return string
     */
    public function panDomain()
    {
        return $this->panDomain === null ? '' : $this->panDomain;
    }
    
    /**
     * 通讯协议类型
     * @return string
     */
    public function scheme()
    {
        return $this->isSSL() ? 'https' : 'http';
    }
    
    /**
     * 安全套接层
     */
    public function isSSL()
    {
        if ($this->server('HTTPS') && (strcasecmp($this->server('HTTPS'), 'on') === 0 || $this->server('HTTPS') == 1)) {
            return true;
        } elseif ($this->server('REQUEST_SCHEME') == 'https') {
            return true;
        } elseif ($this->server('SERVER_PORT' == '443')) {
            return true;
        } elseif ($this->server('HTTP_X_FORWARDED_PROTO') == 'https') {
            return true;
        }
        
        return false;
    }
    
    /**
     * 传输层安全协议
     */
    public function isTLS()
    {
        return $this->isSSL();
    }
    
    /**
     * 是否安全链接
     */
    public function isHttps()
    {
        return $this->isSSL();
    }
    
    /**
     * 当前请求的主机地址
     * @var string
     */
    protected $host;
    
    /**
     * 设置当前请求的主机地址 - 含端口
     * @param string $host 主机名
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;
        
        return $this;
    }
    
    /**
     * 返回当前请求的主机地址
     * @param bool $port 是否带端口号, 默认`false`不带
     * @return string
     */
    public function host($port = false)
    {
        if($this->host === null) {
            $host = $this->server('HTTP_X_REAL_HOST') ?: $this->server('HTTP_HOST');
            if($host === null) {
                $host = $this->server('SERVER_NAME');
                $isSSL = $this->isSSL();
                $server_port = $isSSL ? $this->httpsPort() : $this->httpPort();
                if (($server_port !== 80 && ! $isSSL) || ($server_port !== 443 && $isSSL)) {
                    $host .= ':' . $port;
                }
            }
            $this->host = $host;
        }
        
        return $this->host;
    }
    
    /**
     * 返回当前服务器端口号
     * @return int
     */
    public function port()
    {
        return $this->server('SERVER_PORT');
    }
    
    /**
     * 返回http请求端口号
     * @return int
     */
    public function httpPort()
    {
        if(!$this->isHttps()) {
            return $this->server('SERVER_PORT');
        } else {
            return 80;
        }
    }
    
    /**
     * 返回https请求的端口号
     * @return int
     */
    public function httpsPort()
    {
        if($this->isHttps()) {
            return $this->server('SERVER_PORT');
        } else {
            return 443;
        }
    }
    
    /**
     * 返回请求客户端所用的端口号
     * @return int
     */
    public function remotePort()
    {
        return $this->server('REMOTE_PORT');
    }
    
    /**
     * 当前完整url地址
     * @var string
     */
    protected $url;
    
    /**
     * 设置当前完整URL地址 - 包含域名和查询参数
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        
        return $this;
    }
    
    /**
     * 获取完整URL地址 - 含查询参数
     * @param bool $domain 是否带域名
     * @return string
     */
    public function url($domain = false)
    {
        if($this->url === null) {
            if($this->isCLI()) {
                $this->url = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '';
            } elseif ($this->server('HTTP_X_REWRITE_URL')) {
                $this->url = $this->server('HTTP_X_REWRITE_URL');
            } elseif ($this->server('REQUEST_URI')) {
                $this->url = $this->server('REQUEST_URI');
            } elseif ($this->server('ORIG_PATH_INFO')) {
                $this->url = $this->server('ORIG_PATH_INFO') . (!empty($this->server('QUERY_STRING')) ? '?' . $this->server('QUERY_STRING') : '');
            } else {
                $this->url = '';
            }
        }
        
        return $domain ? $this->domain() . $this->url : $this->url;
    }
    
    /**
     * 基础url地址
     * @var string
     */
    protected $baseUrl;
    
    /**
     * 设置当前基础URL - 不含查询参数
     * @param string $url
     * @return $this
     */
    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
        
        return $this;
    }
    
    /**
     * 返回当前基础域名 - 不含查询参数
     * @param bool $domain 是否带域名
     * @return string 返回域名与问号之间的部分, 即是标准的PathInfo值
     */
    public function baseUrl($domain = false)
    {
        if ($this->baseUrl === null) {
            $url = $this->url();
            $this->baseUrl = strpos($url, '?') ? strstr($url, '?', true) : $url;
        }
        
        return $domain ? $this->domain() . $this->baseUrl : $this->baseUrl;
    }
    
    /**
     * 当前执行的文件
     * @var string
     */
    protected $baseFile;
    
    /**
     * 当前请求执行的文件 - 入口文件url地址
     * @param bool $domain 是否带域名
     * @return string `/index.php`|`http://xxx.com/index.php`
     */
    public function baseFile($domain = false)
    {
        if ($this->baseFile === null) {
            $file = '';
            if ($this->isCGI()) {
                $script_name = basename($this->server('SCRIPT_FILENAME'));
                if (basename($this->server('SCRIPT_NAME')) === $script_name) {
                    $file = $this->server('SCRIPT_NAME');
                } elseif (basename($this->server('PHP_SELF')) === $script_name) {
                    $file = $this->server('PHP_SELF');
                } elseif (basename($this->server('ORIG_SCRIPT_NAME')) === $script_name) {
                    $file = $this->server('ORIG_SCRIPT_NAME');
                } elseif (($pos = strpos($this->server('PHP_SELF'), '/' . $script_name)) !== false) {
                    $file = substr($this->server('SCRIPT_NAME'), 0, $pos) . '/' . $script_name;
                } elseif ($this->server('DOCUMENT_ROOT') && strpos($this->server('SCRIPT_FILENAME'), $this->server('DOCUMENT_ROOT')) === 0) {
                    $file = str_replace('\\', '/', str_replace($this->server('DOCUMENT_ROOT'), '', $this->server('SCRIPT_FILENAME')));
                }
            }
            $this->baseFile = $file;
        }
        
        return $domain ? $this->domain() . $this->baseFile : $this->baseFile;
    }
    
    /**
     * 根URL地址
     * @var string
     */
    protected $root;
    
    /**
     * 设置根URL地址
     * @param string $url
     * @return $this
     */
    public function setRoot($url)
    {
        $this->root = $url;
        
        return $this;
    }
    
    /**
     * 返回根URL地址
     * @param bool $domain 是否带域名
     */
    public function root($domain = false)
    {
        if($this->root === null) {
            $file = $this->baseFile();
            if ($file && 0 !== strpos($this->url(), $file)) {
                $file = str_replace('\\', '/', dirname($file));
            }
            $this->root = rtrim($file, '/');
        }
        
        return $domain ? $this->domain() . $this->root : $this->root;
    }
    
    /**
     * 返回根URL根目录
     * @return string
     */
    public function rootUrl()
    {
        $base = $this->root();
        $root = strpos($base, '.') ? ltrim(dirname($base), DIRECTORY_SEPARATOR) : $base;
        
        if($root != '') {
            $root = '/' . ltrim($root, '/');
        }
        
        return $root;
    }
    
    /**
     * URL路径信息
     * @var string
     */
    protected $pathinfo;
    
    /**
     * 设置URL路径信息
     * @param string $path
     * @return $this
     */
    public function setPathinfo($path)
    {
        $this->pathinfo = $path;
        // 同步重置
        $this->path = null;
        return $this;
    }
    
    /**
     * 多语言表单的编码
     * @param string $url
     * @return string
     */
    public function filterUrl($url)
    {
        // %## 转成对应的字符， + 转成空格
        $url = urldecode($url);
        
        // 正则检查是否是UTF-8编码
        if (!preg_match('%^(?:
        [\x09\x0A\x0D\x20-\x7E]              # ASCII
        | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
        | \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
        | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
        | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
        | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
        | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
        | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
        )*$%xs', $url)) {
            $url = utf8_encode($url);
        }
        
        return $url;
    }
    
    /**
     * 返回当前URL的路径信息 - 包含后缀名
     * @return string
     */
    public function pathinfo()
    {
        if($this->pathinfo === null) {
            if(isset($_GET[$this->config['var_pathinfo']])) {
                // 查询参数获取
                $path = $_GET[$this->config['var_pathinfo']];
                unset($_GET[$this->config['var_pathinfo']]);
            } elseif ($this->isCLI()) {
                // CLI >  index.php module/controller/action/params/...
                $path = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '';
            } elseif (PHP_SAPI == 'cli-server') {
                $path = strpos($this->server('REQUEST_URI'), '?') ? strstr($this->server('REQUEST_URI'), '?', true) : $this->server('REQUEST_URI');
            } elseif ($this->server('PATH_INFO')) {
                $path = $this->server('PATH_INFO');
            } else {
                foreach ($this->config['pathinfo_fetch'] as $name) {
                    if($this->server($name)) {
                        $path = (0 === strpos($this->server($name), $this->server('SCRIPT_NAME'))) ? substr($this->server($name), strlen($this->server('SCRIPT_NAME'))) : $this->server($name);
                        break;
                    }
                }
            }
            
            $this->pathinfo =  empty($path) || '/' == $path ? '' : ltrim($path, '/');
        }
        
        return $this->pathinfo;
    }
    
    /**
     * 当前URL路径信息 - 不含后缀名
     * @var string
     */
    protected $path;
    
    /**
     * 返回当前URL路径信息 - 不含后缀名
     * @return string
     */
    public function path()
    {
        if ($this->path === null) {
            $suffix = $this->config['url_html_suffix'];
            $path = $this->pathinfo();
            if($suffix === false) {
                // 禁用伪静态
                $this->path = $path;
            } elseif ($suffix) {
                // 设置了指定的后缀名, 去除URL后缀
                $this->path = preg_replace('/\.(' . ltrim($suffix, '.') . ')$/i', '', $path);
            } else {
                // 允许任意后缀名, 去除后缀
                $this->path = preg_replace('/\.' . $this->ext() . '$/i', '', $path);
            }
        }
        
        return $this->path;
    }

    /**
     * URL后缀名
     * @var string
     */
    protected $ext;
    
    /**
     * 返回当前URL后缀名
     * @return string
     */
    public function ext()
    {
        return pathinfo($this->pathinfo(), PATHINFO_EXTENSION);
    }
    
    /**
     * 返回当前请求时的时间
     * @param bool $float 是否采用浮点类型
     */
    public function tiem($float = false)
    {
        return $float ? $this->server('REQUEST_TIME_FLOAT') : $this->server('REQUEST_TIME');
    }
    
    /**
     * 资源类型映射
     * @var array
     */
    protected $mimeType = [
        'xml' => 'application/xml,text/xml,application/x-xml',
        'json' => 'application/json,text/x-json,application/jsonrequest,text/json',
        'js' => 'text/javascript,application/javascript,application/x-javascript',
        'css' => 'text/css',
        'rss' => 'application/rss+xml',
        'yaml' => 'application/x-yaml,text/yaml',
        'atom' => 'application/atom+xml',
        'pdf' => 'application/pdf',
        'text' => 'text/plain',
        'image' => 'image/png,image/jpg,image/jpeg,image/pjpeg,image/gif,image/webp,image/*',
        'csv' => 'text/csv',
        'html' => 'text/html,application/xhtml+xml,*/*'
    ];
    
    /**
     * 返回当前请求的资源类型
     * @return string|false
     */
    public function type()
    {
        $accept = $this->server('HTTP_ACCEPT');
        if (empty($accept)) {
            return false;
        }
        
        foreach ($this->mimeType as $type => $text) {
            $item = explode(',', $text);
            foreach ($item as $i => $mime) {
                if (stristr($accept, $mime)) {
                    return $type;
                }
            }
        }
        
        return false;
    }
    
    /**
     * 设置资源类型映射
     * @param string|array $type 类型名称
     * @param string $text 类型描述
     */
    public function mimeType($type, $text = '')
    {
        if (is_array($type)) {
            $this->mimeType = array_merge($this->mimeType, $type);
        } else {
            $this->mimeType[$type] = $text;
        }
    }
    
    /**
     * 当前请求类型
     * @var string
     */
    protected $method;
    
    /**
     * 返回当前请求类型
     * @param bool $real 是否返回真实的请求类型
     * @return string
     */
    public function method($real = false)
    {
        if($real) {
            return $this->server('REQUEST_METHOD') ?: 'GET';
        }
        
        if ($this->method === null) {
            if (isset($_POST[$this->config['var_method']])) {
                $method = strtolower($_POST[$this->config['var_method']]);
                unset($_POST[$this->config['var_method']]);
                if (in_array($method, ['get', 'post', 'put', 'patch', 'delete'])) {
                    $this->method = strtoupper($method);
                    $this->{$method} = $_POST;
                } else {
                    $this->method = 'POST';
                }
            } elseif ($this->server('HTTP_X_HTTP_METHOD_OVERRIDE')) {
                $this->method = strtoupper($this->server('HTTP_X_HTTP_METHOD_OVERRIDE'));
            } else {
                $this->method = $this->server('REQUEST_METHOD') ?: 'GET';
            }
        }
        
        return $this->method;
    }
    
    /**
     * 是否为指定请求类型
     * @param string $type 请求类型
     * @return bool
     */
    public function isMethod($type)
    {
        return $this->method() === strtoupper($type);
    }
    
    /**
     * 是否GET请求
     * @return bool
     */
    public function isGet()
    {
        return $this->method() == 'GET';
    }
    
    /**
     * 是否POST请求
     * @return bool
     */
    public function isPost()
    {
        return $this->method() === 'POST';
    }
    
    /**
     * 是否OPTIONS请求
     * @return bool
     */
    public function isOptions()
    {
        return $this->method() === 'OPTIONS';
    }
    
    /**
     * 是否是HEAD请求
     * @return bool
     */
    public function isHead()
    {
        return $this->method() === 'HEAD';
    }
    
    /**
     * 是否是DELETE请求
     * @return bool
     */
    public function isDelete()
    {
        return $this->method() === 'DELETE';
    }
    
    /**
     * 是否是PUT请求
     * @return bool
     */
    public function isPut()
    {
        return $this->method() === 'PUT';
    }
    
    /**
     * 是否是PATCH请求
     * @return bool
     */
    public function isPatch()
    {
        return $this->method() === 'PATCH';
    }
    
    /**
     * 返回数据源中的参数, 并支持默认值和过滤
     * @param array $date 数据源
     * @param string $name 参数名, 值为`null`时返回数据源
     * @param mixed $default 默认值
     * @param string|array $filter 过滤函数, 未指定时采用全局过滤规则
     * @return mixed
     */
    public function input(array $data = null, $name = null, $default = null, $filter = '')
    {
        // 返回数据源
        if ($name === null) {
            return $data;
        }
        
        $name = (string) $name;
        if (!empty($name)) {
            // 类型转换
            if (strpos($name, '/')) {
                list($name, $type) = explode('/', $name);
            }
            // 获取指定参数的数据
            $data = $this->getData($data, $name);
            
            if($data === null) {
                return $default;
            } elseif (is_object($data)) {
                return $data;
            }
        }
        
        return $this->filterData($data, $filter, $name, $default);
    }
    
    /**
     * 获取指定参数的值
     * @param array $data 数据源
     * @param string $name 带点的多级参数名
     * @return mixed
     */
    protected function getData($data, $name)
    {
        foreach (explode('.', $name) as $val) {
            if (isset($data[$val])) {
                $data = $data[$val];
            } else {
                return null;
            }
        }
        
        return $data;
    }
    
    /**
     * 使用规则过滤数据
     * @param mixed $data 数据
     * @param string|array $filter 过滤函数, 未指定时采用全局过滤规则
     * @param string $name 参数名, 值为`null`时返回数据源
     * @param mixed $default 默认值
     * @return mixed
     */
    protected function filterData($data, $filter, $name, $default)
    {
        // 准备过滤规则
        $filters = $this->readyFilter($filter, $default);
        
        if(is_array($data)) {
            array_walk_recursive($data, [$this, 'filterValue'], $filters);
            reset($data);
        } else {
            $this->filterValue($data, $name, $filters);
        }
        
        return $data;
    }
    
    
    /**
     * 全局过滤规则
     * @var array
     */
    protected $filter;
    
    /**
     * 设置全局过滤规则
     * @param array $filter
     * @return $this
     */
    public function setFilter(array $filter)
    {
        $this->filter = $filter;
        
        return $this;
    }
    
    /**
     * 返回全局过滤规则
     * @return array
     */
    public function filter()
    {
        if ($this->filter === null) {
            $this->filter = [];
        }
        
        return $this->filter;
    }
    
    /**
     * 准备过滤规则
     * @param mixed $filter 可能的值:
     * - `null` : 不使用任何规则
     * - "" : 空字符串, 仅使用全局过滤规则
     * - `string` : 字符串类型, 使用逗号分隔过滤规则.
     * - `array` : 数组类型, 多个过滤规则.
     * @param mixed $default 默认值
     * @return array 返回的数组包含过滤规则, 数组最后一个元素为默认值.
     */
    protected function readyFilter($filter, $default)
    {
        if ($filter === null) {
            $filter = [];
        } else {
            $filter = $filter ?: $this->filter;
            if (is_string($filter) && strpos($filter, '/') === false) {
                $filter = explode(',', $filter);
            } else {
                $filter = (array) $filter;
            }
        }
        
        $filter[] = $default;
        
        return $filter;
    }
    
    /**
     * 过滤参数的值 - 支持递归
     * @param mixed $val 参数值, 键值
     * @param string $key 参数名, 键名
     * @param array $filters 过滤规则, 由[[readyFilter()]]]方法生成的过滤规则
     * @return mixed 返回过滤后的参数值
     */
    private function filterValue(&$val, $key, $filters)
    {
        $default = array_pop($filters);
        
        foreach ($filters as $filter) {
            if (is_callable($filter)) {
                $val = call_user_func($filter, $val);
            } elseif (is_scalar($val)) {
                // 正则过滤
                if (strpos($filter, '/') !== false) {
                    if (!preg_match($filter, $val)) {
                        $val = $default;
                        break;
                    }
                } elseif (!empty($filter)) {
                    $val = filter_var($val, is_int($filter) ? $filter : filter_id($filter));
                    if (false === $val) {
                        $val = $default;
                        break;
                    }
                }
            }
        }
        
        return $val;
    }
    
    /**
     * 是否联合查询参数
     * @var bool
     */
    protected $jointParam = true;
    /**
     * 缓存的联合查询参数
     * @var array
     */
    protected $param = [];
    
    /**
     * 返回当前请求的参数
     * @param string $name 参数名
     * @param mixed $default 默认值
     * @param string|array $filter 过滤方法
     * @return mixed
     */
    public function param($name = null, $default = null, $filter = '')
    {
        // 启用联合查询请求参数
        if (empty($this->param) && $this->jointParam) {
            $method = $this->method(true);
            switch ($method) {
                case 'POST':
                    $joint = $this->post();
                    break;
                case 'PUT':
                case 'DELETE':
                case 'PATCH':
                    $joint = $this->put();
                    break;
                default:
                    $joint = [];
            }

            // 缓存所有请求参数
            $this->param = array_merge($this->param, $this->get(), $joint, $this->route(), $this->file() ?: []);
        }
        
        if (is_array($name)) {
            return $this->only($name, $this->param, $filter);
        }
        
        return $this->input($this->param, $name, $default, $filter);
    }
    
    /**
     * 从数据源获取多个指定参数
     * @param array $name 参数获取集合 [参数名'=>'默认值', ...]
     * @param array|string $data 数据数组或数据源名称
     * @param array|string $filter 过滤规则
     */
    public function only(array $name, $data = 'param', $filter = '')
    {
        $data = is_array($data) ? $data : $this->$data();
        
        $result = [];
        foreach ($name as $key => $val) {
            if (is_int($key)) {
                $default = null;
                $key = $val;
                if (!isset($data[$key])) {
                    continue;
                }
            } else {
                $default = $val;
            }
            
            $result[$key] = $this->filterData(!empty($data[$key]) ? $data[$key] : $default, $filter, $name, $default);
        }
        
        return $result;
    }
    
    /**
     * 是否存在指定的请求参数
     * @param string $name 参数名
     * @param string $data 数据源
     * @param string $checkEmpty 是否检查空值
     */
    public function has($name, $data = 'param', $checkEmpty = false)
    {
        $data = empty($this->$data) ? $this->$data() : $this->$data;
        
        foreach (explode('.', $name) as $val) {
            if (isset($data[$val])) {
                $data = $data[$val];
            } else {
                return false;
            }
        }
        
        return ($checkEmpty && $data === '' ) ? false : true;
    }
    
    /**
     * 从数据源获取排除参数以外的所有参数
     * @param array $name 排除的参数列表
     * @param array|string $data 数据数组或数据源名称
     * @return mixed
     */
    public function except(array $name, $data = 'param')
    {
        $data = is_array($data) ? $data : $this->$data();
        
        foreach ($name as $var) {
            if (isset($data[$var])) {
                unset($data[$var]);
            }
        }
        
        return $data;
    }
    
    /**
     * 当前路由参数
     * @var array
     */
    protected $route = [];
    
    /**
     * 设置路由参数
     * @param array $routes 路由参数
     * @return $this
     */
    public function setRoute(array $routes)
    {
        
        $this->route = array_merge($this->route, $routes);
        
        return $this;
    }
    
    /**
     * 返回路由参数
     * @param string|array $name 参数名
     * @param mixed $default 默认值
     * @param string|array $filter 过滤规则
     */
    public function route($name = null, $default = null, $filter = '')
    {
        if(is_array($name)) {
            return $this->only($name, $this->route, $filter);
        }
        return $this->input($this->route, $name, $default, $filter);
    }
    
    /**
     * 当前请求的查询参数
     * @var array
     */
    protected $get;
    
    /**
     * 返回GET参数
     * @param string|array $name 参数名
     * @param mixed $default  默认值
     * @param string|array $filter 过滤规则
     */
    public function get($name = null, $default = null, $filter = '')
    {
        if ($this->get === null) {
            if($this->pathinfo === null) {
                $this->pathinfo();
            }
            $this->get = $_GET;
        }
        
        if(is_array($name)) {
            return $this->only($name, $this->get, $filter);
        }
        
        return $this->input($this->get, $name, $default, $filter);
    }
    
    /**
     * 中间件传递的参数
     * @var array
     */
    protected $middleware;
    
    /**
     * 返回中间件传递的参数
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function middleware($name, $default = null)
    {
        return isset($this->middleware[$name]) ? $this->middleware[$name] : $default;
    }
    
    /**
     * 当前请求提交的参数
     * @var array
     */
    protected $post;
    
    /**
     * 返回POST参数
     * @param string|array $name 参数名
     * @param mixed $default  默认值
     * @param string|array $filter 过滤规则
     */
    public function post($name = null, $default = null, $filter = '')
    {
        if ($this->post === null) {
            $this->post = !empty($_POST) ? $_POST : $this->getInputData($this->phpInput());
        }
        
        if(is_array($name)) {
            return $this->only($name, $this->post, $filter);
        }
        
        return $this->input($this->post,  $name, $default, $filter);
    }
    
    /**
     * PUT参数
     * @var array
     */
    protected $put;
    
    /**
     * 返回PUT参数
     * @param string|array $name 参数名
     * @param mixed $default  默认值
     * @param string|array $filter 过滤规则
     */
    public function put($name = null, $default = null, $filter = '')
    {
        if ($this->put === null) {
            $this->put = $this->getInputData($this->phpInput());
        }
        
        if(is_array($name)) {
            return $this->only($name, $this->put, $filter);
        }
        
        return $this->input($this->put,  $name, $default, $filter);
    }
    
    /**
     * 请求的原始数据流
     * @var mixed
     */
    protected $phpInput;
    
    /**
     * 返回请求的原始数据流
     * @return mixed
     */
    public function phpInput()
    {
        if ($this->phpInput === null) {
            $this->phpInput = file_get_contents('php://input');
        }
        
        return $this->phpInput;
    }
    
    /**
     * 获取当前请求的php://input
     * @return mixed|string
     */
    public function getInput()
    {
        return $this->phpInput();
    }
    
    /**
     * 返回解析后的原始数据流
     * @param mixed $content 数据内容
     * @return mixed
     */
    protected function getInputData($content)
    {
        if (strpos($this->contentType(), 'application/json') !== false || strpos($content, '{"') === 0) {
            return (array) json_decode($content, true);
        } elseif (strpos($content, '=')) {
            parse_str($content, $data);
            return $data;
        }
        
        return [];
    }
    
    /**
     * 返回DELETE参数
     * @param string|array $name 参数名
     * @param mixed $default  默认值
     * @param string|array $filter 过滤规则
     */
    public function delete($name = null, $default = null, $filter = '')
    {
        return $this->put($name, $default, $filter);
    }
    
    /**
     * 返回PATCH参数
     * @param string|array $name 参数名
     * @param mixed $default  默认值
     * @param string|array $filter 过滤规则
     */
    public function patch($name = null, $default = null, $filter = '')
    {
        return $this->put($name, $default, $filter);
    }
    
    /**
     * 当前请求的参数
     * @var array
     */
    protected $request;
    
    /**
     * 返回request参数
     * @param string|array $name 参数名
     * @param mixed $default  默认值
     * @param string|array $filter 过滤规则
     */
    public function request($name = null, $default = null, $filter = '')
    {
        if ($this->request === null) {
            $this->request = $_REQUEST;
        }
        
        if(is_array($name)) {
            return $this->only($name, $this->request, $filter);
        }
        
        return $this->input($this->request,  $name, $default, $filter);
    }
    
    /**
     * 会话参数
     * @var array
     */
    protected $session;
    
    /**
     * 返回会话参数
     * @param string|array $name 名称
     * @param string $default 默认值
     * @return mixed
     */
    public function session($name = null, $default = null)
    {
        if ($this->session === null) {
            $this->session = QPF::app()->session()->get();
        }
        
        if ($name === null) {
            return $this->session;
        }
        
        $data = $this->getData($this->session, $name);
        
        return $data === null ? $default : $data;
    }
    
    /**
     * 客户端参数
     * @var array
     */
    protected $cookie;
    
    /**
     * 返回客户端参数
     * @param string|array $name 参数名
     * @param mixed $default  默认值
     * @param string|array $filter 过滤规则
     */
    public function cookie($name = null, $default = null, $filter = '')
    {
        $cookie = QPF::app()->cookie();
        
        if ($this->cookie === null) {
            $this->cookie = $cookie->get();
        }
        
        if ($name === null) {
            $data = $this->cookie;
        } else {
            $data = $cookie->has($name) ? $cookie->get($name) : $default;
        }
        
        return $this->filterData($data, $filter, $name, $default);
    }
    
    /**
     * 当前上传文件信息
     * @var array
     */
    protected $file;
    
    /**
     * 返回当前上传文件的信息
     * @param string $name 上传input的name值
     * @return UploadFiles[]|null
     */
    public function file($name = null)
    {
        if ($this->file === null) {
            $this->file = $_FILES;
        }
        
        if (!empty($this->file)) {
            // 将上传文件转换为File对象
            $files = $this->parseFiles($this->file);
            
            if (strpos($name, '.')) {
                list($name, $param) = explode('.', $name);
            }
            
            if ($name === null) {
                return $files;
            } elseif (isset($param) && isset($files[$name][$param])) {
                return $files[$name][$param];
            } elseif (isset($files[$name])) {
                return $files[$name];
            }
        } else {
            return null;
        }
    }
    
    /**
     * 解析$_FILES信息
     * ```
     * [
     *    'input_name'  => Object("qpf\base\File"){}
     *    'input_name'  => [
     *          0 => Object("qpf\base\File"){}
     *          1 => Object("qpf\base\File"){}
     *          2 => Object("qpf\base\File"){}
     *    ],
     * ]
     * ```
     * @param array $files 传入$_FILES变量值
     * @return array
     */
    protected function parseFiles($files)
    {
        $result = [];
        
        foreach ($files as $key => $file) {
            // 批量上传
            if (is_array($file['name'])) {
                $list = [];
                // 参数列表
                $keys = array_keys($file);
                $count = count($file['name']);
                for ($i = 0; $i < $count; $i++) {
                    if ($file['error'][$i] > 0) {
                        throw new UploadException($file['error'][$i]);
                    }
                    
                    // 取出单个文件的信息
                    $info = ['key' => $key];
                    foreach ($keys as $_key) {
                        $info[$_key] = $file[$_key][$i];
                    }
                    
                    $list[] = new UploadFiles($info['tmp_name'], ['info'=> $info]);
                }
                
                $result[$key] = $list;
            } else {
                if ($file instanceof \qpf\file\UploadFiles) {
                    $result[$key] = $file;
                } else {
                    if ($file['error'] > 0) {
                        throw new UploadException($file['error']);
                    }
                    
                    $result[$key] = new UploadFiles($file['tmp_name'], ['info'=> $file]);
                }
            }
        }
        
        return $result;
    }
    
    /**
     * 当前请求的报头信息
     * @var array
     */
    protected $header;
    
    /**
     * 返回当前header报头信息
     * @param string|array $name 参数名
     * @param mixed $default  默认值
     * @param string|array $filter 过滤规则
     * @return string|array
     */
    public function header($name = null, $default = null, $filter = '')
    {
        if ($this->header === null) {
            $header = [];
            
            if (function_exists('apache_request_headers') && $array = apache_request_headers()) {
                $header = $array;
            } else {
                $server = $this->server;
                foreach ($server as $key => $val) {
                    if (strpos($key, 'HTTP_') === 0) {
                        $key = str_replace('_', '-', strtolower(substr($key, 5)));
                        $header[$key] = $val;
                    }
                }
                // 非HTTP开头的参数
                if(isset($server['CONTENT_TYPE'])) {
                    $header['content-type'] = $server['CONTENT_TYPE'];
                }
                if (isset($server['CONTENT_LENGTH'])) {
                    $header['content-length'] = $server['CONTENT_LENGTH'];
                }
                
                $this->header = array_change_key_case($header);
            }
        }
        
        if ($name === null) {
            return $this->header;
        } else {
            $name = str_replace('_', '-', strtolower($name));
            
            return isset($this->header[$name]) ? $this->header[$name] : $default;
        }
    }
    
    /**
     * 是否为命令行模式运行
     * @return bool
     */
    public function isCLI()
    {
        return PHP_SAPI == 'cli';
    }
    
    /**
     * 是否为Web服务器模式运行
     * @return bool
     */
    public function isCGI()
    {
        return strpos(PHP_SAPI, 'cgi') !== false;
    }
    
    
    
    /**
     * 是否为Ajax请求
     * @param bool $ajax 是否仅检查原始请求
     * @return bool
     */
    public function isAjax($ajax = false)
    {
        $value = $this->server('HTTP_X_REQUESTED_WITH');
        $result = $value && 'xmlhttprequest' == strtolower($value) ? true : false;
        
        if($ajax === true) {
            return $result;
        }
        
        // 表单ajax伪装参数
        return $this->param($this->config['var_ajax']) ? true : $result;
    }
    
    /**
     * 是否为Pjax请求
     * @param bool $ajax 是否仅检查原始请求
     * @return bool
     */
    public function isPjax($pjax = false)
    {
        $value = $this->server('HTTP_X_PJAX', false);
        $result = $value ? true : false;
        
        if ($pjax === true) {
            return $result;
        }
        
        // 表单pjax伪装参数
        return $this->param($this->config['var_pjax']) ? true : $result;
    }
    
    /**
     * 是否是Flash请求
     * @return bool
     */
    public function isFlash()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) && (stripos($_SERVER['HTTP_USER_AGENT'], 'Shockwave') !== false || stripos($_SERVER['HTTP_USER_AGENT'], 'Flash') !== false);
    }
    
    /**
     * 是否是移动端访问
     * @return bool
     */
    public function isMobile()
    {
        if ($this->server('HTTP_VIA') && stristr($this->server('HTTP_VIA'), "wap")) {
            return true;
        } elseif ($this->server('HTTP_ACCEPT') && strpos(strtoupper($this->server('HTTP_ACCEPT')), "VND.WAP.WML")) {
            return true;
        } elseif ($this->server('HTTP_X_WAP_PROFILE') || $this->server('HTTP_PROFILE')) {
            return true;
        } elseif ($this->server('HTTP_USER_AGENT') && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $this->server('HTTP_USER_AGENT'))) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 返回客户端IP地址
     * @param int $type 获取类型, 默认`0`IP地址, `1`IPV4地址数字
     * @param bool $adv 是否检查代理
     * @return number|string|mixed|array
     */
    public function ip($type = 0, $adv = true)
    {
        $type = $type ? 1 : 0;
        
        // IP代理获取标识
        $httpAgentIp = $this->config['http_agent_ip'];
        
        if ($httpAgentIp && $this->server($httpAgentIp)) {
            $ip = $this->server($httpAgentIp);
        } elseif ($adv) {
            // 经过的代理服务, 可伪造 `X-Forwarded-For: client, proxy1, proxy2`
            if ($this->server('HTTP_X_FORWARDED_FOR')) {
                $arr = explode(',', $this->server('HTTP_X_FORWARDED_FOR'));
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }
                $ip = trim(current($arr));
            } elseif ($this->server('HTTP_CLIENT_IP')) {
                // 可在http请求头中伪造
                $ip = $this->server('HTTP_CLIENT_IP');
            } elseif ($this->server('REMOTE_ADDR')) {
                $ip = $this->server('REMOTE_ADDR');
            }
        } elseif ($this->server('REMOTE_ADDR')) {
            // 不可伪造, 直接连接服务器的IP, 但可能是代理服务器或局域网的外网地址
            $ip = $this->server('REMOTE_ADDR');
        }
        
        // IP地址类型
        $ip_mode = (strpos($ip, ':') === false) ? 'ipv4' : 'ipv6';
        
        // IP地址合法验证
        if (filter_var($ip, FILTER_VALIDATE_IP) !== $ip) {
            $ip = ('ipv4' === $ip_mode) ? '0.0.0.0' : '::';
        }
        
        // 如果是ipv4地址，则直接使用ip2long返回int类型ip；如果是ipv6地址，暂时不支持，直接返回0
        $long_ip = ('ipv4' === $ip_mode) ? sprintf("%u", ip2long($ip)) : 0;
        
        $ip = [$ip, $long_ip];
        
        return $ip[$type];
    }
    
    /**
     * 返回当前请求的查询参数 - 不带问号
     * @return string
     */
    public function query()
    {
        return $this->server('QUERY_STRING');
    }
    
    /**
     * 返回当前请求协议 - `HTTP/1.1`
     * @return string
     */
    public function protocol()
    {
        return $this->server('SERVER_PROTOCOL');
    }
    
    /**
     * 返回当前请求请求内容类型标识
     * @return string 返回空字符串代表未定义
     */
    public function contentType()
    {
        $value = $this->server('CONTENT_TYPE');
        
        if ($value !== null) {
            if (strpos($value, ';')) {
                // 取出第一个元素, `text/html; charset=UTF-8`
                list ($type) = explode(';', $value);
            } else {
                $type = $value;
            }
            
            return trim($type);
        }
        
        return '';
    }
    
    /**
     * 当前请求的路由信息
     * @var array
     */
    protected $routeInfo = [];
    
    /**
     * 设置或返回请求的路由信息 TODO
     * @param array $route 路由
     * @return array
     */
    public function routeInfo(array $route = [])
    {
        if(!empty($route)) {
            $this->routeInfo = $route;
        }
        
        return $this->routeInfo;
    }
    
    /**
     * 设置或返回当前请求的调度信息 TODO
     * @param Dispatch $dispatch 调度对象
     * @return Dispatch
     */
    public function dispatch(Dispatch $dispatch = null)
    {
        if (!is_null($dispatch)) {
            $this->dispatch = $dispatch;
        }
        
        return $this->dispatch;
    }
    
    /**
     * 请求安全钥
     * @var string
     */
    protected $secureKey;
    
    /**
     * 返回当前请求的安全钥
     * @return string
     */
    public function secureKey()
    {
        if ($this->secureKey === null) {
            $this->secureKey = uniqid('', true);
        }
    }
    
    /**
     * 当前请求的应用名
     * @var string
     */
    protected $app;
    
    /**
     * 设置当前请求的应用名
     * @param string $name
     * @return $this
     */
    public function setApp($name)
    {
        $this->app = $name;
        return $this;
    }
    
    /**
     * 获取当前请求的应用名
     * @return string
     */
    public function app()
    {
        return $this->app ?: '';
    }
    
    /**
     * 当前请求的控制器
     * @var string
     */
    protected $controller;
    
    /**
     * 设置当前请求的控制器
     * @param string $name
     * @return $this
     */
    public function setController($name)
    {
        $this->controller = $name;
        return $this;
    }
    
    /**
     * 获取当前请求的控制器
     * @param bool $lower 是否返回小写
     * @return string
     */
    public function controller($lower = false)
    {
        $name = $this->controller ?: '';
        return $lower ? strtolower($name) : $name;
    }
    
    /**
     * 当前请求的操作名
     * @var string
     */
    protected $action;
    
    /**
     * 设置当前操作名
     * @param string $name
     * @return $this
     */
    public function setAction($name)
    {
        $this->action = $name;
        return $this;
    }
    
    /**
     * 获取当前操作名
     * @param bool $lower 是否返回小写
     * @return string
     */
    public function action($lower = false)
    {
        $name = $this->action ?: '';
        return $lower ? strtolower($name) : $name;
    }
    
    /**
     * 请求的语言
     * @var string
     */
    protected $langset;
    
    /**
     * 设置当前的语言
     * @param string $lang
     * @return $this
     */
    public function setLangset($lang)
    {
        $this->langset = $lang;
        return $this;
    }
    
    /**
     * 获取当前的语言
     * @return string
     */
    public function langset()
    {
        return $this->langset ?: '';
    }
    
    /**
     * 当前请求的内容
     * @var string
     */
    protected $content;
    
    /**
     * 获取当前请求的内容
     * @return string
     */
    public function getContent()
    {
        if($this->content === null) {
            $this->content = $this->phpInput();
        }
    }
    
    /**
     * 生成请求令牌
     * @param string $name 令牌名称
     * @param string|callable $type 生成方式, 哈希类型或回调函数
     * @return string
     */
    public function token($name = '__token__', $type = 'md5')
    {
        $type = is_callable($type) ? $type : 'md5';
        $token = call_user_func($type, $this->server('REQUEST_TIME_FLOAT'));
        
        if ($this->isAjax()) {
            header($name . ':' . $token);
        }
        
        QPF::app()->session->set($name, $token);
        
        return $token;
    }
    
    
    
    /**
     * 模拟提交一个请求
     * @param string $url 请求地址
     * @param string $method 请求类型
     * @param array $params 请求参数
     * @param array $cookie
     * @param array $files 上传文件
     * @param array $server 服务器参数
     * @param string $content 请求内容
     * @return \qpf\core\Request
     */
    public function submit($url, $method = 'GET', array $params = [], array $cookie = [], array $files = [], array $server = [], $content = null)
    {
        $server['PATH_INFO']      = '';
        $server['REQUEST_METHOD'] = strtoupper($method);
        $info                     = parse_url($url);
        
        if (isset($info['host'])) {
            $server['SERVER_NAME'] = $info['host'];
            $server['HTTP_HOST']   = $info['host'];
        }
        
        if (isset($info['scheme'])) {
            if ('https' === $info['scheme']) {
                $server['HTTPS']       = 'on';
                $server['SERVER_PORT'] = 443;
            } else {
                unset($server['HTTPS']);
                $server['SERVER_PORT'] = 80;
            }
        }
        
        if (isset($info['port'])) {
            $server['SERVER_PORT'] = $info['port'];
            $server['HTTP_HOST']   = $server['HTTP_HOST'] . ':' . $info['port'];
        }
        
        if (isset($info['user'])) {
            $server['PHP_AUTH_USER'] = $info['user'];
        }
        
        if (isset($info['pass'])) {
            $server['PHP_AUTH_PW'] = $info['pass'];
        }
        
        if (!isset($info['path'])) {
            $info['path'] = '/';
        }
        
        $options     = [];
        $queryString = '';
        
        $options[strtolower($method)] = $params;
        
        if (isset($info['query'])) {
            parse_str(html_entity_decode($info['query']), $query);
            if (!empty($params)) {
                $params      = array_replace($query, $params);
                $queryString = http_build_query($params, '', '&');
            } else {
                $params      = $query;
                $queryString = $info['query'];
            }
        } elseif (!empty($params)) {
            $queryString = http_build_query($params, '', '&');
        }
        
        if ($queryString) {
            parse_str($queryString, $get);
            $options['get'] = isset($options['get']) ? array_merge($get, $options['get']) : $get;
        }
        
        $server['REQUEST_URI']  = $info['path'] . ('' !== $queryString ? '?' . $queryString : '');
        $server['QUERY_STRING'] = $queryString;
        $options['cookie']      = $cookie;
        $options['param']       = $params;
        $options['file']        = $files;
        $options['server']      = $server;
        $options['url']         = $server['REQUEST_URI'];
        $options['baseUrl']     = $info['path'];
        $options['pathinfo']    = '/' == $info['path'] ? '/' : ltrim($info['path'], '/');
        $options['method']      = $server['REQUEST_METHOD'];
        $options['domain']      = isset($info['scheme']) ? $info['scheme'] . '://' . $server['HTTP_HOST'] : '';
        $options['content']     = $content;

        $this->useConfig($options, true);
        
        return $this;
    }
}
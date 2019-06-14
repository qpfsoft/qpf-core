<?php
namespace qpf\response;

use qpf;
use qpf\exceptions\ConfigException;
use qpf\base\Core;
use qpf\exceptions\ParameterException;

/**
 * 客户端响应类
 * 
 * 该类将应用程序的运行结果格式化为客户端接受的内容类型
 */
class Response extends Core
{
    /**
     * 响应原始数据
     * @var string
     */
    const TYPE_RAW = 'raw';
    /**
     * 响应HTML数据
     * @var string
     */
    const TYPE_HTML = 'html';
    /**
     * 响应Json数据
     * @var string
     */
    const TYPE_JSON = 'json';
    /**
     * 响应Jsonp数据
     * @var string
     */
    const TYPE_JSONP = 'jsonp';
    /**
     * 响应Xml数据
     * @var string
     */
    const TYPE_XML = 'xml';
    
    /**
     * 原始响应数据
     * @var mixed
     */
    protected $data;
    /**
     * 响应类型
     * @var string
     */
    protected $type;
    /**
     * 格式化后的输出内容
     * @var string
     */
    protected $content = null;
    /**
     * 要发送的数据资源流
     * - 设置后响应内容会忽略[$data]和[$content]属性的值.
     * @var resource|array
     */
    protected $stream;
    /**
     * 状态码
     * @var int
     */
    protected $code = 200;
    /**
     * 当前内容类型
     * @var string
     */
    protected $contentType = 'text/html';
    /**
     * 字符集
     * @var string
     */
    protected $charset = 'utf-8';
    /**
     * 输出选项参数
     * @var array
     */
    protected $options = [];
    /**
     * 报头参数
     * @var array
     */
    protected $headers = [
        'X-Powered-By' => 'QPF'
    ];
    /**
     * 是否允许请求缓存
     * @var bool
     */
    protected $allowCache = true;
    /**
     * 响应类型处理程序
     * @var array
     */
    protected $formats = [];
    /**
     * 响应是否已发送
     * @var bool
     */
    protected $isSend = false;
    
    /**
     * HTTP状态码和状态文本
     * @var array
     */
    public static $httpStatus = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        118 => 'Connection timed out',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        210 => 'Content Different',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        310 => 'Too many Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested range unsatisfiable',
        417 => 'Expectation failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable entity',
        423 => 'Locked',
        424 => 'Method failure',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway or Proxy Error',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        507 => 'Insufficient storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];


    protected function boot()
    {
        $this->charset = $this->charset ?: QPF::$app->getCharset();
        // 默认响应类型
        $this->type = $this->type ?: self::TYPE_HTML;
        // 响应类型处理程序
        $this->formats = array_merge($this->getDefaultFormats(), $this->formats);
    }

    /**
     * 发送数据到客户端
     * @return void
     */
    public function send()
    {
        if ($this->isSend) {
            return;
        }
        
        // 格式化数据类型
        $content = $this->getContent();
        
        /*
         * if ($this->code == 200 && $this->allowCache) {
         * $cache = QPF::app()->request()->getCache();
         * if ($cache) {
         * $this->headers['Cache-Control'] = 'max-age=' . $cache[1] . ',must-revalidate';
         * $this->headers['Last-Modified'] = gmdate('D, d M Y H:i:s') . ' GMT';
         * $this->headers['Expires'] = gmdate('D, d M Y H:i:s', $_SERVER['REQUEST_TIME'] + $cache[1]) . ' GMT';
         * }
         * }
         */
        
        // 发送HTTP响应头
        $this->sendHeaders();
        // 保存Cookie到客户端
        $this->getCookie()->save();
        
        // 发送内容
        $this->sendData($content);
        
        // 提高页面响应速度, 响应完成, 关闭连接, 服务端脚本继续运行到结束
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        
        $this->isSend = true;
    }

    /**
     * 处理数据
     * @return void
     */
    protected function output()
    {
        if (isset($this->formats[$this->type])) {
            $format = $this->formats[$this->type];
            
            if (! is_object($format)) {
                $this->formats[$this->type] = $format = QPF::create($format);
            }
            
            if ($format instanceof ResponseInterface) {
                $format->output($this);
            } else {
                throw new ConfigException('Invalid formats Type ' . $this->type . ' no implements ResponseInterface ');
            }
        } elseif ($this->type == 'raw') {
            if ($this->data !== null) {
                $this->content = $this->data;
            }
        } else {
            throw new ConfigException('Invalid response Type ' . $this->type);
        }
        
        if (is_array($this->content)) {
            throw new ParameterException('response content can not is Array');
        } elseif (is_object($this->content)) {
            if (method_exists($this->content, '__toString')) {
                $this->content = $this->content->__toString();
            } else {
                throw new ParameterException('response content can not is Object');
            }
        }
    }

    /**
     * 发送数据
     * @param mixed $data
     * @return void
     */
    protected function sendData($data)
    {
        if ($this->stream === null) {
            echo $data;
            return;
        }
        
        set_time_limit(0); // 重置大文件的时间限制
        $chunkSize = 8 * 1024 * 1024; // 每块8MB
        
        if (is_array($this->stream)) {
            list ($handle, $begin, $end) = $this->stream;
            fseek($handle, $begin);
            while (! feof($handle) && ($pos = ftell($handle)) <= $end) {
                if ($pos + $chunkSize > $end) {
                    $chunkSize = $end - $pos + 1;
                }
                echo fread($handle, $chunkSize);
                flush(); // 释放内存。 否则，大文件会引发PHP的内存限制。
            }
            fclose($handle);
        } else {
            while (! feof($this->stream)) {
                echo fread($this->stream, $chunkSize);
                flush();
            }
            fclose($this->stream);
        }
    }

    /**
     * 发送HTTP响应头
     * @return void
     */
    protected function sendHeaders()
    {
        if (headers_sent()) {
            return;
        }
        
        if (!empty($this->headers)) {
            // 发送状态码
            http_response_code($this->code);
            // 发送头部信息
            foreach ($this->headers as $name => $value) {
                header($name . ($value === null ? '' : ': ' . $value));
            }
        }
    }
    
    /**
     * 返回Cookie管理器
     * @return \qpf\base\Cookie
     */
    protected function getCookie()
    {
        return QPF::$app->cookie;
    }
    
    /**
     * 返回Session管理器
     * @return \qpf\session\Session
     */
    protected function getSession()
    {
        return QPF::$app->session;
    }

    /**
     * 设置响应的原始数据
     * @param mixed $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        
        return $this;
    }

    /**
     * 返回响应的原始数据
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 设置准备输出的内容
     * @param mixed $content
     * @return $this
     */
    public function setContent($content)
    {
        if ($content !== null && !is_string($content) && !is_numeric($content) && !is_callable([$content, '__toString'])) {
            throw new ParameterException('content type error : ' . gettype($content));
        }
        
        $this->content = (string) $content;
        
        return $this;
    }

    /**
     * 返回准备输出的内容
     * @return string
     */
    public function getContent()
    {
        if ($this->content === null) {
            $this->output();
        }
        return $this->content;
    }

    /**
     * 设置HTTP状态码
     * @param int $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        
        return $this;
    }

    /**
     * 返回HTTP状态码
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * 设置输出的参数
     * @param array $options 参数集合
     * @return $this
     */
    public function setOptions(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
        
        return $this;
    }

    /**
     * 返回准备输出的参数
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * 设置响应报头
     * @param string $name 参数名
     * @param string $value 值
     * @return $this
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
        
        return $this;
    }
    
    /**
     * 返回指定报头的信息
     * @param string $name 报头名称
     * @return string|null
     */
    public function getHeader($name)
    {
        return isset($this->headers[$name]) ? $this->headers[$name] : null;
    }

    /**
     * 设置HTTP响应头
     * @param array $header 参数集合
     * @return $this
     */
    public function setHeaders(array $headers = [])
    {
        $this->headers = array_merge($this->headers, $headers);
        
        return $this;
    }

    /**
     * 返回全部HTTP响应头
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
 
    /**
     * 允许请求缓存
     * @return $this
     */
    public function onAllowCache()
    {
        $this->allowCache = true;
        
        return $this;
    }

    /**
     * 不允许请求缓存
     * @return $this
     */
    public function offAllowCache()
    {
        $this->allowCache = false;
        
        return $this;
    }

    /**
     * 发送最后修改时间
     * - 游览器在第二次请求时, 会发送`If-Modified-Since`标记,
     * 询问服务器该时间后文件是否被修改过.
     * - 若资源没有变化, 自动返回304状态, 使用游览器缓存.
     * - Last-Modified / If-Modified-Since
     * 
     * @param stirng $time
     */
    public function putLastModified($time)
    {
        $this->headers['Last-Modified'] = $time;
        
        return $this;
    }

    /**
     * Etag
     * - Etag / If-None-Match
     *
     * # Etag由服务器生成, 客户端通过两种用法:
     * - If-Match : 可以在Etag未改变时断线重传
     * - If-None-Match : 可以刷新对象(在有新的Etag时返回)
     *
     * ```请求流程
     * 第一次访问:
     * - 客户端发起http get 请求一个文件
     * - 服务器处理请求, 返回文件内容, 状态码200 和 Header[] 报头.
     * 包括Etag(例如"2e681a-6-5d044840").
     * 第二次访问:
     * - 客户端发起http get 请求一个文件, 同时发送一个If-None-Match头,
     * 这个头的内容就是第一次请求时服务器返回的Etag：2e681a-6-5d044840.
     * - 服务器判断发送来的Etag和计算出的Etag匹配, 因此If-None-Match为False,
     * 不返回200，返回304，客户端继续使用本地缓存;
     *
     * 如果服务器又设置了Cache-Control, 将同时使用.
     * 即检查完修改时间和Etag之后，服务器才能返回304.
     * ```
     *
     * 作用:
     * - 一些文件也许会周期性更改, 单时内容并没有改变(仅改变了修改时间), 不希望客户端重新GET.
     * - 某些文件修改非常频繁, 比如1秒内修改了N次, 但If-Modified-Since只能检查到s秒. 只能精确到秒.
     * - 服务器不能精确的获得文件的最后修改时间, 为此HTTP/1.1 引入 Etag(Entity Tags), 一个文件相关的标记.
     *
     * HTTP/1.1标准并没有规定内容时什么, 可自定义, 例如`v1.0.0 ro 2e681a-6-5d044840`
     * 
     * @param string $etag
     */
    public function putETag($etag)
    {
        $this->headers['ETag'] = $etag;
        
        return $this;
    }

    /**
     * 设置页面过期时间
     * 
     * @param string $time `-1`将永久过期不缓存
     * @return $this
     */
    public function putExpires($time)
    {
        $this->headers['Expires'] = $time;
        
        return $this;
    }

    /**
     * 设置页面缓存开启状态
     * 
     * @param string $cache 状态码,
     *        - HTTP/1.1 `no-cache`时，浏览器就不会缓存该网页
     * @return $this
     */
    public function putCacheControl($cache)
    {
        $this->headers['Cache-control'] = $cache;
        
        return $this;
    }

    /**
     * 设置输出类型的报头
     * 
     * @param string $type 内容类型
     * @param string $charset 编码类型
     * @return $this
     */
    public function putContentType($type, $charset = 'utf-8')
    {
        $this->headers['Content-Type'] = $type . '; charset=' . $charset;
        
        return $this;
    }

    /**
     * 设置字符集
     * @param string $charset
     * @return $this
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
        
        return $this;
    }
    
    /**
     * 返回字符集
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }
    
    /**
     * 设置响应类型
     * @param string $type 格式
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        
        return $this;
    }
    
    /**
     * 返回响应类型
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * 响应数据格式化类
     * 
     * @return array
     */
    protected function getDefaultFormats()
    {
        return [
            'json' => '\\qpf\\response\\JsonResponse',
            'jsonp' => '\\qpf\\response\\JsonpResponse',
            'jump' => '',
            'view' => '',
            'xml' => '',
            'redirect' => '',
            'html'  => '\\qpf\\response\\HtmlResponse',
        ];
    }
}
<?php
namespace qpf\browser\kernel;

/**
 * 代表Curl一次的请求的操作对象
 */
class Curl extends Base
{
    /**
     * 会话句柄
     * @var resource|false
     */
    private $ch;  
    private $url = "http://www.baidu.com";
    private $flag_if_have_run;   //标记exec是否已经运行
    private $set_time_out = 20;  //设置curl超时时间
    private $cookie_file = "";  //cookie_file路径
    private $cookie_mode = 0;    //cookie保存模式 0不使用 1客户端、2服务器文件
    private $show_header = 0;    //是否输出返回头信息
    private $set_useragent = ""; //模拟用户使用的浏览器，默认为模拟
    
    /**
     * 构造函数
     * @param string $url
     */
    public function __construct($url = '')
    {
        $this->ch = curl_init();
        $this->url = $url;
        // 随机用户代理
        $this->set_useragent = $this->getUserAgent()->getUA();
        // 未指定时的默认首选
        $this->cookie_file = __DIR__ . '/cookie_tmp.txt';
    }
    
    /**
     * 关闭curl
     * @return void
     */
    public function close()
    {
        curl_close($this->ch);
    }
    
    /**
     * 析构函数
     */
    public function __destruct()
    {
        $this->close();
    }
    
    /**
     * 设置超时时间
     * @param int $time 秒, 默认`20`
     * @return $this
     */
    public function setTimeOut($time = 20) 
    {
        $time = intval($time);
        $time = $time > 0 ? $time : 20;
        $this->set_time_out = $time;
        
        return $this;
    }
    
    /**
     * 设置来源页面 
     * @param string $value
     * @return $this
     */
    public function setReferer($value)
    {
        if (!empty($url)) {
            curl_setopt($this->ch, CURLOPT_REFERER, $value);
        }
        
        return $this;
    }
    
    /**
     * 设置cookie存放模式
     * @param string $mode  1客户端、2服务器文件
     * @return $this
     */
    public function setCookieMode($mode = '')
    {
        $this->cookie_mode = $mode;
        
        return $this;
    }
    
    /**
     * 载入cookie
     */
    public function loadCookie()
    {
        if($this->cookie_mode == 1) {
            if (isset($_COOKIE['curl'])) {
                curl_setopt($this->ch, CURLOPT_COOKIE, $_COOKIE['curl']);
            } else {
                $this->exec();
                curl_setopt($this->ch, CURLOPT_COOKIE, $this->cookie_file);
            }
        } elseif ($this->cookie_mode == 2) {
            curl_setopt($this->ch, CURLOPT_COOKIEFILE , $this->cookie_file);
        }
        
        return $this;
    }
    
    /**
     * 设置保存cookie方式
     * @param unknown $cookies 模式1为变量 模式2为文件路径
     */
    public function saveCookie($cookies)
    {
        //保存在客户端
        if($this->cookie_mode == 1 && $cookies) {
            setcookie('curl',$cookies);
        } elseif ($this->cookie_mode == 2) {
            if(!empty($cookie_val)) {
                $this->cookie_file =  $cookies;
                curl_setopt($this->ch, CURLOPT_COOKIEJAR , $this->cookie_file);  
            }
        }
        
        return $this;
    }
    
    /**
     * post参数
     * @param array $post
     * @return $this
     */
    public function post($post)
    {
        if(!empty($post) && is_array($post)){
            curl_setopt($this->ch, CURLOPT_POST , 1);
            curl_setopt($this->ch, CURLOPT_POSTFIELDS , $post );
        }
        return $this;
    }
    
    /**
     * 设置代理 ,例如'68.119.83.81:27977'  
     * @param string $proxy
     * @return $this
     */
    public function setProxy($proxy)
    {
        if(!empty($proxy)){
            curl_setopt($this->ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            curl_setopt($this->ch, CURLOPT_PROXY,$proxy);
        }
        return $this;
    }
    
    /**
     * 设置伪造ip
     * @param string $ip
     * @return string
     */
    public function set_ip($ip)
    {
        if(!empty($ip)) {
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, array("X-FORWARDED-FOR:$ip", "CLIENT-IP:$ip"));
        }
        
        return $ip;
    }
    
    /**
     * 设置是否显示返回头信息
     * @param bool $show
     * @return $this
     */
    public function showHeader($show = false)
    {
        $this->show_header = $show;

        return $this;
    }
    
    /**
     * 设置请求头信息 - 游览器标识
     * @param string $str
     * @return $this
     */
    public function setUserAgent($str)
    {
        $this->set_useragent = $str;
        
        return $this;
    }
    
    /**
     * 执行
     * @param string $url
     */
    public function exec ($url = '')
    {
        $url = empty($url) ? $this->url : $url;
        
        curl_setopt($this->ch, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER , 1 );    //获取的信息以文件流的形式返回
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($this->ch, CURLOPT_USERAGENT, $this->set_useragent); // 模拟用户使用的浏览器
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($this->ch, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->set_time_out);  //超时设置
        curl_setopt($this->ch, CURLOPT_HEADER, $this->show_header); // 显示返回的Header区域内容
        curl_setopt($this->ch, CURLOPT_NOBODY, 0);//不返回response body内容 
        
        $res = curl_exec($this->ch);
        $this->flag_if_have_run = true;
        
        // 获取错误代码, 无错误返回`0`
        if (curl_errno($this->ch) > 0) {
            //echo 'Errno'.curl_error($this->ch);
            return false;
        }
        
        // 数组形式返回头信息和body信息
        if($this->show_header == 1) { 
            list($header, $body) = explode("\r\n\r\n", $res);
            $arr['header'] = $header;
            $arr['body'] = $body;
            if($this->cookie_mode == 1 || $this->cookie_mode == 3){
                preg_match_all("/set\-cookie:([^\r\n]*)/i", $header, $matches);
                //print_r($matches);
                if($matches && isset($matches[1]) ){
                    $val = implode(';',array_unique(explode(';',implode(';',$matches[1])))); //去重处理
                    if($val)
                        $this->save_cookie($val); //设置客户端保存cookie
                }
            }
            
            if($arr) {
                return $arr;
            }
        }
        
        return $res;  
    }
    
    /**
     * 返回  curl_getinfo信息
     * @return mixed
     */
    public function getInfo()
    {
        if($this->flag_if_have_run) {
            return curl_getinfo($this->ch);
        } else {
            throw new \Exception("<h1>需先运行( 执行exec )，再获取信息</h1>");
        }
    }
}
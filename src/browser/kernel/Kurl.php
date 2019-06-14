<?php
namespace qpf\browser\kernel;

/**
 * Kurl 封装了curl的类, 代表一次会话请求
 */
class Kurl
{
    /**
     * curl句柄
     * @var resource
     */
    private $ch;
    /**
     * curl组句柄
     * @var resource
     */
    private $mh;
    /**
     * url值
     * @var string|array
     */
    private $url;
    /**
     * Curl选项
     * @var array
     */
    private $options = [];
    
    /**
     * 构造函数
     * @param string $url 可选, 设置CURLOPT_URL的值
     * @param array $options 可选, 设置curl_setopt相关
     */
    public function __construct($url = '', array $options = [])
    {
        if (!empty($url)) {
            $this->setUrl($url);
        }
        
        if(!empty($options)) {
            $this->setOptions($options);
        }
    }
    
    /**
     * 获取curl会话句柄
     * @param string $url 可选, 设置CURLOPT_URL的值
     * @return resource|false 返回false代表创建失败
     */
    public function getCh($url = null)
    {
        if($this->ch === null) {
            $this->ch = curl_init($url);
        }
        
        return $this->ch;
    }
    
    /**
     * 获取curl批处理句柄
     * @return resource|false 返回false代表创建失败
     */
    public function getMh()
    {
        if($this->mh === null) {
            $this->mh = curl_multi_init();
        }
        
        return $this->mh;
    }
    
    /**
     * 设置url地址, 当资源已打开将无效
     * @param string $value
     * @return $this
     */
    public function setUrl($value)
    {
        if($this->ch === null && $this->mh === null) {
            $this->url = $value;
        } else {
            //
        }
        
        return $this;
    }
    
    /**
     * 获取url地址
     * @return null|string
     */
    public function getUrl()
    {
        return isset($this->options['url']) ? $this->options['url'] : null;
    }

    /**
     * 设置会话选项
     * @param string|int $name 选项名或选项常量`CURLOPT_*`
     * @param mixed $value 选项值
     * @return $this
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
        
        return $this;
    }
    
    /**
     * 获取会话选项的值
     * @param string|int $name 选项名或选项常量`CURLOPT_*`
     * @return mixed 返回NULL代表未设置
     */
    public function getOption($name)
    {
        return isset($this->options[$name]) ? $this->options[$name] : null;
    }
    
    /**
     * 设置多个会话选项
     * @param array $opt
     * @return $this
     */
    public function setOptions(array $opt)
    {
        $this->options = array_merge($this->options, $opt);
        
        return $this;
    }
    
    /**
     * 获取所有会话选项
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * 关闭会话并释放所有资源
     * @return void
     */
    public function close()
    {
        curl_close($this->ch);
    }
    
    /**
     * 关闭一组curl句柄
     * @return void
     */
    public function multiClose()
    {
        curl_multi_close($this->mh);
    }
    
    /**
     * 重置对象
     */
    public function reset()
    {
        $this->ch = null;
        $this->mh = null;
        $this->options = [];
    }
    
    /**
     * 运行当前会话连接
     */
    public function exec()
    {
        
        
        // 自动释放资源
        $this->close();
    }
    
    /**
     * 运行当前会话的子连接
     * @return int 返回curl预定义的常量, 仅关于整个批处理栈相关的错误, 
     * 即使返回CURLM_OK单个传输仍可能有问题.
     */
    public function multiExec()
    {
        do {
            // int $runing 引用变量, 用来标识操作是否仍在执行
            curl_multi_exec($this->mh, $runing);
            curl_multi_select($this->mh);
        } while ($runing > 0);
        
        // 自动释放一组资源
        $this->multiClose();
    }
}
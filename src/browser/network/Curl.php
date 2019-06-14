<?php
namespace qpf\browser\network;

/**
 * Curl 网络请求
 */
class Curl
{
    /**
     * 状态
     * @var int
     */
    protected $code;
    /**
     * 结果
     * @var mixed
     */
    protected $result;
    
    /**
     * 发送GET请求
     * @param string $url
     * @return string
     */
    public function get($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); // (string) 需要获取的 URL地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // (bool) true获取的信息以字符串返回，而不是直接输出
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // (bool) 是否验证SLL证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);// (bool) 是否检查SLL与服务器匹配
        if (curl_exec($ch) === false) {
            throw new \Exception(curl_error($ch));
            $data = '';
        } else {
            $data = curl_multi_getcontent($ch);
        }
        
        $this->setCode(curl_getinfo($ch, CURLINFO_HTTP_CODE));
        curl_close($ch);
        $this->setResult($data);
        
        return $data;
    }

    /**
     * 发送POST请求
     * @param string $url 地址
     * @param array $postData 发送的POST请求参数
     * @return string
     */
    public function post($url, array $postData = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); // 超时时间
        curl_setopt($ch, CURLOPT_POST, true); // 发送POST请求
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);// 发送参数
        if (curl_exec($ch) === false) {
            throw new \Exception(curl_error($ch));
            $data = '';
        } else {
            $data = curl_multi_getcontent($ch);
        }
        $this->setResult($data);
        $this->setCode(curl_getinfo($ch, CURLINFO_HTTP_CODE));
        curl_close($ch);
        
        return $data;
    }
    
    /**
     * 获取结果
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }
    
    /**
     * 设置结果
     * @param mixed $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }
    
    /**
     * 获取状态码
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }
    
    /**
     * 设置状态码
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }
}
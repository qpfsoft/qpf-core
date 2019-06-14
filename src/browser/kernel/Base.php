<?php
namespace qpf\browser\kernel;

/**
 * 内核基础
 */
class Base
{
    /**
     * 用户代理管理器
     * @var UserAgent
     */
    protected $userAgent;
    
    /**
     * User-Agent游览器标识
     * @return UserAgent
     */
    public function getUserAgent()
    {
        if($this->userAgent === null) {
            $this->userAgent = new UserAgent();
        }
        return $this->userAgent;
    }
    
    
}
<?php
namespace qpf\base;

/**
 * 路径属性特征
 */
trait PathPropertyTrait
{
    /**
     * 应用空间名称
     * @var string
     */
    protected $zonename = 'app';
    /**
     * 应用空间目录
     * @var string
     */
    protected $zonePath;
    /**
     * 入口目录
     * @var string
     */
    protected $webPath;
    /**
     * 根目录
     * @var string
     */
    protected $rootPath;
    /**
     * vendor目录
     * @var string
     */
    protected $vendorPath;
    /**
     * qpfsoft目录路径
     * @var string
     */
    protected $qpfsoftPath;
    /**
     * 框架目录
     * @var string
     */
    protected $qpfPath;
    /**
     * 当前APP模块的路由目录
     * @var string
     */
    protected $routePath;
    /**
     * 当前APP模块的运行目录
     * @var string
     */
    protected $runtimePath;
    /**
     * 配置目录
     * @var string
     */
    protected $configPath;
    
    /**
     * 返回空间命名
     * @return string
     */
    public function getZonename()
    {
        return $this->zonename;
    }
    
    /**
     * 返回应用空间目录路径
     * @return string
     */
    public function getZonePath()
    {
        return $this->zonePath;
    }
    
    /**
     * 返回入口文件所在目录
     * @return string
     */
    public function getWebPath()
    {
        return $this->webPath;
    }
    
    /**
     * 返回根目录路径
     * @return string
     */
    public function getRootPath()
    {
        return $this->rootPath;
    }
    
    /**
     * 返回vendor目录路径
     * @return string
     */
    public function getVendorPath()
    {
        return $this->vendorPath;
    }
    
    /**
     * 返回当前应用的route目录路径
     * @return string
     */
    public function getRoutePath()
    {
        return $this->routePath;
    }
    
    /**
     * 返回当前应用的runtime目录路径
     * @return string
     */
    public function getRuntimePath()
    {
        return $this->runtimePath;
    }
    
    /**
     * 返回config目录路径
     * @return string
     */
    public function getConfigPath()
    {
        return $this->configPath;
    }
    
    /**
     * 返回qpfsoft目录路径
     * @return string
     */
    public function getQpfsoftPath()
    {
        return $this->qpfsoftPath;
    }
    
    /**
     * 返回qpf目录路径
     * @return string
     */
    public function getQpfPath()
    {
        return $this->qpfPath;
    }
}
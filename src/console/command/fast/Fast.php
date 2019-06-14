<?php
namespace qpf\console\command\fast;

use qpf\console\command\Cmd;

/**
 * 加速优化命令
 */
class Fast extends Cmd
{
    /**
     * 默认
     */
    public function run()
    {
        // 打印可用操作
        return $this->help();
    }
    
    /**
     * 自动加载 - 加速
     * ```
     * php qpf fast:loader
     * ```
     */
    public function loader()
    {
        $loader = new Loader($this->app->getQpfPath() . '/autoload');
        return $loader->build();
    }
    
    /**
     * 应用配置 - 加速
     * ```
     * php qpf fast:config // 全局
     * php qpf fast:config index // 模块
     * ```
     * @return number
     */
    public function config($module = null)
    {
        if ($module !== null && !preg_match('/[a-z]+/', $module)) {
            return false;
        }
        
        $config = new Config($this->app, $module);
        
        return $config->build();
    }
    
    /**
     * 路由规则 - 加速
     * ```
     * php qpf fast:route
     * ```
     */
    public function route()
    {
        $route = $this->app->route->buildCache();
        return $route ? 'ok' : '开启路由缓存功能, 才允许生成路由缓存';
    }
}
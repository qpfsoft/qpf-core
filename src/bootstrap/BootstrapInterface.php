<?php
namespace qpf\bootstrap;

use qpf\base\Application;

/**
 * 引导接口
 */
interface BootstrapInterface
{
    /**
     * 引导程序
     * @param Application $app
     */
    public function bootstrap(Application $app);
}
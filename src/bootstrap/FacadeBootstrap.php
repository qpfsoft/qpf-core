<?php
namespace qpf\bootstrap;

use qpf\base\Facade;

class FacadeBootstrap implements BootstrapInterface
{
    /**
     * 引导程序
     */
    public function bootstrap(\qpf\base\Application $app)
    {
        Facade::setDependencyApp($app);
    }
}
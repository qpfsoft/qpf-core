<?php
namespace qpf\base;

/**
 * 应用程序初始化接口
 */
interface AppInitInterface
{
    /**
     * 应用初始化程序
     * @param Application $app
     */
    public function initialize(Application $app);
}
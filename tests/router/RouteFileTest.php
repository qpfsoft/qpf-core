<?php
use qpf\deunit\TestUnit;

include __DIR__ .'/../boot.php';

/**
 * 路由规则缓存文件测试
 */
class RouteFileTest extends TestUnit
{
    public $route;
    
    public function setUp()
    {
        QPF::app()->init();
        
        $this->route = QPF::$app->route;
    }
    
    /**
     * 载入路由设置文件
     * 
     * - 添加路由
     */
    public function testInclude()
    {
        $file = QPF::$app->getRoutePath() . '/route.php';
        include($file);
    }
    
    /**
     * 检查路由
     * 
     * - 将路由规则直接转换为数组数据
     * - 然后再进行匹配路由
     */
    public function testCheck()
    {
        $request = QPF::request();
        $result = $this->route->check($request, $request->path());
        return $result ? '匹配成功' : '匹配失败';
    }
}

echor(RouteFileTest::runTestUnit());
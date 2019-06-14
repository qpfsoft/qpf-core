<?php
use qpf\deunit\TestUnit;
use qpf\web\Request;

include __DIR__ . '/../boot.php';

class RouterTest extends TestUnit
{
    public $route;
    
    public function setUp()
    {
        QPF::app()->init();
        $this->route = QPF::$app->route;
    }
    
    /**
     * 添加get请求路由
     */
    public function testGetRule()
    {
        $this->route->get('login', 'index/login/user');
        
        $this->route->get('login/:name', 'login/home')->pattern('name', '[\d]+');
        $this->route->get('login/vip', 'admin/login/vip')->pattern('name', '[\d]+');
    }
    
    /**
     * 添加post请求路由
     */
    public function testAnyRule()
    {
        $this->route->post('index', 'index/index/index');
    }
    
    /**
     * 添加api.xx.com的首页
     */
    public function testDomainRule()
    {
        $this->route->domain('api', 'index/index/index');
    }
    
    /**
     * 添加域名路由
     */
    public function testDomainRuleGroup()
    {
        $this->route->domain('api', function(){
            $this->route->get('login', 'admin/login/user');
        });
    }
    
    /**
     * 转换为数组
     */
    public function testToArray()
    {
        return $this->route->toArray();
    }
    
    /**
     * 检查路由
     */
    public function testCheck()
    {
        $rule = $this->route->check(QPF::request(), 'login/vip');
        
        return $rule;
    }
}

echor(RouterTest::runTestUnit());
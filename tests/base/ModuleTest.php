<?php
use qpf\deunit\TestUnit;
use qpf\base\Controller;
use qpf\base\Module;

include __DIR__ . '/../boot.php';

class ModuleTest extends TestUnit
{
    
    /**
     * 测试控制器名称解析
     */
    public function testParseName()
    {
        $contr = new Module();

        return ([
            'user-login'    => $contr->parseName('user-login'),
            'upVip'     => $contr->parseName('upVip'),
            'auth'  => $contr->parseName('auth'),
            'authController'    => $contr->parseName('authController'),
            'auth-controller'   => $contr->parseName('authController'),
        ]);
    }
}

echor(ModuleTest::runTestUnit());
<?php
use qpf\deunit\TestUnit;
use qpf\router\Rule;
use qpf\router\Router;

include __DIR__ . '/../boot.php';

/**
 * 路由规则
 *
 * - 规则始终是完整匹配的! 正则会自动`^rule$`前后指定开始与结束符
 */
class RuleTest extends TestUnit
{
    public function setUp()
    {
        QPF::app()->init();
    }
    
    public function testRule()
    {
        $rule = new Rule([
            'rule'  => 'home',
            'match' => 'index/index/index',
        ]);
        
        return $rule->parseRule();
    }
    
    /**
     * - 路由以`:`开头代表动态变量
     * - 可单独定义变量的匹配表达式
     */
    public function testRule2()
    {
        $rule = new Rule([
            'rule'  => 'home/:id',
            'match' => 'index/index/:id',
        ]);
        $rule->pattern('id', '\d+');
        
        
        return [
            'express'   => $rule->parseRule(),
            'check'     => $rule->check('home/100000'),
            'params'    => $rule->getParams(),
            'to'        => $rule->getMatch(),
        ];
    }
    
    /**
     * - `[]`可选规则段
     * - 可选规则段, 必须放到最后, 否则可选参数后面
     */
    public function testRule3()
    {
        $rule = new Rule([
            'rule'  => 'home/[:id]',
            'match' => 'index/index/index',
        ]);
        
        return [
            'express'   => $rule->parseRule(),
            'check'     => [
                'home/100000'   => $rule->check('home/100000'),
                'home'   => $rule->check('home'),
            ],
            'params'    => $rule->getParams(),
            'to'        => $rule->getMatch(),
        ];
    }
    
    /**
     * - `[]`可选规则参数使用中括号包裹
     * - 可选规则参数, 最好放在最后
     * - 可选参数后面的参数, 也建议写为可选
     */
    public function testRule4()
    {
        $rule = new Rule([
            'rule'  => 'home/[:id]/[:name]',
            'match' => 'index/index/index',
        ]);
  
        $rule->pattern([
            'id' => '\d+',
            'name' => '\w+'
        ]);
        
        return [
            'express'   => $rule->parseRule(),
            'check'     => [
                'home/100000'   => $rule->check('home/100000'),
                'home/100000/admin'   => $rule->check('home/100000/admin'),
                'home'   => $rule->check('home'), // true
            ],
            'params'    => $rule->getParams(),
            'to'        => $rule->getMatch(),
        ];
    }
    
    /**
     * - `[]`可选规则参数使用中括号包裹
     * - 可选规则参数, 后面的变量不是可选的, 不建议使用这样, 除非知道最终的效果
     */
    public function testRule5()
    {
        $rule = new Rule([
            'rule'  => 'home/[:id]/:name',
            'match' => 'index/index/index',
        ]);

        $rule->pattern([
            'id' => '\d+',
            'name' => '\w+'
        ]);
        
        return [
            'express'   => $rule->parseRule(),
            'check'     => [
                'home/100000'   => $rule->check('home/100000'),
                'home/100000/admin'   => $rule->check('home/100000/admin'),
                'home/admin'   => $rule->check('home/admin'), // true
                'home'   => $rule->check('home'), // false
            ],
            'params'    => $rule->getParams(),
            'to'        => $rule->getMatch(),
        ];
    }
    
    public function testRule6()
    {
        $rule = new Rule([
            'rule'  => 'home/<id>/:name',
            'match' => 'index/index/index',
        ]);
        
        return $rule->toArray();
    }
}

echor(RuleTest::runTestUnit());
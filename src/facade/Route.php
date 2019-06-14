<?php
namespace qpf\facade;

use qpf\base\Facade;
use qpf\router\Router;
use qpf\router\Rule;

/**
 * 路由静态代理类
 * 
 * @see Router
 * 
 * @method static Rule rule(string $rule, $match, string $method) 路由规则
 * @method static Rule any(string $rule, $match) any任何请求类型
 * @method static Rule get(string $rule, $match) get请求路由
 * @method static Rule post(string $rule, $match) post请求路由
 * @method static Rule put(string $rule, $match) put请求路由
 * @method static Rule delete(string $rule, $match) delete请求路由
 * @method static Rule patch(string $rule, $match) patch请求路由
 * @method static Rule head(string $rule, $match) head请求路由
 * @method static Rule options(string $rule, $match) options请求路由
 * @method static Router domain(string $host, mixed $rules) 域名路由
 * @method static void resource(string $rule, string $match) 资源路由
 * @method static Rule callback(string $rule, mixed $match, string $method) 回调请求路由
 * @method static Rule url(string $rule, string $match, string $method) 回调请求路由
 * @method static Rule view(string $rule, string $match, string $method) 视图请求路由
 */
class Route extends Facade
{
    protected static function getFacadeClass()
    {
        return 'route';
    }
}
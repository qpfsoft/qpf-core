<?php
namespace qpf\router;

use qpf;
use qpf\base\Core;
use qpf\base\Application;
use qpf\web\Request;
use qpf\file\Dir;

/**
 * 路由器
 * 
 * 路由采集器
 */
class Router extends Core
{
    /**
     * 缓存路由规则
     * - 可加速解析速度
     * @var bool
     */
    private $cache = true;
    /**
     * 路由规则
     * `域名, 请求方式, 分组`
     * @var RuleGroup[]
     */
    private $rules = [];
    /**
     * 当前域名标识
     *
     * - 由类自动设置
     * @var string
     */
    private $currentDomain;
    /**
     * 应用程序
     * @var Application
     */
    private $app;

    /**
     * 构造函数
     * @param Application $app 应用程序
     * @param array $config 对象配置
     */
    public function __construct(Application $app, $config = [])
    {
        $this->app = $app;
        parent::__construct($config);
    }

    protected function boot()
    {
        $this->currentDomain = $this->getDefDoamin();
    }
    
    /**
     * 是否启用路由解析缓存
     * @param bool $bool
     */
    public function cahce($bool)
    {
        $this->cache = $bool;
    }
    
    /**
     * 获取路由规则集合
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }
    
    /**
     * 获取变量默认规则
     * @return string
     */
    public function defvar(): string
    {
        return $this->defvar;
    }
    
    /**
     * 获取默认域名标识
     * @return string
     */
    protected function getDefDoamin()
    {
        return 'www';
    }

    /**
     * 解析路由规则的分组名称
     * @param string $rule 路由规则
     * @return string 返回该规则的分组名称
     */
    protected function parseRuleGrouping(string $rule): string
    {
        $pos = strpos(trim($rule, '/'), '/');
        
        if ($pos !== false) {
            $group =  substr($rule, 0, $pos);
        } else {
            $group =  $rule;
        }
        
        return empty($group) ? '*' : $group;
    }
    
    /**
     * 获取指定域名的路由规则
     * @param string $domain 域名
     * @return array
     */
    protected function getDomainRules($domain): array
    {
        if (isset($this->rules[$domain])) {
            return $this->rules[$domain];
        } elseif ($domain === 'www') {
            return $this->rules[$this->getDefDoamin()];
        }
        
        return [];
    }
    
    /**
     * 路由规则排序 
     * 
     * - 根据规则长度排序, 长的在前
     * @param array $rules 路由规则集合
     * @param string $sort 排序规则{desc:降序,asc:升序}, 默认`desc`降序z-a
     */
    protected function rulesSort(array $rules, $sort = 'desc')
    {
        
    }
    
    /**
     * 注册域名路由
     * @param string $host 主机记录
     * @param \Closure $rules 闭包路由组或绑定域名首页
     */
    public function domain($host, \Closure $rules): Router
    {
        if ($rules instanceof \Closure) {
            $current = $this->currentDomain;
            $this->currentDomain = $host;
            call_user_func($rules);
            $this->currentDomain = $current;
        }
        
        return $this;
    }
    
    /**
     * 获取当前域名规则分组
     * @return RuleGroup
     */
    protected function getDomainRuleGroup(): RuleGroup
    {
        if (!isset($this->rules[$this->currentDomain])) {
            $this->rules[$this->currentDomain] = new RuleGroup();
        }
        
        return $this->rules[$this->currentDomain];
    }
    
    /**
     * 添加路由规则
     * @param string $method 请求类型
     * @param string $rule 路由规则
     * @param mixed $match 匹配结果
     * @param string $type 路由类型, 默认`controller`即控制器路由
     * @return Rule 返回该路由规则操作实例
     */
    protected function addRule(string $method, string $rule, $match, string $type = 'controller'): Rule
    {
        return call_user_func_array([$this->getDomainRuleGroup(), 'rule'], [$rule, $match, $method, $type]);
    }
    
    /**
     * 添加路由规则
     * @param string $rule 规则描述
     * @param string|\Closure $match 匹配路线
     * @param string $method 请求类型, 多种用`|`分割, 不区分大小写
     */
    public function rule(string $rule, $match, string $method)
    {
        return $this->addRule($method, $rule, $match);
    }
    
    public function any($rule, $match)
    {
        return $this->addRule('any', $rule, $match);
    }
    
    public function get($rule, $match)
    {
        return $this->addRule('get', $rule, $match);
    }
    
    public function post($rule, $match)
    {
        return $this->addRule('post', $rule, $match);
    }
    
    public function put($rule, $match)
    {
        return $this->addRule('put', $rule, $match);
    }
    
    public function delete($rule, $match)
    {
        return $this->addRule('delete', $rule, $match);
    }
    
    public function patch($rule, $match)
    {
        return $this->addRule('patch', $rule, $match);
    }
    
    public function head($rule, $match)
    {
        return $this->addRule('head', $rule, $match);
    }
    
    public function options($rule, $match)
    {
        return $this->addRule('options', $rule, $match);
    }
    
    /**
     * 控制器路由
     * @param string $rule
     * @param string $match
     * @param string $method 请求类型
     * @return Rule
     */
    public function controller(string $rule, string $match, string $method): Rule
    {
        return $this->addRule($method, $rule, $match, 'controller');
    }
    
    /**
     * 回调路由
     * @param string $rule 路由规则
     * @param callable $match 回调或闭包
     * @param string $method 请求类型
     * @return Rule
     */
    public function callback(string $rule, $match, string $method): Rule
    {
        return $this->addRule($method, $rule, $match, 'callback');
    }
    
    /**
     * URL路由
     * @param string $rule 路由规则
     * @param string $match 目标URL地址, 需带协议
     * @param string $method 请求类型
     * @return Rule
     */
    public function url(string $rule, string $match, string $method): Rule
    {
        return $this->addRule($method, $rule, $match, 'url');
    }
    
    /**
     * 视图路由
     * @param string $rule 路由规则
     * @param string $match 视图文件
     * @param string $method 请求类型
     * @return Rule
     */
    public function view(string $rule, string $match, string $method): Rule
    {
        return $this->addRule($method, $rule, $match, 'view');
    }
    
    /**
     * 资源路由
     * 
     * 批量注册多条路由规则到指定控制器
     * @param string $rule 路由规则
     * @param string $match 指定到控制器, 不指定操作
     * @return void
     */
    public function resource(string $rule, string $match): void
    {
        $reg = [
            ['get', $rule, $match . '/index'], // 首页
            ['get', $rule . '/create', $match . '/create'], // 创建资源
            ['post', $rule, $match . '/save'], // 保存资源
            ['get', $rule . '/:id',  $match . '/read'], // 读取资源
            ['get', $rule . '/:id/edit', $match . '/edit'], // 编辑资源
            ['put', $rule . '/:id', $match . '/update'], // 更新资源
            ['delete', $rule . '/:id', $match . '/delete'], // 删除资源
        ];
        
        foreach ($reg as $item) {
            $this->addRule($item[0], $item[1], $item[2]);
        }
    }

    /**
     * 生成路由缓存文件
     * @return array|false 返回缓存的内容
     */
    public function buildCache()
    {
        if ($this->cache) {
            $this->loadRules(); // 加载所有路由定义文件
            $data = $this->toArray(); // 解析全部路由定义为数组
            $this->buildDomainCache($data); // 按域名分别生成缓存文件
            return $data;
        }
        
        return false;
    }
    
    /**
     * 按域名分组生成缓存文件
     * @param array $data 路由规则集合
     */
    protected function buildDomainCache(array $data)
    {
        foreach ($data as $domain => $list) {
            Dir::single()->createFile($this->getCachePath() . '/' . $domain . '.php', serialize($list));
        }
    }

    /**
     * 获取路由定义目录路径
     * @return string
     */
    public function getRulePath(): string
    {
        return $this->app->getRoutePath();
    }
    
    /**
     * 获取路由缓存目录路径
     * @return string
     */
    public function getCachePath(): string
    {
        return $this->app->getRuntimePath() . '/route';
    }
    
    /**
     * 加载指定域名分组的定义
     * @param string $domain 域名分组名称
     * @return bool
     */
    public function loadRule(string $domain): bool
    {
        $file = $this->getRulePath() . '/' . $domain . '.php';
        if (is_file($file)) {
            if ($domain === 'route' || $domain === 'www') {
                include($file); // 全局路由定义文件
            } else {
                $this->domain($domain, function() use ($file){
                    include($file);
                });
            }
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 加载所有域名分组的定义
     */
    public function loadRules()
    {
        foreach (glob($this->getRulePath() . '/*.php') as $file) {
            $domian = pathinfo($file, PATHINFO_FILENAME);
            if ($domian === 'route') {
                include($file); // 全局路由定义文件
            } else {
                $this->domain($domian, function() use ($file){
                    include($file);
                });
            }
        }
    }
    
    /**
     * 获取路由数据
     * @return array
     */
    public function getData($domain): array
    {
        if ($this->cache) {
            $file = $this->getCachePath() . '/' . $domain . '.php';
            
            if (is_file($file)) {
                $cache = [$domain => unserialize(file_get_contents($file))];
            } elseif (!is_dir($this->getCachePath())) {
                // TODO bug, 如果该域名下未定义路由, 将总是触发重新生成路由
                // 解决方案: 如果缓存目录存在, 说明生成过缓存, 就不重新生成!
                $cache = $this->buildCache();
                return $cache ?: [];
            } else {
                $cache = [];
            }
        } else {
            $this->loadRule('route'); // 加载全局定义
            $this->loadRule($domain); // 加载分组定义

            $cache = $this->toArray();
        }
        
        return $cache;
    }
    
    /**
     * 检查路由
     * @param string $type 请求类型
     * @param string $route 请求路线
     * @return array|false 匹配成功返回调度数组, 失败false
     */
    public function check(Request $request, string $url)
    {
        $domain = $request->subDomain();

        // 默认域名分组`*`
        if (empty($domain) || $domain === 'www') {
            $domain = $this->getDefDoamin();
        }
        
        // 当前请求类型
        $method = strtolower($request->method());
        
        // 当前请求URL
        if (empty($url) || $url === '/') {
            $url = '/';
            $group = '/';
            $q = 0;
        } else {
            $path = explode('/', $url);
            $group = current($path);
            $q = count($path);
        }
        
        // 路由规则数据
        $rules = $this->getData($domain);
        
        // TODO 打印当前可用路由规则
        //echor($rules);
        
        // 优先检测当前请求类型
        $result = $this->checkTypeRule($url, $q, $method, $domain, $group, $rules);
        if ($result) {
            return $result;
        } else {
            // 不匹配时, 再检查`any`类型
            return $this->checkTypeRule($url, $q, 'any', $domain, $group, $rules);
        }
    }
    
    /**
     * 检查请求类型路由规则
     * @param string $url 当前请求URL地址
     * @param int $q 请求URL的Q值
     * @param string $method 请求类型分组名称
     * @param string $domain 域名分组名称
     * @param string $group 规则分组名称
     * @param array $rules 规则集合
     * @return mixed 匹配失败返回false, 成功将返回array或string类型
     */
    protected function checkTypeRule($url, $q, $method, $domain, $group, $rules)
    {
        if (isset($rules[$domain][$method][$group])) {
            foreach ($rules[$domain][$method][$group] as $rule) {
                if ($rule['q'] >= $q) {
                    $item = new Rule($rule);
                    if ($item->check($url)) {
                        return ['type' => $item->getType(), 'action' => $item->getMatch(true), 'param' => $item->getParams()];
                    }
                }
            }
        } elseif (isset($rules[$domain]['extra'][$method])) {
            foreach ($rules[$domain]['extra'][$method] as $rule) {
                if ($rule['q'] >= $q) {
                    $item = new Rule($rule);
                    if ($item->check($url)) {
                        return ['type' => $item->getType(), 'action' => $item->getMatch(true), 'param' => $item->getParams()];
                    }
                }
            }
        }
        
        return false;
    }

    /**
     * 获取路由解析数组
     * @return array
     */
    public function toArray(): array
    {
        $rules = $this->rules;
        
        foreach ($rules as $domain => $group) {
            $rules[$domain] = $group->toArray();
        }
        
        return $rules;
    }
}
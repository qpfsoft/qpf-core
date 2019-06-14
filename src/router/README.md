路由规则
===

> 域名分组 > 请求类型分组 > 规则分组 > 路由规则 + Q

路由规则的匹配是按照以下3个分组来快速定位:

- 域名分组
  - 按子域名进行分组, 支持二级域名
  - 域名分组是根分组, 所以路由规则按域名完全隔离!
  - 特别说明`www` 子域名是默认域名分组
    - 即 `xxx.com` 与 `www.xxx.com` 都在`www`域名分组内!
- 请求类型分组
  - `http`请求类型, 每个请求方法都是一个分组
    - 当一个路由规则支持`get|post`两种请求类型, 会在两个分组下都添加该规则!
  - 特殊`any`类型, 即支持任何请求类型
    - 该类型优先级最低
    - 系统会先匹配当前请求类型的路由规则, 若未命中, 才会匹配`any`分组的路由
- 规则分组
  - 将规则的第一段路径作为分组名
    - 所以不支持第一段路径是变量规则
  - 后面单独添加了动态路由的支持
    - 单独的原因是路由分组与动态路由的概念是冲突的.
    - 例如`login/*`的路由分组, 与 动态路由`<:string>/*`, 当同时添加它们时, 会出现兼容问题.
    - 因此当路由分组不存在时, 才会去匹配动态路由!
    - 动态路由的优先级最低!
- 规则分组的Q值
  - Q值代表路由规则优先级评估,  它是为了实现尽可能仅执行一次正则匹配, 快速定位路由规则

### 路由变量

- 必选变量`:var`, 可选变量`[:var]`
- 必选变量`<var>`, 可选变量`<var?>`

### 变量规则

- 默认变量规则`\w+` 等于`[A-Za-z0-9_]`
- 但是URL允许`-`符号, 即`[\w\-]+`

```php
Router::rule('login/:user', 'home/login', 'get');
// 等效
Router::rule('login/<user>', 'home/login', 'get');

// url: php.net/login/admin - true
// url: php.net/login/10000 - true

Router::rule('/login', 'home/login', 'get'); // error 错误的路由, 不能以'/'开头
```

#### 组合变量

- 可自定义分隔符

```php
Router::rule('news/item-<type>-<page>', 'news/list', 'get')
    ->pattern(['type' => '[a-zA-Z]+', 'page' => '\d+']);
Router::rule('news/item-<name><page?>', 'news/view', 'get')
    ->pattern(['type' => '[a-zA-Z]+', 'page' => '\d+']);
```

### 动态路由

支持动态路由, 即`域名分组 > 动态分组 > 请求类型分组 > 路由规则 + Q `,  没有规则分组!

```php
Route::get('item-<type>-<page>', 'index/news/item')->pattern(['page' => '\d+']);
```

- 注意动态路由仅在, 路由组不存在时才会触发!
  - 一是确保固定前缀路由组, 始终可用!
  - 二是如果动态路由覆盖其他路由规则, 就没有意义
  - 这样做可确保路由定义与使用时的清晰度!

### 首页路由

用于定义访问域名时, 默认访问的路线, 默认请求类型为`any`且无法修改;

```php
Route::domain('/', 'index/news/index');
Route::domain('api', 'api/index/index');
Route::domain('free.api', 'api/free/index');

# 首页路由支持所有类型
Route::get('/', 'api/free/index');
```

### 多请求类型

```php
Route::rule('test', 'index/test/index', 'get|post');
```

### 回调路由

```php
// 闭包
Route::callback('tt', function (){
    return 'ok';
}, 'get'); // ok

// 静态方法
Route::callback('tt', 'QPF::version', 'post'); // 1.1.2

// 回调方法 ['app\类', '方法名']
Route::callback('tt', ['We', 'hi'], 'put');
```

### url路由

URL地址重定向

```php
Route::url('qpf', '//www.quiun.com', 'any'); // 正确
Route::url('qpf', 'http://www.quiun.com', 'any'); // 正确
Route::url('qpf', 'www.quiun.com', 'any'); // 错误 to `localhost/www.quiun.com`
```

### 视图路由

- `html`静态视图文件, 直接读取文件内容. 然后输出.
- `php`动态视图文件, `include`文件后获取所有输出内容.

```php
# 支持路径别名
Route::view('ui', '@web/abc.php', 'any');
```





### 配置

路由配置定义在 `@config/web.php`配置文件!

```php
[
    'onRoute'	=> true, // 是否启用路由功能
    'strictRoute'	=> false, // 是否严格匹配, 请求必须匹配一条规则, 否则禁止访问
    'routeCache'	=> true, // 是否生成路由解析加速缓存
]
```

备注: 路由服务被web服务管理, 所以配置在web配置中!



### 解析加速

所有的路由定义可在`@route/route.php`文件内编写, 但同时也支持以域名分组名为文件名的碎片配置!

```php
/@route
|   |- route.php // 可定义所有域名分组的规则
|   |- www.php // 访问根域名与www一级域名
|   |- api.php // 一级域名
|   |- login.api.php // 二级域名
```

> 路由规则可全部定义在route文件内, 也可以按域名分散到多个文件.



- 这样的分文件或不分文件, 都不会对路由解析加速文件造成影响.  
  - 分文件的好处是, 方便路由规则的管理, 与路由规则冲突的排查
  - 并且在独立文件的域名规则分组中, 无需声明当前路由所绑定到的域名!
    - 即在该域名分组文件内, 定义的路由都会在该域名下有效!
- 所有的路由定义在route文件内
  - 在未开启解析缓存时, 会加载多余的路由规则



#### 解析加速原理

- 未开启缓存
  - 首先会加载`route.php` 全局定义, 确保在该文件定义的域名路由始终有效
    - 为了兼容单路由定义文件
  - 会加载当前域名的同名的路由定义文件. 如果存在的话
- 开启缓存
  - 会查找`@runtime/route/`目录下当前请求域名的同名路由定义文件, 例如`www.php`
    - 存在就直接返回`['domain'=> cacheData]`;
    - 注意, 缓存仅仅保存了`cacheData`数据, 需要与域名组合成数组返回!
  - 如果不存在缓存文件, 会检查是否存在`@runtime/route/`目录
    - 不存在在, 将生成路由加速缓存
    - 否则将返回空数组

这样做的原因是, 防止某域名未定义路由规则, 始终无法获取到路由定义缓存.造成始终会生成路由加速缓存! 
因此, 删除`@runtime/route/`目录将会触发, 重新生成路由解析缓存!



>  路由检查时, 只会加载当前域名相关的路由规则! 



#### 命令

通过控制台生成路由规则解析加速缓存!

```
php qpf fast:route
```


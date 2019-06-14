控制台
===

执行命令, 由于未指定操作, 将自动执行`build:run`默认操作.

```
php qpf build
# 等价
php qpf build:run
```

查看可执行的操作:

```
php qpf build:help
```

查看某个操作的参数选项:

```
php qpf build:help app
```



## 命令附录

- 生成文件或目录结构


```
# 打印生成命令的可用操作
qpf build:run

# 创建应用模块目录结果, 参数示例`home`
qpf build:app <应用模块名>

# 创建控制器, 参数示例 `home/index` 不覆盖已存在控制器
qpf build:controller <模块名/控制器>

# 创建资源控制器, 不覆盖已存在控制器
qpf build:resource <模块名/控制器>
```

- 加速优化命令

```
# 打印可用操作
qpf fast:run

# 生成自动加载加速缓存
qpf fast:loader

# 应用配置加速
qpf fast:config // 全局
qpf fast:config index // 模块

# 路由解析加速
qpf fast:route
```

- 其它
```
# 修复cgi无法执行命令
qpf run:fixcmd // 该命令会生成一份cli环境的环境参数. 并保存在`cli_env.php`
```


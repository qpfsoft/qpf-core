日志
===

## bug警示

- `runtime` 中生成的缓存, 由于未及时更新会造成bug!
	- 在新的环境, 应该清空所以, 重新生成临时文件

### 2019年5月24日 15:25:03

- 发现bug
	- `qpf fast:config <app_name>` 初始化加速文件, 造成web解析程序无法绑定域名到模块!
	```
	排查结果: 
	- 生成的加速文件存在域名绑定配置! 所以怀疑配置没有正确的初始化到类
	定位错误:
	由于之前数组文件文件内容生成模板(\qpf\builder\template\ArrayConfigFile), 重构, `return [];`造成直接返回了配置,导致后续
	初始化脚本, 没有执行!
	注意:
	将数组转换为php数组源代码, 以`\qpf\builder\code\ArrayCode`类负责了
	备注: 源代码临时存储, 可使用序列化来解决!
	------
	排查: 
	是模块生成命令, 错误的将`common.php`, 全局函数库生成为了, 数组配置文件! 
	```

- 请求参数异常
  - 获取PahtInfo时会拦截`r`查询参数, 但需要确保获取get参数方法之前, 拦截!
  - 已修复: 在获取get请求参数前, 会先检查是pathinf是否为null, 以确保提前拦截.
  - 造成bug的问题, QPF::trace()记录页面跟踪消息内部, 提前检查了是否ajax请求, 触发$request->param()提前缓存全局请求参数, 连带调用获取get请求参数!

新增类

```
\qpf\builder\template\Html5File // 生成html5基础文件模板内容, 可添加js,css
```

自动生成

```
app模块目录结果, 新增再view/index/index.html 模板文件
```


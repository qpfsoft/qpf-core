资源Assets
===

### 前言

在MVC的框架中, 静态资源一般都部署在入口目录内(或第三方), 但资源被谁所有或使用却很难知道.

当要移植某个模块时, 将/static内所有文件都赋值给对方一份? 很定会包含多余的资源文件.

> 应用程序模块, 是一个独立的, 不可在一个模块内调用其它应用模块内的控制器或模型!
>
> 原因: 通过URL访问某个模块时, 系统只加载了该模块的配置文件!
>
> 兼容: 除非配置公用. 例如 表前缀, 数据库名. 都相同的情况下. (强烈不推荐)

#### 一个可能的解决办法:

按照视图的目录结构:
```
@web/static
|  |- 模块名
|  |    |- 控制器名
|  |    |    |- 操作名
|  |    |    |    |- img/
|  |    |    |    |- js/
|  |    |    |    |- css/
```

`但是这样做会造成资源重复, 公用的脚本,样式和图片等.

### 默认情况

```
/web
|  |- static/
|  |    |- image/
|  |    |- css/
|  |    |- js/
|  |    |- lib/
```

不管是图片还是脚本都会交叉存放. 无法很明确资源所属和使用情况!



assets` 和 `static` 的区别:

- `asset`
- `/static` 目录目录下的文件并不会被 Webpack 处理;

### 资源对象

一个资源对象可以存放 js, css, 图片, 和视频等;

资源包除了字母意思(一组静态资源的打包)外, 在QPF中的作用:

- 资源包被绑定应用程序模块
- 多个模块可公用一个资源包
	- 例如使用了相同的前端框架
- 资源包可能没有被释放, 需要通过编译后生成到@web/入口目录内.

> 输出目录和部署URL的关系 `@web` = > `http://domain.com/` 

参数:
- 资源包ID : 在全局唯一, 防止多个模块依赖的资源包被重复打包.
- 输出目录 : 打包输出





### 资源包

一个资源包就是在渲染一个html页面, 页面的css与js;

```html
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title></title>
		<!-- Assest::$css[] -->
	</head>
	<body>
	
		<!-- Assest::$js[] -->
	</body>
</html>
```
提前收集好, 一组css, 一组js. 渲染到页面, 通过资源包相互依赖, 解决重复加载

jquery包{
	'js' => [
		'@web/static/jquery/1.11/jquery.min.js'
	],
}

qpf-ui包{
	'css' => [
		'@web/static/qpf-ui/qpf.css'
	],
	'js' => [
		'@web/static/qpf-ui/qpf.js',
		'@web/static/qpf-ui/qpf.ext.js'
	],
	'require' => [
		'\qpf\x\jqueryAsset',
	],
}

require依赖的包会优先加载,
然后按照书写顺序一次写入


资源包的定义:
- 定义html页面使用到的js,css
- 设置的src目录路径, 会将该目录的内容全部复制到`@web/static` 目录下, 需要注意的是可能会覆盖,
- 全部复制后, 也方便使用图片

#### 模拟过程

[
	'srcPath' => `@qpf-ui/dst`, // 结尾无`/`
	'js'	=> [
		'//sdn.baidu.com/jquery/jquery.min.js', // 支持url
		'https://sdn.baidu.com/jquery/jquery.min.js',
		'/js/qpf.min.js', // 需要以`/`开头, 相对于srcPath
	],
	'css'	=> [
		'//sdn.baidu.com/css/jquery.min.css', // 支持url
		'https://sdn.baidu.com/css/jquery.min.css',
		'/qpf.min.css',
	]
]

```
<?php
 
/* @var $this \yii\web\View */
/* @var $content string */
 
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
 
// 注册全局加载
AppAsset::register($this);
 
// 按需加载css
AppAsset::addCss($this, Yii::$app->request->baseUrl."/css/site.css");
// 按需加载js
AppAsset::addJs($this, Yii::$app->request->baseUrl."/js/respond.min.js");
 
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
 
         
    <?= $content ?>
 
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
```





### WEB资源包

- web资源包
  - `WebAsset` 以列表为级别,  每个项代表一个需要安装的文件与目标位置.
  - `WebAssetPack` 以目录为级别, 直接安装资源到目标目录
- 所有web资源包, 需要实现`AssetInstallInterface`资源安装接口

web资源包通过`@qpf/assets.php`配置文件, 进行定义!

```
[
	'@qpfsoft/..../pack.php', // pack.php是web资源包的定义配置, 描述了资源包类型与定义
]
```

- 执行命令`php qpf web:asset`
  - 将会安装注册的web资源包
<?php
// ╭───────────────────────────────────────────────────────────┐
// │ QPF Framework [Key Studio]
// │-----------------------------------------------------------│
// │ Copyright (c) 2016-2019 quiun.com All rights reserved.
// │-----------------------------------------------------------│
// │ Author: qiun <qiun@163.com>
// ╰───────────────────────────────────────────────────────────┘
/* 
use qpf\deunit\Deunit;
use qpf\deunit\QPFUnit;

// 路径
$qpf_path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src';
$qpfsoft_path = dirname(dirname(__DIR__));

include $qpfsoft_path . '/deunit/src/Deunit.php';
// 初始化单元调试
Deunit::init();
// 注册命名空间
Deunit::$namespace['qpf'] = $qpf_path;
Deunit::$namespace['qpf\helper'] = $qpfsoft_path . '/helper/src';
Deunit::$namespace['qpf\error'] = $qpfsoft_path . '/error/src';
Deunit::$namespace['qpf\lang'] = $qpfsoft_path . '/lang/src';
Deunit::$namespace['qpf\deunit'] = $qpfsoft_path . '/deunit/src';

// 引导QPF类与助手函数
include $qpf_path . '/bootstrap.php';
QPFUnit::init($qpf_path); */

include __DIR__ . '/../src/bootstrap.php';




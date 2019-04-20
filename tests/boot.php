<?php
// ╭───────────────────────────────────────────────────────────┐
// │ QPF Framework [Key Studio]
// │-----------------------------------------------------------│
// │ Copyright (c) 2016-2019 quiun.com All rights reserved.
// │-----------------------------------------------------------│
// │ Author: qiun <qiun@163.com>
// ╰───────────────────────────────────────────────────────────┘
use qpf\deunit\Deunit;

include __DIR__ . '/deunit/Deunit.php';
include __DIR__ . '/../src/helper.php';

Deunit::$namespace['qpf'] = __DIR__ . '/../src';
Deunit::$namespace['qpf\helper'] = __DIR__ . '/../vendor/qpfsoft/helper/src';
Deunit::init();
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
include __DIR__ . '/helper.php';

Deunit::$namespace['qpf\helper'] = __DIR__ . '/../src';
Deunit::init();
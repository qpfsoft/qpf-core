<?php
use qpf\deunit\Deunit;
use qpf\deunit\QPFUnit;

include 'QPFUnit.php';
include 'Deunit.php';
Deunit::$namespace['qpf'] = QPFUnit::$QPF_PATH;
Deunit::init();

include QPFUnit::$QPF_PATH . '/bootstrap.php';


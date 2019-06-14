<?php
use qpf\captcha\Captcha;

include __DIR__ . '/../../unit.php';

$auth = new Captcha();

$auth->create();


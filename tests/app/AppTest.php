<?php
use qpf\base\Application;

include __DIR__ . '/../boot.php';

class AppTest
{
    public function base1()
    {
        $app = new Application();
        $app->init();
        echor($app);
    }
}

$test = new AppTest();

$test->base1();
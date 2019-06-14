<?php
namespace qpf\console\command\web;

use qpf\console\command\Cmd;
use qpf\assets\Webpack;

class Web extends Cmd
{
    /**
     * 默认执行
     */
    public function run()
    {
        return [
            'qpf web:asset' =>  '安装web静态资源包',
        ];
    }
    
    
    /**
     * 安装web静态资源
     * @cmd qpf web:asset
     */
    public function asset()
    {
        return (new Webpack())->install();
    }

    
}
<?php
namespace qpf\provider;

use qpf\base\ServiceProvider;
use qpf\log\Log;

class LogProvider extends ServiceProvider
{
    /**
     * 延迟加载
     * @var bool
     */
    public $defer = false;
    
    /**
     * 配置文件
     * @var array
     */
    public $cionfig = [
        'enableSave'    => true, // 启用日志保存
        
        // 日志存储器
        'storage'   => [
            // 单文件存储
            'app'   => [
                '$class' => '\\qpf\\log\\storage\\App',
                'enable'    => false, // 是否启用
                'level' => [
                    'warning',
                ],
            ],
            // 文件系统
            'file'  => [
                '$class' => '\\qpf\\log\\storage\\File',
                'enable'    => true, // 是否启用
                'level' => [
                    'error',
                    'info',
                ]
            ],
            // 测试日志
            'test'  => [
                '$class' => '\\qpf\\log\\storage\\Test',
                'enable'    => true, // 是否启用
                'level' => [
                    'emergency',
                    'alert',
                    'critical',
                    'error',
                    'warning',
                    'notice',
                    'info',
                    'sql',
                    'debug',
                ],
            ],
        ],
    ];
    
    /**
     * 引导
     */
    public function boot()
    {
        
    }
    
    /**
     * 注册服务
     */
    public function register()
    {
        $this->app->single('log', [
            '$class' => Log::class,
            '$options'   => $this->cionfig,
        ]);
    }
}
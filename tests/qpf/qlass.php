<?php

/**
 * 旧版对象定义数组
 * 
 * - class : 指定要创建的类
 * - 其它为对象的属性配置
 * @var array $base
 */
$base = [
    'config' => [
        'class' => 'qpf\test\Foo', // 类名
        'property1' => 'value1', // 属性设置1
        'property2' => 'value2', // 属性设置2
        // ... 更多属性设置
    ],
    
    // 构造参数示例1 - 按顺序传入构造
    'params' => [
        'value1',
        function (){
            return 'value2';
        },
        new Foo(),
    ],
];

/**
 * 新版配置,
 * 通过一个数组可描述:
 * - 要创建的类: 类名
 * - 构造参数: 创建类时, 传入构造的参数
 * - 属性初始化: 设置对象的属性
 * - 是否单例: 类实例将缓存在容器
 * @var array $new_qlass
 */
$new_qlass = [
    '$class' => 'qpf\test\Foo', // 实现类
    '$params' => [], // 构造参数
    '$single' => true, // 是否单例
    '$options' => [ // 对象属性配置
        'property1' => 'value1', // 属性设置1
        'property2' => 'value2', // 属性设置2
    ], 
];

/* 需要注意一
 * QPF::create(类名string|定义数组array $name, 构造参数 $params);
 * 
 * - 该入口的 类定义, 依然支持简洁版(仅限定类名, 其它都为属性); 
 * */
$qpf_create = [
    '$class' => 'qpf\test\Foo',
    'property1' => 'value1', // 属性设置1
    'property2' => 'value2', // 属性设置2
];

/* 需要注意二
 * 
 * - 新版类定义单数组格式, 主要用于注册到容器!
 * 
 * 也就是说, 仅仅改变容器配置数组即可!
 */
    
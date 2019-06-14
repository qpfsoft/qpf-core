<?php
/* 参考APi配置 - 单接口
 * ----------------------
 */

return [
    
    // URL别名, 必须带一个`_`, 以下都是正确的
    '__url_alias__' => [
        '_domain_'      => 'domain.com',
        'api_domain'    => 'api.domain.com',
        'qq_'           => 'qq.com',
        '_sohu'         => 'sohu.com',
    ],
    
    
    // 接口可配置一 , 简单快速配置
    'web_login'  => [
        // 接口URL地址. 不带查询参数
        'url'       => 'http://zone.domain.com/api.php',
        'param'     => [
            'user'  => [
                'set'       => true, // 必须
                'default'   => 'admin', // 默认值
            ],
            'pwd'   => [
                'set'       => true, // 必须
                'default'   => '123', // 默认值
            ],
            'charset'   => [
                'set'       => false, // 可选, 默认值未设将会被忽略
                'default'   => time(), // 默认值, 值`null`代表未设置, '' 值将会`&charset=&a..`
            ],
        ],
    ],
    
    // 示例 - 设置主机/域名部分, 展示别名的用法, 目前仅host参数支持解析别名
    'day_local' => [
        'host'  => 'day._domain_', // 解析结果 `day.domain.com`
        'host'  => 'day.api_domain', // 解析结果 `day.api.domain.com`  
        'host'  => 'qq_._sohu',  // 错误的, 但解析结果为 `qq.com.sohu.com`
    ],
    
    // 示例 - 设置返回值预处理
    'dom_local' => [
        'return_type'       => 'json', // 返回类型, 方便系统将结果转换为数组
        'return_charset'    => 'utf-8', // 结果字符集设置, 防止乱码
        'return_content'    => '{erro:1, meg: "error"}', // 不再真实请求api, 直接返回改值
    ],
    
    // 示例 - 设置api地址  
    'tes_local' => [
        'https' => false, // api协议类型, bool类型来表明
        'host'  => 'day._domain_', // api域名部分
        'path'  => '/index/login.php', // api路径部分, 以`/`开头
        // 解析结果为: http://day.domain.com/index/login.php
        
        // 可直接设置url. 来忽略以上3个参数
        'url'   => 'http://day.domain.com/index/login.php',
        
        // 分离式url参数. 是为了方便. 子接口的拼接. 和域名地址管理.
    ],
];
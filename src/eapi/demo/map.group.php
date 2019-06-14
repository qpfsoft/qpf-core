<?php
/* 参考APi配置 - 接口组
 * ----------------------
 * 群组公用https, host, path, url
 */

return [
    
    '__url_alias__' => [
        '_souhu_'  => 'sohu.com',
    ],
    
    'dom_api'   => [
        'https' => false, // 协议类型, {false:'http', true:'https'}
        'host'  => 'pv._souhu_', // api域名不带协议, 可使用别名
        'path'  => '/cityjson', // api路径, 以`/`开头
        
        'param' => [
            'token' => 's3sAdf', // 可用字符串类型, 固定参数值
            'user'  => [
                 'set'  => true, // 必须调用时必须提供, 否则抛出异常
            ],
            'type'  => [
                'set'       => false, // 可选, 有默认值不为`null`时设置
                'default'   => 'qpf', // 默认值
            ],
        ],
        
        'group' => [
            
            'one'   => [
                'host'  => 'one._souhu_', // 独有属性
                'param' => [
                    'type'  => ':type', // 映射公共参数配置
                ],
            ],
            
            'two'   => [
                'path'  => '/login', // 独有属性
                'param' => [
                    'user'  => ':user',
                    'pwd'   => [
                        'set'   => true,
                    ],
                ],
            ],
        ],
    ],
];

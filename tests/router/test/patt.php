<?php
/* 
//$pattern = '#^login/(?<type>[\w\-]+)/?(\w+)?#';
$pattern = '#^login/(?<type>[\w\-]+)/?(?:\w+)?#';
$subject = 'login/user';
preg_match($pattern, $subject, $matche);

print_r($matche); */

//从URL中获取主机名称
preg_match('/[^.]+\.[^.]+$/', "www.php.net", $matches);


print_r($matches);


$demo = [
    [
        'login/:user',
        'login/admin',
        'login/([\w\-]+)',
        'login/(?<user>[\w\-]+)',
        'q' => '1',
    ],
    [
        'login/[:user]',
        'login',
        'login/admin',
        'login/?([\w\-]+)?',
        'q' => '1',
    ],
    [
        'login/[:user]/[:pwd]',
        'login',
        'login/admin',
        'login/admin/123',
        'login/?([\w\-]+)?/?([\d]+)?',
        'q' => '1',
    ],
    
    [
        'item-<type>-<page>'
    ],
];

/*


  [
    
  ]


 */
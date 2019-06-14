<?php
/**
 * 注册目录
 * 
 * 用于访问没有命名空间的类文件, 不推荐或尽量少的注册查找目录
 */
$qpfsoft_path = dirname(dirname(dirname(__DIR__)));
$root_path = dirname(dirname($qpfsoft_path));
return [
    $root_path . '/extend',
];
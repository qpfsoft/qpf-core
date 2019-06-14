<?php
/**
 * 命名空间映射到目录
 */
$qpfsoft_path = dirname(dirname(dirname(__DIR__)));
$root_path = dirname(dirname($qpfsoft_path));
return [
    'qpf'                   => $qpfsoft_path . '/qpf-core/src',
    'qpf\\error'            => $qpfsoft_path . '/error/src',
    'qpf\\lang'             => $qpfsoft_path . '/lang/src',
    'qpf\\deunit'           => $qpfsoft_path . '/deunit/src',
    'qpf\\helper'           => $qpfsoft_path . '/helper/src',
    'qpf\\protect'          => $qpfsoft_path . '/protect/src',
    'cg'                    => $qpfsoft_path . '/qpf-cg',
    'app'                   => $root_path . '/app',
    'phtml'                 => $qpfsoft_path . '/phtml/src',
];
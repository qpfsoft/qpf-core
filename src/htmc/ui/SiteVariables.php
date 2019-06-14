<?php
namespace qpf\htmc\ui;

/**
 * 设置站点变量
 */
$var['globals']['color'] = [
    // 颜色设置
    'primaryColor'   => 'pink', //原色
    'secondaryColor' => 'grey', // 次要颜色
    // 颜色变量
    'red'       =>  '#B03060', // 红色
    'orange'    =>  '#FE9A76', // 橙子, 橙色
    'yellow'    =>  '#FFD700', // 黄色
    'olive'     =>  '#32CD32', // 橄榄色, 土黄绿
    'green'     =>  '#016936', // 绿色
    'teal'      =>  '#008080', // 天蓝色, 海洋色
    'blue'      =>  '#0E6EB8', // 蓝色
    'violet'    =>  '#EE82EE', // 紫色, 偏蓝
    'purple'    =>  '#B413EC', // 紫色, 偏红
    'pink'      =>  '#FF1493', // 粉色
    'brown'     =>  '#A52A2A', // 棕色
    'grey'      =>  '#A0A0A0', // 灰色
    'black'     =>  '#000000', // 黑色
];

/**
 * 组件
 */
$var['modules'] = [
    'checkboxActiveBackground'  =>  $var['globals']['color']['primaryColor'],
    'checkboxActiveBorderColor' =>  $var['globals']['color']['secondaryColor'],
    'checkboxActiveCheckColor'  =>  '',
];
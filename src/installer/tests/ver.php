<?php
$input = '>=5.6';

if (preg_match('/^([<|>|=]+)+([\d+].?[\d+]?.?[\d+]?)$/', $input, $result)) {
    array_shift($result);
    list($operator, $version2) = $result;
    if (version_compare(PHP_VERSION, $version2, $operator)) {
        echo '当前' . $input;
    }
} else {
    echo '输入格式错误';
}

var_dump($result);
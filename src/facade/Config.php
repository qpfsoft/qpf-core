<?php
namespace qpf\facade;

use qpf\base\Facade;

class Config extends Facade
{
    /**
     * 获得Facade绑定的类名
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'config';
    }
}
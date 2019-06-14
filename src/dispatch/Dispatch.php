<?php
namespace qpf\dispatch;

use qpf\dispatch\action\ActionBase;
use qpf\dispatch\action\ControllerAction;
use qpf\exceptions\ParameterException;
use qpf\dispatch\action\CallbackAction;
use qpf\dispatch\action\UrlAction;
use qpf\dispatch\action\ViewAction;

/**
 * 请求调度
 */
class Dispatch
{
    /**
     * 创建调度动作
     * @param mixed $action 调度操作
     * @param array $param 调度参数
     * @param string $type 调度类型
     * @return ActionBase
     */
    public static function create(array $option)
    {
        switch ($option['type']) {
            case 'controller':
                    return new ControllerAction($option['action'], $option['param']);
                break;
            case 'callback':
                    return new CallbackAction($option['action'], $option['param']);
                break;
            case 'url':
                    return new UrlAction($option['action'], $option['param']);
                break;
            case 'view':
                    return new ViewAction($option['action'], $option['param']);
                break;
            default:
                throw new ParameterException('Unknown dispatch type : `' . $option['type'] . '`');
        }
    }
}
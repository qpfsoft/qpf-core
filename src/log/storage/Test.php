<?php
namespace qpf\log\storage;

use qpf;

/**
 * 测试即将要保存的日志内容
 */
class Test extends LogStorage
{
    public function save()
    {
        $tpl = '';
        $fol = '<p>'.str_repeat('─', '35').'</p>';
        $eol = '<br>';
        
        $tpl .= $fol . $eol;
        $tpl .= '# 日志内容' . $eol;
        foreach ($this->log as $level => $list) {
            foreach ($list as $msg) {
                $tpl .= "[{$level}] " . $msg . $eol;
            }
        }
        $tpl .= $fol . $eol;
        if(QPF::app()->isDebug() && !QPF::app()->isProd()) {
            echo $tpl;
        }
    }
}
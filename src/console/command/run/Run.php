<?php
namespace qpf\console\command\run;

use qpf\console\command\Cmd;
use qpf\console\RunCmd;

/**
 * PHP运行其它程序命令
 */
class Run extends Cmd
{
    /**
     * @cmd php qpf run:run
     */
    public function run()
    {
    }
    
    /**
     * 修复cgi无法执行命令
     * @cmd php qpf run:fixcmd
     */
    public function fixcmd()
    {
        $cmd = new RunCmd();
        
        $cmd->buildCliEnv();
        
        return $cmd->getCliEnv();
    }
    
    /**
     * 加载资源包
     */
    public function asset()
    {
        
    }
}
<?php
class CmdTest
{
    /**
     * 执行一个命令
     * @param string $command 要执行的命令
     * @param array $descriptor
     * @param array $pipes
     */
    public function execute(string $command, array $descriptor, array $pipes = [])
    {
        $descriptor = [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'], // 标准输出，子进程向此管道中写入数据
        ];
        $process = proc_open($command, $descriptor, $pipes);
    }
}
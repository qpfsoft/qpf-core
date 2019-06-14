<?php
namespace qpf\console;

use qpf;
use qpf\exceptions\Exception;
use qpf\builder\template\ArrayConfigFile;

/**
 * PHP 执行控制台命令
 */
class Runcmd
{
    /**
     * 命令模板
     * @var array
     */
    public $commands = [
        'php' => 'php {arg}',
        'node' => 'node {arg}',
    ];
    
    public $info = [];
    
    /**
     * cli运行环境
     * - 用于解决cgi执行命令错误
     * @var string
     */
    public $cliEnvFile = __DIR__ . '/cli_env.php';
    
    /**
     * 运行命令
     * @param string $cmd 命令
     * @param array $arg 命令参数
     * @param string $basePath 运行目录
     * @param array $env 环境变量
     * @param array $opt 其它参数
     * @return bool
     */
    public function run($cmd, array $args = [], string $basePath = null, array $env = null, array $opt = null):bool
    {
        
        if (!empty($args)) {
            $words = array_keys($args);
            foreach ($words as &$w) {
                $w = "{{$w}}";
            }
            $cmd = str_replace($words, $args, $cmd);
        }

        $descriptor = [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $pipes = [];
        
        $process = proc_open($cmd, $descriptor, $pipes, $basePath, $env, $opt);
        $stdout = stream_get_contents($pipes[1]); // 输出信息
        $stderr = stream_get_contents($pipes[2]);// 错误信息
        
        $msg = !empty($stderr) ? $stderr : $stdout;
        
        $this->info[] = [
            'cmd' => $cmd,
            'msg' => $this->characet($msg),
        ];
        
        foreach ($pipes as $pipe) {
            fclose($pipe);
        }
        $status = proc_close($process);
        
        if ($status === 0) {
            QPF::debug('Command fails ' . $cmd);
        } elseif (QPF::$app->isDebug()) {
            throw new Exception('Command fails ' . $cmd);
        } else {
            QPF::error('Command fails ' . $cmd);
        }
        
        return $status === 0;
    }
    
    /**
     * 修复关键字
     * 
     * - 修复win平台下使用绝对路径调用命令
     * @param string $cmd
     * @return string
     */
    public function fixKeyword(string $cmd):string
    {
        if ($this->isWin() && $this->isWinPath($cmd)) {
            return sprintf('cd %s && %s', escapeshellarg(dirname($cmd)), basename($cmd));
        }
        
        return $cmd;
    }
    
    /**
     * 生成Cli运行环境
     */
    public function buildCliEnv()
    {
        // 控制台模式生成, 生成控制台的环境变量, 方便非控制台模式使用!
        if (PHP_SAPI == 'cli') {
            $file = new ArrayConfigFile();
            $file->setConfig(getenv());
            file_put_contents($this->cliEnvFile, $file->getContent());
        }
    }
    
    /**
     * 获取cli环境变量
     * @return array
     */
    public function getCliEnv(): array
    {
        if (PHP_SAPI == 'cli') {
            return getenv();
        } elseif (is_file($this->cliEnvFile)) {
            return include $this->cliEnvFile;
        } else {
            throw new Exception('Need to execute this command in the console: `php qpf run:fixcmd`');
        }
    }
    
    /**
     * 是否win系统平台
     * @return bool
     */
    public function isWin(): bool
    {
        static $status;
        
        if ($status === null) {
            $status = strtolower(substr(PHP_OS, 0, 3)) === 'win';
        }
        
        return $status;
    }
    
    /**
     * 检查路径是否win系统路径
     * @param string $path
     * @return bool
     */
    public function isWinPath(string $path):bool
    {
        return strpos(substr($path, 0, 3), ':') !== false;
    }
    
    /**
     * 字符集转换为UTF-8
     * @param string $data
     * @return string
     */
    public function characet(string $data): string
    {
        if(!empty($data)){
            $type = mb_detect_encoding($data, ['UTF-8', 'GBK', 'GB2312']);
            if($type != 'UTF-8'){
                $data = mb_convert_encoding($data ,'UTF-8' , $type);
            }
        }
        return $data;
    }
}
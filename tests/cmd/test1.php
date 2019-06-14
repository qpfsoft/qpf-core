<?php
$cmd = 'C:\\Program Files\\nodejs\\node.exe';
if (strtolower(substr(PHP_OS,0,3)) === 'win') {
    $cmd = sprintf('cd %s && %s', escapeshellarg(dirname($cmd)), basename($cmd));
}

$filePath = __DIR__ . '/tmp';
if (!is_dir($filePath)) {
    mkdir($filePath, 0755);
}

$descriptorspec = array(
    0 => array("pipe", "r"),  // 标准输入，子进程从此管道中读取数据
    1 => array("pipe", "w"),  // 标准输出，子进程向此管道中写入数据
    2 => array("file", $filePath . "/error-output.txt", "a") // 标准错误，写入到一个文件
);



$pipes = [];
$cwd = 'C:\phpStudy\PHPTutorial\php\php-7.2.1-nts'; // 命令行路径
$env = array('some_option' => 'aeiou');

$process = proc_open('php -v', $descriptorspec, $pipes, $cwd);

if (is_resource($process)) {
    // $pipes 现在看起来是这样的：
    // 0 => 可以向子进程标准输入写入的句柄
    // 1 => 可以从子进程标准输出读取的句柄
    // 错误输出将被追加到文件 /tmp/error-output.txt
    
    fwrite($pipes[0], '<?php echo "ok"; ?>');
    fclose($pipes[0]);
    
    echo stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    
    
    // 切记：在调用 proc_close 之前关闭所有的管道以避免死锁。
    $return_value = proc_close($process);
    
    echo "\n命令返回 $return_value\n";
}
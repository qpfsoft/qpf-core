<?php
/**
 * 当前脚本在Cli 环境调用, 才能正确的调用其它命令. 否则环境异常!
 * @var array $commands
 */

$commands = [
    'php' => 'php {arg}',
    'node' => 'C:\Program Files\nodejs\node.exe {arg},',
    'npm'   => 'npm {arg}',
];


/**
 * 运行命令
 * @param unknown $cmd
 * @param unknown $arg
 * @param unknown $basePath
 * @return boolean
 */
function runCmd($cmd, $arg, $basePath, $env = null)
{
    if (strtolower(substr(PHP_OS,0,3)) === 'win' && strpos(substr($cmd, 0, 3), ':') !== false) {
        $cmd = sprintf('cd %s && %s', escapeshellarg(dirname($cmd)), basename($cmd));
    }
    
    $cmd = strtr($cmd, [
        '{arg}' => escapeshellarg($arg),
    ]);
    
    $descriptor = [
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];
    $pipes = [];
    
    $proc = proc_open($cmd, $descriptor, $pipes, $basePath, getenv());
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);

    if (!empty($stderr)) {
        var_export('> ERROR: ' . iconv('GB2312', 'utf-8', $stderr) . PHP_EOL);
    } else {
        var_export('> ' . $stdout . PHP_EOL);
    }
    
    foreach ($pipes as $pipe) {
        fclose($pipe);
    }
    $status = proc_close($proc);
    
    if ($status === 0) {
        var_export('命令执行成功!' . PHP_EOL);
    } else {
        var_export("`$cmd` 命令执行失败!");
    }
    
    return $status === 0;
}

runCmd($commands['npm'], '-v', __DIR__, [
    'ALLUSERSPROFILE' => 'C:\\ProgramData',
    'APPDATA' => 'C:\\Users\\QPF-Y410P\\AppData\\Roaming',
    'asl_log' => 'Destination=file',
    'CommonProgramFiles' => 'C:\\Program Files (x86)\\Common Files',
    'CommonProgramFiles(x86)' => 'C:\\Program Files (x86)\\Common Files',
    'CommonProgramW6432' => 'C:\\Program Files\\Common Files',
    'COMPUTERNAME' => 'DESKTOP-E0RC0V0',
    'ComSpec' => 'C:\\Windows\\system32\\cmd.exe',
    'DriverData' => 'C:\\Windows\\System32\\Drivers\\DriverData',
    'HOMEDRIVE' => 'C:',
    'HOMEPATH' => '\\Users\\QPF-Y410P',
    'LOCALAPPDATA' => 'C:\\Users\\QPF-Y410P\\AppData\\Local',
    'LOGONSERVER' => '\\\\DESKTOP-E0RC0V0',
    'NUMBER_OF_PROCESSORS' => '8',
    'OS' => 'Windows_NT',
    'Path' => 'C:\\Python27\\;C:\\Python27\\Scripts;C:\\Windows\\system32;C:\\Windows;C:\\Windows\\System32\\Wbem;C:\\Windows\\System32\\WindowsPowerShell\\v1.0\\;C:\\Windows\\System32\\OpenSSH\\;C:\\Program Files (x86)\\Common Files\\Thunder Network\\KanKan\\Codecs;C:\\Program Files (x86)\\NVIDIA Corporation\\PhysX\\Common;j:\\Program Files\\IDM Computer Solutions\\UltraEdit;C:\\Program Files\\NVIDIA Corporation\\NVIDIA NvDLISR;C:\\phpStudy\\PHPTutorial\\php\\php-7.2.1-nts;j:\\composer;J:\\Program Files\\Redis\\;J:\\Program Files\\TortoiseGit\\bin;j:\\Program Files\\Git\\cmd;C:\\Program Files\\nodejs\\;C:\\Users\\QPF-Y410P\\AppData\\Local\\Microsoft\\WindowsApps;J:\\Microsoft VS Code\\bin;C:\\Users\\QPF-Y410P\\AppData\\Roaming\\Composer\\vendor\\bin;D:\\Program Files\\Microsoft VS Code\\bin;C:\\Users\\QPF-Y410P\\AppData\\Roaming\\npm',
    'PATHEXT' => '.COM;.EXE;.BAT;.CMD;.VBS;.VBE;.JS;.JSE;.WSF;.WSH;.MSC;.CPL',
    'PROCESSOR_ARCHITECTURE' => 'x86',
    'PROCESSOR_ARCHITEW6432' => 'AMD64',
    'PROCESSOR_IDENTIFIER' => 'Intel64 Family 6 Model 60 Stepping 3, GenuineIntel',
    'PROCESSOR_LEVEL' => '6',
    'PROCESSOR_REVISION' => '3c03',
    'ProgramData' => 'C:\\ProgramData',
    'ProgramFiles' => 'C:\\Program Files (x86)',
    'ProgramFiles(x86)' => 'C:\\Program Files (x86)',
    'ProgramW6432' => 'C:\\Program Files',
    'PSModulePath' => 'K:\\ĵ\\WindowsPowerShell\\Modules;C:\\Program Files\\WindowsPowerShell\\Modules;C:\\Windows\\system32\\WindowsPowerShell\\v1.0\\Modules',
    'PUBLIC' => 'C:\\Users\\Public',
    'SystemDrive' => 'C:',
    'SystemRoot' => 'C:\\Windows',
    'TEMP' => 'C:\\Users\\QPF-Y4~1\\AppData\\Local\\Temp',
    'TMP' => 'C:\\Users\\QPF-Y4~1\\AppData\\Local\\Temp',
    'USERDOMAIN' => 'DESKTOP-E0RC0V0',
    'USERDOMAIN_ROAMINGPROFILE' => 'DESKTOP-E0RC0V0',
    'USERNAME' => 'QPF-Y410P',
    'USERPROFILE' => 'C:\\Users\\QPF-Y410P',
    'windir' => 'C:\\Windows',
]);
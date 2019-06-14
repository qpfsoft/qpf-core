<?php
namespace qpf\browser\kernel;

/**
 * 用户代理 - 游览器标识生成器
 */
class UserAgent
{
    /**
     * 真机用户代理
     * @var array
     */
    private $trueUserAgent = [
        '360'       => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36 QIHU 360SE',
        'chrome'    => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.80 Safari/537.36',
        'opera'     => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36 OPR/54.0.2952.64 (Edition B2)',
        'edge'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 Safari/537.36 Edge/17.17134',
        'firefox'   => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:63.0) Gecko/20100101 Firefox/63.0',
    ];
    /**
     * 用户操作系统平台
     * @var array
     */
    private $partOS = [
        'win'   => [
            'win2000'   => 'Windows NT 5.0',
            'winxp'     => 'Windows NT 5.1',
            'winvista'  => 'Windows NT 6.0',
            'win7'      => 'Windows NT 6.1',
            'win8'      => 'Windows NT 6.2',
            'win8.1'    => 'Windows NT 6.3',
            'win10'     => 'Windows NT 10.0',
        ],
        'linux' => [
            'X11; Linux i686', // Linux 桌面，i686 版本
            'X11; Linux x86_64', // Linux 桌面，x86_64 版本
            'X11; Linux i686 on x86_64', // Linux 桌面，运行在 x86_64 的 i686 版本
        ],
        'mac'  => [
            'Macintosh; Intel Mac OS X 10_9_0', // Intel x86 或者 x86_64
            'Macintosh; PPC Mac OS X 10_9_0',  // PowerPC 处理器
            'Macintosh; Intel Mac OS X 10.12;' // 不用下划线，用点
        ]
    ];

    /**
     * 360游览器用户代理
     * @param bool $rand 是否随机生成, 默认`false`
     * @return string
     */
    public function getUA360($rand = false)
    {
        return $rand ? $this->randAU('win', '360') : $this->trueUserAgent['360'];
    }
    
    /**
     * chrome游览器用户代理
     * @param bool $rand 是否随机生成, 默认`false`
     * @return string
     */
    public function getUAchrome($rand = false)
    {
        return $rand ? $this->randAU($this->randArray(['win', 'linux', 'mac']), 'chrome') : $this->trueUserAgent['chrome'];
    }
    
    /**
     * opera游览器用户代理
     * @param bool $rand 是否随机生成, 默认`false`
     * @return string
     */
    public function getUAopera($rand = false)
    {
        return $rand ? $this->randAU($this->randArray(['win', 'linux', 'mac']), 'opera') : $this->trueUserAgent['opera'];
    }
    
    /**
     * edge游览器用户代理
     * @param bool $rand 是否随机生成, 默认`false`
     * @return string
     */
    public function getUAedge($rand = false)
    {
        return rand ? $this->randAU('win10', 'edge') : $this->trueUserAgent['edge'];
    }
    
    /**
     * firefox游览器用户代理
     * @param bool $rand 是否随机生成, 默认`false`
     * @return string
     */
    public function getUAfirefox($rand = false)
    {
        return $rand ? $this->randAU($this->randArray(['win', 'linux', 'mac']), 'firefox') : $this->trueUserAgent['firefox'];
    }
    
    /**
     * 随机生成AU
     * @param string $os 系统平台, `win[2000|xp|7|8|8.1|10], linux, mac`
     * @param string $browser 游览器类型, `chrome, 360, edge, opera, firefox`
     */
    public function randAU($os, $browser)
    {
        $ua = ['Mozilla/5.0'];
        
        // 系统平台
        $os = $this->getPartOS($os);
        if ($browser == 'firefox') {
            $firefox_ver = $this->buildFirefoxVersion(33, 63);
            $os .= '; rv:' . $firefox_ver;
        }
        $ua[] = '(' . $os . ')';
        
        // 引擎内核
        if($browser == 'firefox') {
            $ua[] = 'Gecko/20100101';
        } else {
            $ua[] = 'AppleWebKit/537.36 (KHTML, like Gecko)';
        }
        
        // 游览器版本
        if($browser == 'firefox') {
            $ua[] = 'Firefox/' . $firefox_ver;
        } else {
            // chrome
            $chrome = 'Chrome/' . $this->buildBrowserVersion(55, 71) . ' Safari/537.36';
            
            switch ($browser) {
                case 'opera':
                    $ua[] = $chrome . ' OPR/' . $this->buildBrowserVersion(50, 58) . ' (Edition B2)';
                    break;
                case '360':
                    $ua[] = $chrome . ' QIHU 360SE';
                    break;
                case 'edge':
                    $ua[] = $chrome . ' Edge/' . $this->randInt(12, 17) . '.' . $this->randInt(10000, 17134);
                    break;
                default:
                    $ua[] = $chrome;
            }
        }
        
        return implode(' ', $ua);
    }
    
    /**
     * 获取随机的用户代理
     * @return string
     */
    public function getUA()
    {
        // 游览器类型, 支持平台
        $pool = [
            ['chrome', ['win', 'linux', 'mac']],
            ['opera', ['win', 'linux', 'mac']],
            ['firefox', ['win', 'linux', 'mac']],
            ['360', 'win'],
            ['edge', 'win10'],
        ];
       
        list($browser, $os) = $this->randArray($pool);
        $os = is_array($os) ? $this->randArray($os) : $os;
        
        return $this->randAU($os, $browser);
    }
    
    /**
     * 生成游览器版本 - xx.0.xxxx.xxx
     * @param int $min 主版本号
     * @param int $max 主版本号
     * @return string
     */
    private function buildBrowserVersion($min, $max)
    {
        // 主版本号
        $code = $this->randInt($min, $max); // 2位
        $code .= '.0'; // 默认0
        $code .= '.' . $this->randInt(100, 500); // 3位
        $code .= '.' . $this->randInt(50, 300); // 2~3位
        return $code;
    }
    
    /**
     * 生成火狐游览器版本 - xx.0.x
     * @param int $min 主版本号, 最小
     * @param int $max 主版本号, 最大
     */
    private function buildFirefoxVersion($min, $max)
    {
        $code = $this->randInt($min, $max);
        $code .= '.0';
        return $code;
    }
    
    /**
     * 获取指定系统信息
     * @param string $os 平台类型`win[2000|xp|7|8|8.1|10], linux, mac`
     * @return string|mixed
     */
    private function getPartOS($os)
    {
        if(strpos($os, 'win') === 0) {
            // 随机
            if($os == 'win') {
                $result = $this->randArray($this->partOS[$os]);
            }
            
            // 指定win系统
            if(isset($this->partOS['win'][$os])) {
                $result = $this->partOS['win'][$os];
            }
            
            // 支持x64
            if(!in_array($os, ['winxp', 'win2000'])) {
                $result .= '; '. $this->randArray(['Win64; x64', 'WOW64']);
            }
        } elseif (isset($this->partOS[$os])) {
            $result = $this->randArray($this->partOS[$os]);
        }
        
        return isset($result) ? $result : '';
    }
    
    /**
     * 随机生成整数
     * @param int $min
     * @param int $max
     * @return int
     */
    public function randInt($min, $max)
    {
        return mt_rand($min, $max);
    }
    
    /**
     * 随机返回数组中的一个值
     * @param array $array
     * @return mixed
     */
    public function randArray(array $array)
    {
        $array = array_values($array);
        return $array[mt_rand(0, count($array) - 1)];
    }
    
    /**
     * 随机生成字符串
     * @param int $length 长度
     * @return string
     */
    public function randString($length = 8)
    {
        $chars = 'bcdfghjklmnprstvwxzaeiou';
        
        for ($p = 0; $p < $length; $p++) {
            $result .= ($p%2) ? $chars[mt_rand(19, 23)] : $chars[mt_rand(0, 18)];
        }
        
        return $result;
    }
}
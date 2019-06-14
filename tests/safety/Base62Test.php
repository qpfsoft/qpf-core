<?php
use qpf\deunit\TestUnit;
use qpf\safety\Base62;
use qpf\func\StringFunc;

include __DIR__ . '/../boot.php';

class Base62Test extends TestUnit
{
    public function testBase1()
    {
        $data = 'asdfghjklqwertyuiopzxcvbnm';
        $base62_arr = Base62::encode($data);
        $base62_str = join('', $base62_arr);
        return [
            $base62_arr,
            $base62_str,
            strlen($base62_str),
        ];
    }
    
    public function base64($data, $key = 'qpf')
    {
        return base64_encode($data); // md5($data . $key)
    }
    
    public function testBase64()
    {
        $data = 'asdfghjklqwertyuiopzxcvbnm';
        $base64 = $this->base64($data);
        
        return [
            $base64,
            StringFunc::base64UrlEncode($data),
        ];
    }
}

echor(Base62Test::runTestUnit());
<?php 
use qpf\deunit\TestUnit;
use qpf\safety\AuthCode;
use qpf\autoload\Autoload;

include __DIR__ . '/../boot.php';

/**
 * 授权码验证测试
 */
class AuthCodeTest extends TestUnit
{
    public function testBase()
    {
        $data = 'qpf new ok!qpf new ok!qpf new ok!qpf new ok!qpf new ok!';
        
        $code = AuthCode::encode($data);

        return [
            'encode' => $code,
            'length' => strlen($code),
            'decode' => AuthCode::decode($code),
        ];
    }
    
    public function testDecode()
    {
        $encode = '3c74qvmnDr9Tez6ABLDPZ8ZiVgGVe2oDuEzO9CL6vWvNJdQzkXNxBg';
        return AuthCode::decode($encode);
    }
    
    public function testGroup()
    {
        $encode = '3c74qvmnDr9Tez6ABLDPZ8ZiVgGVe2oDuEzO9CL6vWvNJdQzkXNxBg';
        $query = AuthCode::groupEnode($encode);
        $decode = AuthCode::groupDecode($query);
        return [
            $query,
            $decode,
            AuthCode::decode($decode),
        ];
    }
    
}

echor(AuthCodeTest::runTestUnit());
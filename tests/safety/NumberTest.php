<?php
use qpf\deunit\TestUnit;
use qpf\safety\Number;

include __DIR__ . '/../boot.php';


/**
 * 将数字编码为短字符串, 可解码
 *
 */
class NumberTest extends TestUnit
{
    public $num;
    
    public function setUp()
    {
        QPF::app()->init();
    }
    
    public function testBase()
    {
        $code = Number::secureCodes();
        return [strlen($code), $code];
    }
    
    public function testEncodeNumber()
    {
        $number = 12234;
        $encode = Number::encode($number);
        return [
            'number'    => $number,
            'encode'    => Number::encode($number),
            'decode'    => Number::dencode($encode),
        ];
    }
    
}

echor(NumberTest::runTestUnit());
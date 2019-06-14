<?php
use qpf\deunit\TestUnit;
use qpf\safety\QueryGroup;

include __DIR__ .'/../boot.php';

/**
 * 查询参数分组
 */
class QueryGroupTest extends TestUnit
{
    public function testBase()
    {
        $qg = new QueryGroup();
        
        $data = '3c74qvmnDr9Tez6ABLDPZ8ZiVgGVe2oDuEzO9CL6vWvNJdQzkXNxBg';
        $encode = $qg->split($data);
        parse_str($encode, $get);
        $decode = $qg->merge($get);
        return [
            $data,
            $encode,
            $decode,
        ];
    }
    
    public function testBase2()
    {
        $qg = new QueryGroup();
        
        $data = '3c74qvmnDr9Tez6ABLDPZ8ZiVgGVe2oDuEzO9CL6vWvNJdQzkXNxBg';
        $encode = $qg->splitAvg($data, 'any');
        parse_str($encode, $get);
        $decode = $qg->merge($get, 'any');
        return [
            $data,
            $encode,
            $decode,
        ];
    }
}

echor(QueryGroupTest::runTestUnit());
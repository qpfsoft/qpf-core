<?php
namespace qpf\browser\kernel;

/**
 * Curl 异常
 */
class CurlException extends \Exception
{
    public function getName()
    {
        return 'Curl Service';
    }
}
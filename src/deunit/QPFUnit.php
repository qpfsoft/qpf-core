<?php
namespace qpf\deunit;

class QPFUnit
{
    public static $QPF_PATH;
    public static $QPFSOFT_PATH;
    public static $VENDOR_PATH;
    public static $ROOT_PATH;
    public static $WEB_PATH;
}

QPFUnit::$QPF_PATH = dirname(__DIR__);
if (substr(QPFUnit::$QPF_PATH, -3) == 'src') {
    QPFUnit::$QPFSOFT_PATH = dirname(dirname(QPFUnit::$QPF_PATH));
} else {
    QPFUnit::$QPFSOFT_PATH = dirname(QPFUnit::$QPF_PATH);
}
QPFUnit::$VENDOR_PATH = dirname(QPFUnit::$QPFSOFT_PATH);
QPFUnit::$ROOT_PATH = dirname(QPFUnit::$VENDOR_PATH);
QPFUnit::$WEB_PATH = QPFUnit::$ROOT_PATH . DIRECTORY_SEPARATOR . 'web';
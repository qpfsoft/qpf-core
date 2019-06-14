<?php
use qpf\deunit\TestUnit;
use qpf\assets\QPFAsset;

include __DIR__ . '/../boot.php';

class QPFAssetTest extends TestUnit
{
    public function testBase()
    {
        QPF::app()->init();
        $asset = new QPFAsset();

        echor( $asset->getJsAll($asset->srcPath .'/js', $asset->srcPath) );
    }
}

echor(QPFAssetTest::runTestUnit());
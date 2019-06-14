<?php
use qpf\deunit\TestUnit;
use qpf\assets\Asset;

include __DIR__ . '/../boot.php';

class AssetTest extends TestUnit
{
    public function testBase()
    {
        $asset = new Asset([
            'src' => __DIR__ . '/src',
            'dst' => __DIR__ . '/dst',
            'file' => 'style.css',
            'type' => 'css',
        ]);

        return $asset->getPath();
    }
}

echor(AssetTest::runTestUnit());
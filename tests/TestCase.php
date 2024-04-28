<?php

namespace Attestto\SolanaPhpSdk\Tests;

use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public $config;

    public function setUp(): void
    {
        $jsonFilePath = dirname(__DIR__) . '/src/Programs/SNS/Constants/config.json';
       $this->config = json_decode(file_get_contents($jsonFilePath), true);
    }
}

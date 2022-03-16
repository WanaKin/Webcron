<?php

namespace Tests\Feature;

use Hamcrest\Util;
use WanaKin\Webcron\WebcronServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Util::registerGlobalFunctions();
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('cache.driver', 'array');

        $app['config']->set('webcron', include __DIR__ . '/../../config/webcron.php');
    }

    protected function getPackageProviders($app)
    {
        return [
            WebcronServiceProvider::class
        ];
    }
}
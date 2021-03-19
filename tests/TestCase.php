<?php

namespace TCGunel\XmlAligner\Tests;

use TCGunel\XmlAligner\XmlAlignerServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public $faker;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create("tr_TR");
    }

    protected function getPackageProviders($app): array
    {
        return [
            XmlAlignerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
    }


}

<?php

namespace TCGunel\XmlAligner;

use Illuminate\Support\ServiceProvider;

class XmlAlignerServiceProvider extends ServiceProvider
{
    /**
     * Publishes configuration file.
     *
     * @return  void
     */
    public function boot()
    {
    }

    /**
     * Make config publishment optional by merging the config from the package.
     *
     * @return  void
     */
    public function register()
    {
        $this->app->bind('xmlAligner', function($app) {
            return new XmlAligner();
        });
    }
}

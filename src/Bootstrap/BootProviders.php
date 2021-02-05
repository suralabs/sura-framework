<?php

namespace Sura\Bootstrap;

use Sura\Application;

class BootProviders
{
    /**
     * Bootstrap the given application.
     *
     * @param  \Sura\Application  $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        $app->boot();
    }
}
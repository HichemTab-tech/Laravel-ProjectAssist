<?php

use Illuminate\Foundation\Application;

class Repository
{
    public Application $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}

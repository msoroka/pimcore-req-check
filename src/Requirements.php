<?php

namespace App;

use App\Requirements\Apps;
use App\Requirements\Database;
use App\Requirements\Extensions;

class Requirements
{
    /**
     * Requirements constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function checkPHP()
    {
        return Extensions::check();
    }

    /**
     * @return array
     */
    public function checkExternalApps()
    {
        return Apps::check();
    }

    /**
     * @param  array  $config
     * @return array
     */
    public function checkDatabase(array $config)
    {
        return Database::check($config);
    }
}
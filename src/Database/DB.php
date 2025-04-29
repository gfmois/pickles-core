<?php

namespace Pickles\Database;

use Pickles\Kernel;

class DB
{
    public static function statement($query, array $bind =  [])
    {
        return db()->statement($query, $bind);
    }
}

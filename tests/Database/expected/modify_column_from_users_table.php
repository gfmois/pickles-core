<?php

use Pickles\Database\DB;
use Pickles\Database\Migrations\Migration;

return new class () implements Migration {
    public function up()
    {
        DB::statement('ALTER TABLE users');
    }

    public function down()
    {
        DB::statement('ALTER TABLE users');
    }
};

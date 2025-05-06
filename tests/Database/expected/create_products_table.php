<?php

use Pickles\Database\DB;
use Pickles\Database\Migrations\Migration;

return new class () implements Migration {
    public function up()
    {
        DB::statement('CREATE TABLE products (
            id INT AUTO_INCREMENT PRIMARY KEY
        );');
    }

    public function down()
    {
        DB::statement('DROP TABLE products;');
    }
};

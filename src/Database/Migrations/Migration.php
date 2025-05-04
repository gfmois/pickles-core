<?php

namespace Pickles\Database\Migrations;

interface Migration
{
    public function up();
    public function down();
}

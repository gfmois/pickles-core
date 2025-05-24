<?php

namespace Pickles\Database\Migrations;

/**
 * Interface Migration
 *
 * Defines the contract for database migration classes.
 * Implementing classes should provide methods to apply and revert database schema changes.
 */
interface Migration
{
    public function up();
    public function down();
}

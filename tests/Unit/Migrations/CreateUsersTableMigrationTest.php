<?php

namespace Tests\Unit\Migrations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CreateUsersTableMigrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @testdox It should test users table has expected columns
     * @return void
     */
    public function usersTableHasExpectedColumns()
    {
        // Run the migration
        $this->artisan('migrate');

        // Check if the columns exist in the users table
        $this->assertTrue(Schema::hasColumns('users', [
            'prefixname',
            'firstname',
            'middlename',
            'lastname',
            'suffixname',
            'username',
            'email',
            'password',
            'photo',
            'type',
            'created_at',
            'updated_at',
            'deleted_at',
        ]));
    }
}

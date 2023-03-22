<?php

namespace Tests\Unit\Migrations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CreateDetailsTableMigrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @testdox It should test details table has expected columns
     * @return void
     */
    public function detailsTableHasExpectedColumns()
    {
        // Run the migration
        $this->artisan('migrate');

        // Check if the columns exist in the details table
        $this->assertTrue(Schema::hasColumns('details', [
            'key',
            'value',
            'icon',
            'status',
            'type',
            'user_id',
            'created_at',
            'updated_at',
        ]));
    }
}

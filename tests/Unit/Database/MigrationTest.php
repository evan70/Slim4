<?php

namespace Tests\Unit\Database;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\QueryException;

class MigrationTest extends TestCase
{
    public function test_users_table_has_required_columns(): void
    {
        $columns = Capsule::schema()->getColumnListing('users');
        
        $this->assertContains('id', $columns);
        $this->assertContains('name', $columns);
        $this->assertContains('email', $columns);
        $this->assertContains('password', $columns);
        $this->assertContains('remember_token', $columns);
        $this->assertContains('created_at', $columns);
        $this->assertContains('updated_at', $columns);
    }

    public function test_email_column_is_unique(): void
    {
        $this->expectException(QueryException::class);
        
        User::create([
            'name' => 'Test User 1',
            'email' => 'duplicate@example.com',
            'password' => 'password'
        ]);

        User::create([
            'name' => 'Test User 2',
            'email' => 'duplicate@example.com',
            'password' => 'password'
        ]);
    }
}

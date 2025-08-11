<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up()
    {
        // Create admin role
        $adminRole = Role::create(['name' => 'admin']);

        // Create admin user
        DB::table('users')->insert([
            'name' => 'Administrator',
            'email' => 'admin@suratkuasa.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign admin role to user
        $adminUser = \App\Models\User::where('email', 'admin@suratkuasa.com')->first();
        $adminUser->assignRole('admin');
    }

    public function down()
    {
        DB::table('users')->where('email', 'admin@suratkuasa.com')->delete();
        Role::where('name', 'admin')->delete();
    }
};
